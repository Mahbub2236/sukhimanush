<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookAppointmentApiRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorChamber;
use App\Models\Payment;
use App\Models\User;
use App\Services\SmsNotificationService;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppointmentBookingController extends Controller
{
    /**
     * Book appointment with doctor and process SaaS payment transaction (High-Concurrency Atomic Lock Safe).
     */
    public function store(BookAppointmentApiRequest $request): JsonResponse
    {
        $data = $request->validated();

        $doctor = Doctor::findOrFail($data['doctor_id']);
        $date = $data['appointment_date'];
        $time = $data['appointment_time'];
        $consultType = ($data['consultation_type'] ?? 'in_chamber') === 'in_person' ? 'in_chamber' : ($data['consultation_type'] ?? 'in_chamber');

        // Resolve specific chamber if booking for in_chamber
        $chamber = null;
        if (! empty($data['chamber_id'])) {
            $chamber = DoctorChamber::where('id', $data['chamber_id'])->where('doctor_id', $doctor->id)->first();
        }
        if (! $chamber && $consultType === 'in_chamber') {
            $chamber = $doctor->primaryChamber ?: $doctor->chambers()->first();
        }

        // High Concurrency Atomic Lock (Prevents double bookings & quota overflow in 1k-5k spikes)
        $lockKey = "booking_lock_{$doctor->id}_{$date}_".md5($time);

        try {
            return Cache::lock($lockKey, 5)->block(3, function () use ($doctor, $chamber, $data, $date, $time, $consultType) {
                return DB::transaction(function () use ($doctor, $chamber, $data, $date, $time, $consultType) {
                    // 1. Prevent double booking for the same doctor at the same slot
                    $alreadyBooked = Appointment::where('doctor_id', $doctor->id)
                        ->whereDate('appointment_date', $date)
                        ->where('appointment_time', $time)
                        ->where('status', '!=', 'cancelled')
                        ->lockForUpdate()
                        ->exists();

                    if ($alreadyBooked) {
                        return response()->json([
                            'success' => false,
                            'message' => "The time slot ({$time}) on {$date} was just booked by another patient. Please pick another available slot.",
                        ], 422);
                    }

                    // 2. Check if date is a Free Consultation Day for this doctor
                    $isFree = $doctor->isFreeOnDate($date);

                    if ($isFree) {
                        $remainingQuota = $doctor->getFreeConsultationRemainingQuota($date);
                        if ($remainingQuota <= 0) {
                            return response()->json([
                                'success' => false,
                                'message' => "দুঃখিত! {$date} তারিখের ফ্রি কনসালটেশন কোটা (সর্বোচ্চ {$doctor->free_patient_limit} জন) পূর্ণ হয়ে গেছে। অনুগ্রহ করে অন্য দিন নির্বাচন করুন।",
                            ], 422);
                        }
                    }

                    // 3. Calculate patient's daily serial number atomically
                    $serialNumber = Appointment::where('doctor_id', $doctor->id)
                        ->whereDate('appointment_date', $date)
                        ->lockForUpdate()
                        ->count() + 1;

                    // 4. Associate with logged-in user or match patient profile safely
                    /** @var User|null $authUser */
                    $authUser = auth('sanctum')->user();
                    $patientId = $authUser ? $authUser->id : null;
                    if (! $patientId && ! empty($data['patient_id']) && $authUser && in_array($authUser->role, ['admin', 'super_admin', 'agent'], true)) {
                        $patientId = $data['patient_id'];
                    }
                    if (! $patientId) {
                        $existingUser = User::where('email', $data['patient_email'])->where('role', 'patient')->first();
                        if ($existingUser) {
                            $patientId = $existingUser->id;
                        }
                    }

                    if ($isFree) {
                        $fee = 0.00;
                        $adminCommission = 0.00;
                        $doctorEarning = 0.00;
                        $paymentMethod = 'free_charity';
                    } else {
                        if ($consultType === 'online_video') {
                            $fee = (float) ($doctor->telemedicine_fee ?: $doctor->consultation_fee ?: 50.00);
                        } elseif ($chamber && $chamber->consultation_fee) {
                            $fee = (float) $chamber->consultation_fee;
                        } else {
                            $fee = (float) ($doctor->consultation_fee ?: 50.00);
                        }
                        $adminCommissionRate = 0.10; // 10% platform fee
                        $adminCommission = round($fee * $adminCommissionRate, 2);
                        $doctorEarning = round($fee - $adminCommission, 2);
                        $paymentMethod = $data['payment_method'] ?? 'bkash';
                    }

                    $aptNumber = Appointment::generateAppointmentNumber();
                    $meetingLink = null;
                    if ($consultType === 'online_video') {
                        $meetingLink = $doctor->telemedicine_link ?: url("/video-chamber/{$aptNumber}");
                    }

                    // Determine real payment and appointment status
                    if ($isFree) {
                        $appointmentPaymentStatus = 'paid';
                        $appointmentStatus = 'confirmed';
                        $paymentRecordStatus = 'completed';
                        $txnPrefix = 'TXN-FREE-';
                    } elseif ($paymentMethod === 'cash') {
                        $appointmentPaymentStatus = 'unpaid';
                        $appointmentStatus = 'confirmed';
                        $paymentRecordStatus = 'pending';
                        $txnPrefix = 'TXN-CASH-';
                    } elseif (in_array($paymentMethod, ['bkash', 'nagad', 'rocket'], true)) {
                        $appointmentPaymentStatus = 'unpaid';
                        $appointmentStatus = 'pending';
                        $paymentRecordStatus = 'pending';
                        $txnPrefix = 'TXN-MFS-';
                    } else {
                        $appointmentPaymentStatus = 'unpaid';
                        $appointmentStatus = 'pending';
                        $paymentRecordStatus = 'pending';
                        $txnPrefix = 'TXN-';
                    }

                    $appointment = Appointment::create([
                        'appointment_number' => $aptNumber,
                        'doctor_id' => $doctor->id,
                        'chamber_id' => $chamber?->id,
                        'patient_id' => $patientId,
                        'family_member_id' => $data['family_member_id'] ?? null,
                        'patient_name' => $data['patient_name'],
                        'patient_email' => $data['patient_email'],
                        'patient_phone' => $data['patient_phone'],
                        'appointment_date' => $date,
                        'appointment_time' => $time,
                        'serial_number' => $serialNumber,
                        'current_serving_serial' => 1,
                        'consultation_fee' => $fee,
                        'is_free_consultation' => $isFree,
                        'consultation_type' => $consultType,
                        'district' => $chamber ? ($chamber->district ?: ($doctor->district ?? 'Dhaka')) : ($data['district'] ?? ($doctor->district ?? 'Dhaka')),
                        'meeting_link' => $meetingLink,
                        'payment_status' => $appointmentPaymentStatus,
                        'payment_method' => $paymentMethod,
                        'status' => $appointmentStatus,
                        'reason_for_visit' => $data['reason_for_visit'] ?? null,
                    ]);

                    // Record SaaS payment transaction
                    Payment::create([
                        'transaction_id' => $txnPrefix.strtoupper(Str::random(10)),
                        'appointment_id' => $appointment->id,
                        'doctor_id' => $doctor->id,
                        'patient_id' => $patientId,
                        'amount' => $fee,
                        'admin_commission' => $adminCommission,
                        'doctor_earning' => $doctorEarning,
                        'payment_method' => $paymentMethod,
                        'status' => $paymentRecordStatus,
                    ]);

                    // Send automated Bangla SMS and generate WhatsApp Share URL
                    $smsResult = app(SmsNotificationService::class)->sendBookingConfirmation($appointment);

                    $successMessage = $isFree
                        ? "ফ্রি কনসালটেশন অ্যাপয়েন্টমেন্ট সফলভাবে নিশ্চিত হয়েছে! আপনার সিরিয়াল নং: #{$serialNumber}"
                        : "অ্যাপয়েন্টমেন্ট সফলভাবে নিশ্চিত হয়েছে! সিরিয়াল নং: #{$serialNumber}";

                    return response()->json([
                        'success' => true,
                        'is_free' => $isFree,
                        'message' => $successMessage,
                        'whatsapp_url' => $smsResult['whatsapp_url'] ?? null,
                        'sms_sent' => $smsResult['sms_status']['success'] ?? false,
                        'appointment' => $appointment->load(['doctor', 'chamber', 'familyMember']),
                    ], 201);
                });
            });
        } catch (LockTimeoutException $e) {
            return response()->json([
                'success' => false,
                'message' => 'সার্ভারে অতিরিক্ত চাপের কারণে রিকোয়েস্টটি প্রসেস হতে পারেনি। অনুগ্রহ করে ২ সেকেন্ড পর পুনরায় চেষ্টা করুন।',
            ], 429);
        }
    }

    /**
     * Track live appointment pass by tracking number or phone.
     */
    public function track(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        $q = trim($request->input('query'));

        $authUser = $request->user('sanctum');

        $appointments = Appointment::with(['doctor', 'chamber'])
            ->where('appointment_number', $q)
            ->orWhere('patient_phone', $q)
            ->orWhere('patient_email', $q)
            ->orderByDesc('created_at')
            ->get();

        $maskedAppointments = $appointments->map(function ($apt) use ($authUser) {
            $isOwner = $authUser && ($authUser->id === $apt->patient_id || in_array($authUser->role, ['admin', 'super_admin'], true));
            if (! $isOwner) {
                $apt = clone $apt;
                $apt->reason_for_visit = null;
                $apt->doctor_notes = null;
                if (! empty($apt->patient_phone)) {
                    $len = strlen($apt->patient_phone);
                    if ($len >= 6) {
                        $apt->patient_phone = substr($apt->patient_phone, 0, 3).'****'.substr($apt->patient_phone, -2);
                    } else {
                        $apt->patient_phone = '***';
                    }
                }
                if (! empty($apt->patient_email)) {
                    $parts = explode('@', $apt->patient_email);
                    $apt->patient_email = substr($parts[0], 0, 2).'***@'.($parts[1] ?? 'domain');
                }
            }

            return $apt;
        });

        return response()->json([
            'success' => true,
            'count' => $maskedAppointments->count(),
            'appointments' => $maskedAppointments,
        ]);
    }

    /**
     * Get live video chamber details for an appointment.
     * Accessible by both patient and doctor without requiring any email or password.
     */
    public function videoChamber(string $appointmentNumber): JsonResponse
    {
        $appointment = Appointment::with(['doctor', 'chamber', 'prescription'])
            ->where('appointment_number', trim($appointmentNumber))
            ->first();

        if (! $appointment) {
            return response()->json([
                'success' => false,
                'message' => 'অ্যাপয়েন্টমেন্ট পাওয়া যায়নি। অনুগ্রহ করে সঠিক সিরিয়াল/ট্র্যাকিং নম্বর প্রদান করুন।',
            ], 404);
        }

        // Clean room name suitable for WebRTC / Jitsi
        $sanitizedCode = preg_replace('/[^a-zA-Z0-9]/', '', $appointment->appointment_number);
        $roomName = 'SukhiManushTelemed_'.$sanitizedCode;

        // Current authenticated user (if any)
        /** @var User|null $authUser */
        $authUser = auth('sanctum')->user();
        $isDoctor = false;
        if ($authUser) {
            if ($authUser->role === 'doctor' || ($appointment->doctor && $appointment->doctor->user_id === $authUser->id)) {
                $isDoctor = true;
            }
        }

        return response()->json([
            'success' => true,
            'appointment' => [
                'id' => $appointment->id,
                'appointment_number' => $appointment->appointment_number,
                'patient_name' => $appointment->patient_name,
                'patient_phone' => $appointment->patient_phone,
                'appointment_date' => $appointment->appointment_date,
                'appointment_time' => $appointment->appointment_time,
                'serial_number' => $appointment->serial_number,
                'consultation_type' => $appointment->consultation_type,
                'meeting_link' => $appointment->meeting_link,
                'status' => $appointment->status,
                'reason_for_visit' => $appointment->reason_for_visit,
            ],
            'doctor' => [
                'id' => $appointment->doctor?->id,
                'name' => $appointment->doctor?->name,
                'specialization' => $appointment->doctor?->specialization,
                'designation' => $appointment->doctor?->designation,
                'avatar' => $appointment->doctor?->avatar,
                'telemedicine_link' => $appointment->doctor?->telemedicine_link,
                'department_name' => $appointment->doctor?->specialization,
                'phone' => $appointment->doctor?->phone,
            ],
            'room_name' => $roomName,
            'is_doctor' => $isDoctor,
        ]);
    }
}
