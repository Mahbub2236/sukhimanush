<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ChamberQueue;
use App\Models\Doctor;
use App\Models\DoctorAssistant;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueTrackerController extends Controller
{
    /**
     * Get live queue status for a doctor on a specific date.
     */
    public function getQueueStatus(int $doctorId, Request $request): JsonResponse
    {
        $doctor = Doctor::findOrFail($doctorId);
        $date = $request->input('date', now()->format('Y-m-d'));

        // Retrieve or create today's chamber session
        $queueSession = ChamberQueue::firstOrCreate(
            ['doctor_id' => $doctorId, 'queue_date' => $date],
            [
                'doctor_status' => 'consultation_started',
                'avg_minutes_per_patient' => $doctor->slot_duration_minutes ?: 12,
                'started_at' => now(),
            ]
        );

        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->orderBy('serial_number')
            ->get();

        $totalSerials = $appointments->count();
        $completedCount = $appointments->where('status', 'completed')->count();
        $calledAppointment = $appointments->where('is_called', true)->where('status', '!=', 'completed')->last();

        $currentSerial = $calledAppointment
            ? $calledAppointment->serial_number
            : ($completedCount > 0 ? $completedCount : ($totalSerials > 0 ? 1 : 0));

        // Sync queue session with actual counts only if changed (prevents DB write contention on high-concurrency polling)
        if ($queueSession->current_token !== $currentSerial ||
            $queueSession->total_tokens !== $totalSerials ||
            $queueSession->completed_tokens !== $completedCount) {
            $queueSession->update([
                'current_token' => $currentSerial,
                'total_tokens' => $totalSerials,
                'completed_tokens' => $completedCount,
            ]);
        }

        $waitingCount = max(0, $totalSerials - $completedCount);
        $avgMinutes = $queueSession->avg_minutes_per_patient ?: 12;

        return response()->json([
            'success' => true,
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'specialization' => $doctor->specialization,
                'chamber_address' => $doctor->chamber_address,
                'phone' => $doctor->phone,
            ],
            'date' => $date,
            'queue' => [
                'doctor_status' => $queueSession->doctor_status,
                'current_serving_serial' => $currentSerial,
                'total_serials' => $totalSerials,
                'completed_count' => $completedCount,
                'waiting_count' => $waitingCount,
                'avg_minutes_per_patient' => $avgMinutes,
                'notice_message' => $queueSession->notice_message,
                'called_patient_name' => $calledAppointment ? $calledAppointment->patient_name : null,
                'is_emergency_active' => (bool) $queueSession->is_emergency_active,
                'emergency_reason' => $queueSession->emergency_reason,
                'emergency_patient_name' => $queueSession->emergency_patient_name,
                'emergency_started_at' => $queueSession->emergency_started_at ? $queueSession->emergency_started_at->format('h:i A') : null,
            ],
        ]);
    }

    /**
     * Verify that authenticated user has authorization to control this doctor's queue.
     */
    private function authorizeDoctorAccess(int $doctorId, Request $request): ?JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (in_array($user->role, ['admin', 'super_admin'], true)) {
            return null;
        }

        if ($user instanceof DoctorAssistant) {
            if ((int) $user->doctor_id !== $doctorId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized for this doctor queue.'], 403);
            }

            return null;
        }

        $doctor = Doctor::where('user_id', $user->id)->first();
        if (! $doctor || (int) $doctor->id !== $doctorId) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You do not own this doctor queue.'], 403);
        }

        return null;
    }

    /**
     * Advance live queue: Call next patient.
     */
    public function callNextPatient(int $doctorId, Request $request): JsonResponse
    {
        if ($authError = $this->authorizeDoctorAccess($doctorId, $request)) {
            return $authError;
        }

        $date = $request->input('date', now()->format('Y-m-d'));

        $nextApt = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->where(function ($q) {
                $q->where('is_called', false)->orWhereNull('is_called');
            })
            ->orderBy('serial_number')
            ->first();

        if (! $nextApt) {
            // Pick earliest pending
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

        // Reset previous called appointments
        Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->update(['is_called' => false]);

        $nextApt->is_called = true;
        if ($nextApt->status === 'pending') {
            $nextApt->status = 'confirmed';
        }
        $nextApt->save();

        // Update ChamberQueue session
        ChamberQueue::updateOrCreate(
            ['doctor_id' => $doctorId, 'queue_date' => $date],
            [
                'current_token' => $nextApt->serial_number,
                'last_called_at' => now(),
                'doctor_status' => 'consultation_started',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "📢 সিরিয়াল #{$nextApt->serial_number} ({$nextApt->patient_name})-কে ডাকা হয়েছে!",
            'called_appointment' => $nextApt,
            'current_serving_serial' => $nextApt->serial_number,
        ]);
    }

    /**
     * Skip patient or mark absent and advance to next.
     */
    public function skipPatient(int $doctorId, int $appointmentId, Request $request): JsonResponse
    {
        if ($authError = $this->authorizeDoctorAccess($doctorId, $request)) {
            return $authError;
        }

        $appointment = Appointment::where('doctor_id', $doctorId)->findOrFail($appointmentId);
        $appointment->is_called = false;
        $appointment->status = 'pending'; // delayed
        $appointment->save();

        return $this->callNextPatient($doctorId, $request);
    }

    /**
     * Update doctor presence / queue status (e.g. on_the_way, paused, consultation_started).
     */
    public function updateDoctorStatus(int $doctorId, Request $request): JsonResponse
    {
        if ($authError = $this->authorizeDoctorAccess($doctorId, $request)) {
            return $authError;
        }

        $data = $request->validate([
            'doctor_status' => 'required|in:not_arrived,on_the_way,consultation_started,paused,completed',
            'notice_message' => 'nullable|string|max:255',
        ]);

        $date = $request->input('date', now()->format('Y-m-d'));

        $queue = ChamberQueue::updateOrCreate(
            ['doctor_id' => $doctorId, 'queue_date' => $date],
            [
                'doctor_status' => $data['doctor_status'],
                'notice_message' => $data['notice_message'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'চেম্বার স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে।',
            'queue' => $queue,
        ]);
    }

    /**
     * Patient's live token status for a specific appointment.
     */
    public function getPatientTokenStatus(Request $request, int $appointmentId): JsonResponse
    {
        $appointment = Appointment::with(['doctor', 'patient'])->findOrFail($appointmentId);
        $doctorId = $appointment->doctor_id;
        $date = Carbon::parse($appointment->appointment_date)->format('Y-m-d');

        $queueSession = ChamberQueue::firstOrCreate(
            ['doctor_id' => $doctorId, 'queue_date' => $date],
            [
                'doctor_status' => 'consultation_started',
                'avg_minutes_per_patient' => $appointment->doctor->slot_duration_minutes ?: 12,
            ]
        );

        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->orderBy('serial_number')
            ->get();

        $totalSerials = $appointments->count();
        $completedCount = $appointments->where('status', 'completed')->count();
        $calledApt = $appointments->where('is_called', true)->where('status', '!=', 'completed')->last();

        $currentServing = $calledApt
            ? $calledApt->serial_number
            : ($queueSession->current_token ?: ($completedCount > 0 ? $completedCount : 1));

        $mySerial = $appointment->serial_number;
        $patientsAhead = max(0, $mySerial - $currentServing);
        $avgMins = $queueSession->avg_minutes_per_patient ?: 12;
        $estimatedWaitMins = $patientsAhead * $avgMins;

        // If emergency is currently in progress, add estimated 15 minutes delay buffer
        if ($queueSession->is_emergency_active) {
            $estimatedWaitMins += 15;
        }

        // Smart Bengali Milestones with Emergency Priority Notification
        $isMyTurn = ($currentServing === $mySerial && $appointment->is_called);
        $milestoneAlert = null;
        if ($queueSession->is_emergency_active) {
            $reason = $queueSession->emergency_reason ? " ({$queueSession->emergency_reason})" : '';
            $milestoneAlert = "🚨 জরুরি চিকিৎসা চলছে: চেম্বারে একজন আশঙ্কাজনক জরুরি রোগী{$reason} দেখা হচ্ছে। আপনার সিরিয়াল সাময়িকভাবে বিলম্বিত হচ্ছে। অনুগ্রহ করে একটু ধৈর্য ধরুন।";
        } elseif ($appointment->status === 'completed') {
            $milestoneAlert = 'আপনার সিরিয়াল ও কনসালটেশন সম্পন্ন হয়েছে।';
        } elseif ($isMyTurn) {
            $milestoneAlert = '📢 আপনার সিরিয়াল ডাকা হয়েছে! এখনই ডাক্তারের চেম্বারে প্রবেশ করুন।';
        } elseif ($patientsAhead === 1) {
            $milestoneAlert = 'সতর্কবার্তা: আপনার আগের রোগী চেম্বারে আছেন। প্রস্তুত থাকুন, এরপরেই আপনার ডাক পড়বে।';
        } elseif ($patientsAhead <= 3 && $patientsAhead > 0) {
            $milestoneAlert = "আর মাত্র {$patientsAhead} জন রোগী সামনে আছেন। চেম্বারের ওয়েটিং এরিয়াতে থাকুন।";
        } elseif ($patientsAhead <= 5 && $patientsAhead > 0) {
            $milestoneAlert = "আর মাত্র {$patientsAhead} জন রোগী সামনে আছেন। চেম্বারের উদ্দেশ্যে রওনা দেওয়ার পরামর্শ দেওয়া হচ্ছে।";
        }

        $user = $request->user('sanctum');
        $isOwner = $user && ($user->id === $appointment->patient_id || in_array($user->role, ['admin', 'super_admin', 'doctor', 'staff'], true));

        $sanitizedAppointment = clone $appointment;
        if (! $isOwner) {
            $sanitizedAppointment->makeHidden(['patient_phone', 'patient_email', 'reason_for_visit', 'doctor_notes']);
        }

        return response()->json([
            'success' => true,
            'appointment' => $sanitizedAppointment,
            'doctor' => $appointment->doctor,
            'my_serial' => $mySerial,
            'current_serving_serial' => $currentServing,
            'total_serials' => $totalSerials,
            'patients_ahead' => $patientsAhead,
            'estimated_wait_minutes' => $estimatedWaitMins,
            'doctor_status' => $queueSession->doctor_status,
            'notice_message' => $queueSession->notice_message,
            'is_emergency_active' => (bool) $queueSession->is_emergency_active,
            'emergency_reason' => $queueSession->emergency_reason,
            'emergency_patient_name' => $queueSession->emergency_patient_name,
            'emergency_started_at' => $queueSession->emergency_started_at ? $queueSession->emergency_started_at->format('h:i A') : null,
            'is_my_turn' => $isMyTurn,
            'is_called' => (bool) $appointment->is_called,
            'milestone_alert' => $milestoneAlert,
        ]);
    }

    /**
     * Emergency Bypass: Intervene normal serial flow for critical/emergency patient.
     */
    public function emergencyBypass(int $doctorId, Request $request): JsonResponse
    {
        if ($authError = $this->authorizeDoctorAccess($doctorId, $request)) {
            return $authError;
        }

        $data = $request->validate([
            'action' => 'required|in:start,stop',
            'patient_name' => 'nullable|string|max:100',
            'emergency_reason' => 'nullable|string|max:255',
            'date' => 'nullable|date',
        ]);

        $date = $data['date'] ?? now()->format('Y-m-d');
        $doctor = Doctor::findOrFail($doctorId);

        $queue = ChamberQueue::firstOrCreate(
            ['doctor_id' => $doctorId, 'queue_date' => $date],
            [
                'doctor_status' => 'consultation_started',
                'avg_minutes_per_patient' => $doctor->slot_duration_minutes ?: 12,
            ]
        );

        if ($data['action'] === 'start') {
            $patientName = ! empty($data['patient_name']) ? $data['patient_name'] : 'জরুরি রোগী (Emergency Patient)';
            $reason = ! empty($data['emergency_reason']) ? $data['emergency_reason'] : 'আশঙ্কাজনক জরুরি কেস';

            $queue->update([
                'is_emergency_active' => true,
                'emergency_reason' => $reason,
                'emergency_patient_name' => $patientName,
                'emergency_started_at' => now(),
                'doctor_status' => 'paused',
                'notice_message' => "🚨 চেম্বারে জরুরি রোগী ({$reason}) দেখা হচ্ছে। নিয়মিত সিরিয়াল সাময়িকভাবে স্থগিত।",
            ]);

            return response()->json([
                'success' => true,
                'message' => "🚨 ইমার্জেন্সি রোগী ({$patientName}) সফলভাবে চেম্বারে প্রবেশ করানো হয়েছে। অপেক্ষমাণ রোগীদের স্ক্রিনে লাইভ অ্যালার্ট পাঠানো হয়েছে।",
                'is_emergency_active' => true,
                'queue' => $queue->fresh(),
            ]);
        } else {
            // Stop emergency and resume normal queue
            $queue->update([
                'is_emergency_active' => false,
                'emergency_reason' => null,
                'emergency_patient_name' => null,
                'emergency_started_at' => null,
                'doctor_status' => 'consultation_started',
                'notice_message' => 'জরুরি রোগীর চিকিৎসা সম্পন্ন হয়েছে। নিয়মিত সিরিয়াল পুনরায় সচল রয়েছে।',
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ জরুরি চিকিৎসা সম্পন্ন হয়েছে! নিয়মিত সিরিয়াল পুনরায় চালু হয়েছে।',
                'is_emergency_active' => false,
                'queue' => $queue->fresh(),
            ]);
        }
    }
}
