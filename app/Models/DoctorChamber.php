<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $doctor_id
 * @property string $chamber_name
 * @property string|null $branch_name
 * @property string $address
 * @property string|null $city
 * @property string|null $district
 * @property string|null $upazila
 * @property string|null $phone
 * @property array<int, string>|null $visiting_days
 * @property string|null $start_time
 * @property string|null $end_time
 * @property int $slot_duration_minutes
 * @property float|null $consultation_fee
 * @property bool $is_primary
 * @property bool $is_active
 * @property-read Doctor|null $doctor
 * @property-read Collection<int, Appointment> $appointments
 */
class DoctorChamber extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'chamber_name',
        'branch_name',
        'address',
        'city',
        'district',
        'upazila',
        'phone',
        'visiting_days',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'consultation_fee',
        'is_primary',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'visiting_days' => 'array',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
            'consultation_fee' => 'decimal:2',
            'slot_duration_minutes' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Doctor, $this>
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * @return HasMany<Appointment, $this>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'chamber_id');
    }

    /**
     * Calculate available time slots for this chamber on a given date.
     */
    public function getAvailableSlotsForDate(string $date): array
    {
        $dateObj = Carbon::parse($date);
        $dayOfWeek = $dateObj->format('l');

        $visitingDays = is_array($this->visiting_days) && ! empty($this->visiting_days)
            ? $this->visiting_days
            : ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        if (! in_array($dayOfWeek, $visitingDays)) {
            return [];
        }

        $startStr = $this->start_time ?: '17:00:00';
        $endStr = $this->end_time ?: '21:00:00';

        $startTime = Carbon::parse($date.' '.$startStr);
        $endTime = Carbon::parse($date.' '.$endStr);
        $interval = max(5, $this->slot_duration_minutes ?: 20);

        $bookedSlots = $this->appointments()
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
                'chamber_id' => $this->id,
                'chamber_name' => $this->chamber_name,
            ];

            $current->addMinutes($interval);
        }

        return $slots;
    }
}
