<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChamberQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'queue_date',
        'doctor_status',
        'current_token',
        'total_tokens',
        'completed_tokens',
        'avg_minutes_per_patient',
        'notice_message',
        'is_emergency_active',
        'emergency_reason',
        'emergency_patient_name',
        'emergency_started_at',
        'started_at',
        'last_called_at',
    ];

    protected $casts = [
        'queue_date' => 'date',
        'is_emergency_active' => 'boolean',
        'emergency_started_at' => 'datetime',
        'started_at' => 'datetime',
        'last_called_at' => 'datetime',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
