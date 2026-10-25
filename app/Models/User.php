<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string $password
 * @property string $role
 * @property bool $is_approved
 * @property string|null $phone
 * @property string|null $avatar
 * @property string|null $address
 * @property string|null $pharmacy_name
 * @property float $agent_commission_balance
 * @property string|null $blood_group
 * @property string|null $emergency_contact_phone
 * @property string|null $emergency_medical_notes
 * @property Carbon|null $email_verified_at
 * @property-read Doctor|null $doctor
 * @property-read Collection<int, Appointment> $appointments
 * @property-read Collection<int, Prescription> $prescriptions
 * @property-read Collection<int, Payment> $payments
 * @property-read Collection<int, Appointment> $agentAppointments
 * @property-read Collection<int, FamilyMember> $familyMembers
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_approved',
        'phone',
        'avatar',
        'address',
        'pharmacy_name',
        'agent_commission_balance',
        'blood_group',
        'emergency_contact_phone',
        'emergency_medical_notes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Doctor profile relation (if role === 'doctor').
     *
     * @return HasOne<Doctor, $this>
     */
    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    /**
     * Patient's booked appointments.
     *
     * @return HasMany<Appointment, $this>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    /**
     * Prescriptions issued to the patient.
     *
     * @return HasMany<Prescription, $this>
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'patient_id');
    }

    /**
     * Payments made by the patient.
     *
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'patient_id');
    }

    /**
     * @return HasMany<Appointment, $this>
     */
    public function agentAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'agent_id');
    }

    /**
     * @return HasMany<FamilyMember, $this>
     */
    public function familyMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class, 'user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin'], true);
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }
}
