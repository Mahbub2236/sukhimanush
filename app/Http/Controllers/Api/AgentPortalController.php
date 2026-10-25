<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use App\Services\SmsNotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgentPortalController extends Controller
{
    /**
     * Agent / Rural Digital Health Point Dashboard overview.
     */
    public function dashboard(int $agentId, Request $request): JsonResponse
    {
        $authUser = $request->user();
        $isAdmin = $authUser && in_array($authUser->role, ['admin', 'super_admin'], true);
        if ($authUser && ! $isAdmin && $authUser->id !== $agentId) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 403);
        }

        $agent = User::findOrFail($agentId);

        $bookings = Appointment::where('agent_id', $agentId)
            ->with(['doctor', 'patient'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(30)
            ->get();

        $todayBookings = $bookings->filter(fn ($b) => Carbon::parse($b->appointment_date)->isToday());
        $totalBookingsCount = Appointment::where('agent_id', $agentId)->count();
        $totalEarnedCommission = Appointment::where('agent_id', $agentId)->sum('agent_commission');

        return response()->json([
            'success' => true,
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'pharmacy_name' => $agent->pharmacy_name ?: ($agent->name.' ডিজিটাল হেলথ পয়েন্ট'),
                'phone' => $agent->phone,
                'is_approved' => (bool) $agent->is_approved,
                'commission_balance' => (float) $agent->agent_commission_balance,
                'total_commission_earned' => (float) $totalEarnedCommission,
                'total_bookings_count' => $totalBookingsCount,
            ],
            'stats' => [
                'today_count' => $todayBookings->count(),
                'today_commission' => $todayBookings->sum('agent_commission'),
                'pending_consultations' => $bookings->where('status', 'confirmed')->count(),
            ],
            'recent_bookings' => $bookings,
        ]);
    }

    /**
     * Search patient by phone or name for quick walk-in selection.
     */
    public function searchPatients(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['success' => true, 'patients' => []]);
        }

        $patients = User::where('role', 'patient')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->take(10)
            ->get(['id', 'name', 'phone', 'email', 'avatar']);

        return response()->json([
            'success' => true,
            'patients' => $patients,
        ]);
    }

    /**
     * Create walk-in appointment from Rural Digital Health Point / Pharmacy.
     */
    public function createBooking(int $agentId, Request $request): JsonResponse
    {
        $authUser = $request->user();
        $isAdmin = $authUser && in_array($authUser->role, ['admin', 'super_admin'], true);
        if ($authUser && ! $isAdmin && $authUser->id !== $agentId) {
            return response()->json(['success' => false, 'message' => 'অননুমোদিত অ্যাক্সেস।'], 403);
        }

        $agent = User::findOrFail($agentId);

        if (! $agent->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'আপনার ডিজিটাল হেলথ পয়েন্টটি এখনও অ্যাডমিন কর্তৃক অনুমোদিত হয়নি। অ্যাডমিন অনুমোদনের পর আপনি রোগী বুকিং সম্পন্ন করতে পারবেন।',
            ], 403);
        }

        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_name' => 'required|string|max:150',
            'patient_phone' => 'required|string|max:25',
            'appointment_date' => 'required|date|after_or_equal:today',
            'consultation_type' => 'nullable|in:in_chamber,online_video',
            'reason_for_visit' => 'nullable|string',
            'district' => 'nullable|string',
            'upazila' => 'nullable|string',
        ]);

        $doctor = Doctor::findOrFail($data['doctor_id']);

        // Anti-Fraud: Prevent duplicate bookings for the same patient on the same day
        $alreadyBooked = Appointment::where('doctor_id', $doctor->id)
            ->where('patient_phone', $data['patient_phone'])
            ->whereDate('appointment_date', $data['appointment_date'])
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($alreadyBooked) {
            return response()->json([
                'success' => false,
                'message' => 'উক্ত রোগীর জন্য এই তারিখে এই ডাক্তারের অ্যাপয়েন্টমেন্ট ইতোমধ্যে বুক করা আছে।',
            ], 422);
        }

        // Find or create patient user
        $patient = User::where('phone', $data['patient_phone'])->first();
        if (! $patient) {
            $patient = User::create([
                'name' => $data['patient_name'],
                'phone' => $data['patient_phone'],
                'email' => 'patient_'.time().'_'.Str::random(6).'@sukhimanush.local',
                'password' => bcrypt(Str::random(16)),
                'role' => 'patient',
            ]);
        }

        // Generate serial
        $serialNumber = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $data['appointment_date'])
            ->count() + 1;

        $consultType = $data['consultation_type'] ?? 'in_chamber';
        $fee = ($consultType === 'online_video') ? $doctor->telemedicine_fee : $doctor->consultation_fee;
        $agentCommission = 40.00; // ৳40 commission per agent booking

        $appointmentNumber = 'AGT-'.strtoupper(Str::random(3)).'-'.rand(1000, 9999);

        $appointment = Appointment::create([
            'appointment_number' => $appointmentNumber,
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'agent_id' => $agent->id,
            'patient_name' => $data['patient_name'],
            'patient_phone' => $data['patient_phone'],
            'patient_email' => $patient->email,
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $doctor->start_time ?: '05:00 PM',
            'consultation_type' => $consultType,
            'district' => $data['district'] ?? $doctor->district,
            'upazila' => $data['upazila'] ?? $doctor->upazila,
            'serial_number' => $serialNumber,
            'consultation_fee' => $fee,
            'agent_commission' => $agentCommission,
            'payment_status' => 'paid',
            'payment_method' => 'cash_agent',
            'status' => 'confirmed',
            'reason_for_visit' => $data['reason_for_visit'] ?? 'গ্রামীণ ডিজিটাল হেলথ পয়েন্ট হতে বুকিং',
        ]);

        // Add commission to agent's balance
        $agent->increment('agent_commission_balance', $agentCommission);

        // Dispatch automated SMS and WhatsApp link for rural patient
        $smsResult = app(SmsNotificationService::class)->sendBookingConfirmation($appointment);

        return response()->json([
            'success' => true,
            'message' => "রোগীর বুকিং সফল হয়েছে! সিরিয়াল নম্বর: #{$serialNumber}",
            'appointment' => $appointment->load('doctor'),
            'tracking_code' => $appointmentNumber,
            'commission_earned' => $agentCommission,
            'whatsapp_url' => $smsResult['whatsapp_url'] ?? null,
            'sms_sent' => $smsResult['sms_status']['success'] ?? false,
        ]);
    }
}
