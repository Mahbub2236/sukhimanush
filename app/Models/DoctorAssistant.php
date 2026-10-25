<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property int $doctor_id
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property string $access_pin
 * @property bool $is_active
 * @property-read Doctor|null $doctor
 */
class DoctorAssistant extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'doctor_id',
        'name',
        'phone',
        'email',
        'access_pin',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'access_pin',
    ];

    /**
     * @return BelongsTo<Doctor, $this>
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
