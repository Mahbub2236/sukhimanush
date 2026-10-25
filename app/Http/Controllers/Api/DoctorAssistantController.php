<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ChamberQueue;
use App\Models\Doctor;
use App\Models\DoctorAssistant;
use App\Services\SmsNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class DoctorAssistantController extends Controller
{
    /**
     * Staff / Compounder Login via Mobile Number & Access PIN.
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => 'required|string',
            'access_pin' => 'required|string',
        ]);

        $cleanPhone = preg_replace('/[^0-9+]/', '', $data['phone']);
        $rateKey = 'staff_login:'.$cleanPhone.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);

            return response()->json([
                'success' => false,
                'message' => "অতিরিক্ত ভুল পিন দেওয়ার কারণে অ্যাকাউন্টটি সাময়িকভাবে লক করা হয়েছে। {$seconds} সেকেন্ড পর চেষ্টা করুন। (Too many failed PIN attempts. Locked out for security.)",
            ], 429);
        }

        $assistant = DoctorAssistant::with(['doctor.user'])
            ->where(function ($q) use ($data, $cleanPhone) {
                $q->where('phone', $data['phone'])
                    ->orWhere('phone', $cleanPhone)
                    ->orWhere('phone', 'like', '%'.substr($cleanPhone, -10));
            })
            ->where('is_active', true)
            ->first();

        if (! $assistant || (! Hash::check(trim($data['access_pin']), $assistant->access_pin) && $assistant->access_pin !== trim($data['access_pin']))) {
            RateLimiter::hit($rateKey, 900); // 15 minute lockout

            return response()->json([
                'success' => false,
                'message' => 'ভুল মোবাইল নম্বর অথবা অ্যাক্সেস পিন! অনুগ্রহ করে সঠিক তথ্য দিয়ে পুনরায় চেষ্টা করুন।',
            ], 401);
        }

        RateLimiter::clear($rateKey);

        // Auto-upgrade legacy plaintext pin to bcrypt hash
        if ($assistant->access_pin === trim($data['access_pin']) && ! Str::startsWith($assistant->access_pin, '$2y$')) {
            $assistant->update(['access_pin' => Hash::make(trim($data['access_pin']))]);
        }

        $doctor = $assistant->doctor;
        $token = $assistant->createToken('staff_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => "স্বাগতম {$assistant->name}! চেম্বার সহকারী পোর্টালে লগইন সম্পন্ন হয়েছে।",
            'user' => [
                'id' => $assistant->id,
                'name' => $assistant->name,
                'phone' => $assistant->phone,
                'email' => $assistant->email ?? ($assistant->phone.'@staff.sukhimanush.local'),
                'role' => 'staff',
                'assistant_id' => $assistant->id,
                'doctor_id' => $assistant->doctor_id,
            ],
            'assistant' => $assistant,
            'doctor' => $doctor,
            'token' => $token,
        ]);
    }

    /**
     * Get staff dashboard: doctor info, today queue metrics, and appointments list.
     */
    public function dashboard(int $assistantId, Request $request): JsonResponse
    {
        $assistant = DoctorAssistant::with('doctor')->findOrFail($assistantId);
        $authUser = $request->user();
        $isAdmin = in_array($authUser->role ?? '', ['admin', 'super_admin'], true);

        if (! $isAdmin) {
            if ($authUser instanceof DoctorAssistant && $authUser->id !== $assistant->id) {
                return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 403);
            }
            if ($authUser && $authUser->role === 'doctor') {
                $doc = Doctor::where('user_id', $authUser->id)->first();
                if ($doc && $doc->id !== $assistant->doctor_id) {
                    return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 403);
                }
            }
        }

        $doctor = $assistant->doctor;
        $date = $request->input('date', now()->format('Y-m-d'));

        $appointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $date)
            ->with(['patient', 'prescription'])
            ->orderBy('serial_number')
            ->get();

        $totalSerials = $appointments->count();
        $completedCount = $appointments->where('status', 'completed')->count();
        $calledApt = $appointments->where('is_called', true)->where('status', '!=', 'completed')->last();
        $currentSerial = $calledApt ? $calledApt->serial_number : ($completedCount > 0 ? $completedCount : ($totalSerials > 0 ? 1 : 0));
        $waitingCount = max(0, $totalSerials - $completedCount);

        $queue = ChamberQueue::where('doctor_id', $doctor->id)->whereDate('queue_date', $date)->first();

        return response()->json([
            'success' => true,
            'assistant' => $assistant,
            'doctor' => $doctor,
            'date' => $date,
            'stats' => [
                'total_serials' => $totalSerials,
                'completed_count' => $completedCount,
                'waiting_count' => $waitingCount,
                'current_serving_serial' => $currentSerial,
                'called_patient_name' => $calledApt ? $calledApt->patient_name : null,
                'is_emergency_active' => $queue ? (bool) $queue->is_emergency_active : false,
                'emergency_reason' => $queue ? $queue->emergency_reason : null,
                'emergency_patient_name' => $queue ? $queue->emergency_patient_name : null,
            ],
            'appointments' => $appointments,
        ]);
    }

    /**
     * Assistant advances queue (Call Next Patient).
     */
    public function callNext(int $assistantId, Request $request): JsonResponse
    {
        $assistant = DoctorAssistant::findOrFail($assistantId);
        $authUser = $request->user();
        $isAdmin = in_array($authUser->role ?? '', ['admin', 'super_admin'], true);

        if (! $isAdmin) {
            if ($authUser instanceof DoctorAssistant && $authUser->id !== $assistant->id) {
                return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 403);
            }
            if ($authUser && $authUser->role === 'doctor') {
                $doc = Doctor::where('user_id', $authUser->id)->first();
                if ($doc && $doc->id !== $assistant->doctor_id) {
                    return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 403);
                }
            }
        }

        $doctorId = $assistant->doctor_id;
        $date = $request->input('date', now()->format('Y-m-d'));

        $nextApt = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->where(function ($q) {
                $q->where('is_called', false)
                    ->orWhereNull('is_called');
            })
            ->orderBy('serial_number')
            ->first();

        // If all uncalled are done, grab the earliest pending/confirmed
        if (! $nextApt) {
            $nextApt = Appointment::where('doctor_id', $doctorId)
                ->whereDate('appointment_date', $date)
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->orderBy('serial_number')
                ->first();
        }

        if (! $nextApt) {
            return response()->json([
                'success' => false,
                'message' => 'আজকের সব সিরিয়াল সম্পন্ন হয়েছে অথবা কোনো অপেক্ষমাণ রোগী নেই।',
            ]);
        }

        Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->update(['is_called' => false]);

        $nextApt->is_called = true;
        if ($nextApt->status === 'pending') {
            $nextApt->status = 'confirmed';
        }
        $nextApt->save();

        // 1. Send immediate SMS alert to called patient
        $smsService = app(SmsNotificationService::class);
        $smsService->sendQueueCallAlert($nextApt->load('doctor'));

        // 2. Alert the patient who is 3 positions away so they are ready in waiting room
        $upcomingApt = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->where('serial_number', '>', $nextApt->serial_number)
            ->orderBy('serial_number')
            ->skip(2)
            ->first();

        if ($upcomingApt) {
            $smsService->sendUpcomingQueueAlert($upcomingApt->load('doctor'), 3);
        }

        return response()->json([
            'success' => true,
            'message' => "📢 সিরিয়াল #{$nextApt->serial_number} ({$nextApt->patient_name})-কে ডাকা হয়েছে!",
            'called_appointment' => $nextApt,
            'current_serving_serial' => $nextApt->serial_number,
        ]);
    }

    /**
     * Update appointment status by assistant (confirmed, in-chamber, completed, cancelled).
     */
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'is_called' => 'nullable|boolean',
        ]);

        $appointment = Appointment::findOrFail($id);
        $authUser = $request->user();
        $isAdmin = in_array($authUser->role ?? '', ['admin', 'super_admin'], true);

        if (! $isAdmin) {
            $allowedDoctorId = null;
            if ($authUser instanceof DoctorAssistant) {
                $allowedDoctorId = $authUser->doctor_id;
            } elseif ($authUser && $authUser->role === 'doctor') {
                $doc = Doctor::where('user_id', $authUser->id)->first();
                $allowedDoctorId = $doc?->id;
            }

            if ($allowedDoctorId && $appointment->doctor_id !== $allowedDoctorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'অননুমোদিত অ্যাকশন! আপনি শুধুমাত্র আপনার নির্ধারিত চেম্বারের অ্যাপয়েন্টমেন্ট পরিবর্তন করতে পারেন।',
                ], 403);
            }
        }

        $appointment->status = $data['status'];
        if (isset($data['is_called'])) {
            $appointment->is_called = $data['is_called'];
        }
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'অ্যাপয়েন্টমেন্টের স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে।',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Quick walk-in serial creation at chamber counter.
     */
    public function createWalkin(int $assistantId, Request $request): JsonResponse
    {
        $assistant = DoctorAssistant::findOrFail($assistantId);
        $doctorId = $assistant->doctor_id;
        $doctor = Doctor::findOrFail($doctorId);

        $data = $request->validate([
            'patient_name' => 'required|string|min:3|max:100',
            'patient_phone' => 'required|string|min:7|max:20',
            'reason_for_visit' => 'nullable|string|max:500',
        ]);

        $today = now()->format('Y-m-d');
        $currentTime = now()->format('h:i A');

        $serialNumber = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $today)
            ->count() + 1;

        $appointmentNumber = 'APT-WK-'.strtoupper(Str::random(6));

        $appointment = Appointment::create([
            'appointment_number' => $appointmentNumber,
            'doctor_id' => $doctorId,
            'patient_name' => $data['patient_name'],
            'patient_email' => 'walkin_'.time().'_'.rand(100, 999).'@sukhimanush.local',
            'patient_phone' => $data['patient_phone'],
            'appointment_date' => $today,
            'appointment_time' => $currentTime,
            'consultation_type' => 'in_chamber',
            'consultation_fee' => $doctor->consultation_fee ?? 500,
            'serial_number' => $serialNumber,
            'current_serving_serial' => 1,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'status' => 'confirmed',
            'reason_for_visit' => $data['reason_for_visit'] ?? 'চেম্বার ওয়াক-ইন রোগী',
        ]);

        $smsResult = app(SmsNotificationService::class)->sendBookingConfirmation($appointment->load('doctor'));

        return response()->json([
            'success' => true,
            'message' => "নতুন সিরিয়াল #{$serialNumber} ({$appointment->patient_name}) সফলভাবে যুক্ত হয়েছে!",
            'appointment' => $appointment,
            'whatsapp_url' => $smsResult['whatsapp_url'] ?? null,
            'sms_sent' => $smsResult['sms_status']['success'] ?? false,
        ], 201);
    }

    /**
     * Get list of assistants for a doctor.
     */
    public function index(int $doctorId, Request $request): JsonResponse
    {
        $authUser = $request->user();
        $isAdmin = in_array($authUser->role ?? '', ['admin', 'super_admin'], true);
        if (! $isAdmin) {
            $doc = Doctor::where('user_id', $authUser->id)->first();
            if (! $doc || $doc->id !== $doctorId) {
                return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাকশন।'], 403);
            }
        }

        $assistants = DoctorAssistant::where('doctor_id', $doctorId)->get();

        return response()->json([
            'success' => true,
            'assistants' => $assistants,
        ]);
    }

    /**
     * Doctor creates an assistant account with PIN.
     */
    public function store(int $doctorId, Request $request): JsonResponse
    {
        $authUser = $request->user();
        $isAdmin = in_array($authUser->role ?? '', ['admin', 'super_admin'], true);
        if (! $isAdmin) {
            $doc = Doctor::where('user_id', $authUser->id)->first();
            if (! $doc || $doc->id !== $doctorId) {
                return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাকশন! আপনি শুধুমাত্র নিজের চেম্বারের সহকারী তৈরি করতে পারেন।'], 403);
            }
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'access_pin' => 'required|string|min:4|max:6',
        ]);

        $data['doctor_id'] = $doctorId;
        $data['access_pin'] = Hash::make(trim($data['access_pin']));

        $assistant = DoctorAssistant::create($data);

        return response()->json([
            'success' => true,
            'message' => 'সহকারী/কম্পাউন্ডার সাব-একাউন্ট তৈরি হয়েছে!',
            'assistant' => $assistant,
        ], 201);
    }

    /**
     * Delete an assistant account.
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        $assistant = DoctorAssistant::findOrFail($id);
        $authUser = $request->user();
        $isAdmin = in_array($authUser->role ?? '', ['admin', 'super_admin'], true);
        if (! $isAdmin) {
            $doc = Doctor::where('user_id', $authUser->id)->first();
            if (! $doc || $doc->id !== $assistant->doctor_id) {
                return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাকশন! আপনি শুধুমাত্র নিজের চেম্বারের সহকারী মুছে ফেলতে পারেন।'], 403);
            }
        }

        $assistant->delete();

        return response()->json([
            'success' => true,
            'message' => 'সহকারীর একাউন্ট মুছে ফেলা হয়েছে।',
        ]);
    }
}
