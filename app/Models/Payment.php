<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $transaction_id
 * @property int $appointment_id
 * @property int|null $doctor_id
 * @property int|null $patient_id
 * @property float $amount
 * @property float $admin_commission
 * @property float $doctor_earning
 * @property string $payment_method
 * @property string $status
 * @property-read Appointment|null $appointment
 * @property-read Doctor|null $doctor
 * @property-read User|null $patient
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'appointment_id',
        'doctor_id',
        'patient_id',
        'amount',
        'admin_commission',
        'doctor_earning',
        'payment_method',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'admin_commission' => 'decimal:2',
            'doctor_earning' => 'decimal:2',
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
}
