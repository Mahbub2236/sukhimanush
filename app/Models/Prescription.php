<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $prescription_number
 * @property int $appointment_id
 * @property int $doctor_id
 * @property int|null $patient_id
 * @property string|null $patient_name
 * @property string|null $patient_age_gender
 * @property string|null $diagnosis
 * @property array<int, array<string, mixed>>|null $medicines
 * @property array<int, mixed>|null $medical_tests
 * @property string|null $advice
 * @property Carbon|null $follow_up_date
 * @property-read Appointment|null $appointment
 * @property-read Doctor|null $doctor
 * @property-read User|null $patient
 */
class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_number',
        'appointment_id',
        'doctor_id',
        'patient_id',
        'patient_name',
        'patient_age_gender',
        'diagnosis',
        'medicines',
        'medical_tests',
        'advice',
        'follow_up_date',
    ];

    protected function casts(): array
    {
        return [
            'medicines' => 'array',
            'medical_tests' => 'array',
            'follow_up_date' => 'date:Y-m-d',
        ];
    }

    /**
     * @return BelongsTo<Appointment, $this>
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * @return BelongsTo<Doctor, $this>
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public static function generatePrescriptionNumber(): string
    {
        $prefix = 'RX-'.date('Y');
        $random = strtoupper(Str::random(10));

        return "{$prefix}-{$random}";
    }
}
