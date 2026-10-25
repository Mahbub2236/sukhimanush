<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    /**
     * Get available appointment time slots for a doctor on a given date.
     */
    public function getAvailableSlots(int $doctorId, string $date): array
    {
        $doctor = Doctor::with('chambers')->findOrFail($doctorId);
        $dateObj = Carbon::parse($date);
        $dayOfWeek = $dateObj->format('l');

        $availableDays = $doctor->available_days ?: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        if (! in_array($dayOfWeek, $availableDays)) {
            return [
                'doctor' => $doctor,
                'is_available_day' => false,
                'message' => "{$doctor->name} is not on duty on {$dayOfWeek}s.",
                'slots' => [],
            ];
        }

        // Doctor working time bounds
        $startTime = Carbon::parse($date.' '.($doctor->start_time ?: '09:00:00'));
        $endTime = Carbon::parse($date.' '.($doctor->end_time ?: '17:00:00'));
        $durationMinutes = $doctor->slot_duration_minutes ?: 30;

        // Existing booked slots on that date
        $bookedSlots = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->pluck('appointment_time')
            ->toArray();

        $slots = [];
        $current = $startTime->copy();
        $isToday = $dateObj->isToday();
        $now = Carbon::now();

        while ($current->lt($endTime)) {
            $formattedTime = $current->format('h:i A');
            $isPast = $isToday && $current->lte($now);
            $isBooked = in_array($formattedTime, $bookedSlots);

            $slots[] = [
                'time' => $formattedTime,
                'available' => ! $isBooked && ! $isPast,
                'is_booked' => $isBooked,
                'is_past' => $isPast,
            ];

            $current->addMinutes($durationMinutes);
        }

        return [
            'doctor' => $doctor,
            'is_available_day' => true,
            'message' => count($slots) ? 'Time slots loaded successfully.' : 'No slots scheduled.',
            'slots' => $slots,
        ];
    }

    /**
     * Book and create a new appointment.
     */
    public function bookAppointment(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            $doctorId = $data['doctor_id'];
            $date = $data['appointment_date'];
            $time = $data['appointment_time'];

            // Double check slot availability to prevent double-booking with pessimistic lock
            $alreadyBooked = Appointment::where('doctor_id', $doctorId)
                ->whereDate('appointment_date', $date)
                ->where('appointment_time', $time)
                ->where('status', '!=', 'cancelled')
                ->lockForUpdate()
                ->exists();

            if ($alreadyBooked) {
                throw new Exception("The selected time slot ({$time}) on {$date} was just booked by another patient. Please choose another slot.");
            }

            // Calculate serial number for that doctor on that day atomically
            $serialNumber = Appointment::where('doctor_id', $doctorId)
                ->whereDate('appointment_date', $date)
                ->lockForUpdate()
                ->count() + 1;

            $appNum = Appointment::generateAppointmentNumber();
            $data['appointment_number'] = $appNum;
            $data['serial_number'] = $serialNumber;
            $data['status'] = 'confirmed';

            if (empty($data['consultation_type'])) {
                $data['consultation_type'] = 'online_video';
            }

            if ($data['consultation_type'] === 'online_video' && empty($data['meeting_link'])) {
                $doctor = Doctor::find($doctorId);
                if ($doctor && ! empty($doctor->telemedicine_link)) {
                    $data['meeting_link'] = $doctor->telemedicine_link;
                } else {
                    $data['meeting_link'] = url("/video-chamber/{$appNum}");
                }
            }

            return Appointment::create($data)->load('doctor');
        });
    }

    /**
     * Find appointment by tracking code or phone number.
     */
    public function trackAppointment(string $query): Collection
    {
        $trimmed = trim($query);

        return Appointment::with(['doctor', 'chamber'])
            ->where('appointment_number', $trimmed)
            ->orWhere('patient_phone', $trimmed)
            ->orWhere('patient_email', $trimmed)
            ->orderByDesc('created_at')
            ->get();
    }
}
