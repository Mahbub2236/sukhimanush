<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $appointment_number
 * @property int $doctor_id
 * @property int|null $chamber_id
 * @property int|null $patient_id
 * @property int|null $family_member_id
 * @property string $patient_name
 * @property string|null $patient_email
 * @property string $patient_phone
 * @property Carbon|string $appointment_date
 * @property string|null $appointment_time
 * @property int $serial_number
 * @property int|null $current_serving_serial
 * @property float $consultation_fee
 * @property bool $is_free_consultation
 * @property string|null $consultation_type
 * @property string|null $district
 * @property string|null $upazila
 * @property string|null $meeting_link
 * @property string $payment_status
 * @property string|null $payment_method
 * @property string $status
 * @property bool $is_called
 * @property bool $is_emergency
 * @property string|null $reason_for_visit
 * @property string|null $doctor_notes
 * @property int|null $agent_id
 * @property float|null $agent_commission
 * @property-read Doctor|null $doctor
 * @property-read DoctorChamber|null $chamber
 * @property-read User|null $patient
 * @property-read FamilyMember|null $familyMember
 * @property-read Prescription|null $prescription
 * @property-read Payment|null $payment
 * @property-read User|null $agent
 */
class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_number',
        'doctor_id',
        'chamber_id',
        'patient_id',
        'family_member_id',
        'patient_name',
        'patient_email',
        'patient_phone',
        'appointment_date',
        'appointment_time',
        'serial_number',
        'current_serving_serial',
        'consultation_fee',
        'is_free_consultation',
        'consultation_type',
        'district',
        'upazila',
        'meeting_link',
        'payment_status',
        'payment_method',
        'status',
        'is_called',
        'is_emergency',
        'reason_for_visit',
        'doctor_notes',
        'agent_id',
        'agent_commission',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date:Y-m-d',
            'serial_number' => 'integer',
            'current_serving_serial' => 'integer',
            'consultation_fee' => 'decimal:2',
            'is_free_consultation' => 'boolean',
            'is_called' => 'boolean',
            'is_emergency' => 'boolean',
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
     * @return BelongsTo<DoctorChamber, $this>
     */
    public function chamber(): BelongsTo
    {
        return $this->belongsTo(DoctorChamber::class, 'chamber_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * @return BelongsTo<FamilyMember, $this>
     */
    public function familyMember(): BelongsTo
    {
        return $this->belongsTo(FamilyMember::class);
    }

    /**
     * @return HasOne<Prescription, $this>
     */
    public function prescription(): HasOne
    {
        return $this->hasOne(Prescription::class);
    }

    /**
     * @return HasOne<Payment, $this>
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public static function generateAppointmentNumber(): string
    {
        $prefix = 'DOC-'.date('Y');
        $random = strtoupper(Str::random(10));

        return "{$prefix}-{$random}";
    }
}
