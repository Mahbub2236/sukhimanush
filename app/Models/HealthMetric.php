<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'family_member_id',
        'metric_type',
        'value_primary',
        'value_secondary',
        'measured_time',
        'recorded_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'recorded_date' => 'date:Y-m-d',
            'value_primary' => 'float',
            'value_secondary' => 'float',
        ];
    }

    protected $appends = [
        'metric_label',
        'status_color',
    ];

    public function getMetricLabelAttribute(): string
    {
        $labels = [
            'blood_sugar' => 'ব্লাড সুগার / ডায়াবেটিস (mmol/L)',
            'blood_pressure' => 'রক্তচাপ / Blood Pressure (mmHg)',
            'weight' => 'শরীরের ওজন / Weight (kg)',
            'pulse' => 'হার্ট রেট / Pulse (bpm)',
            'temperature' => 'শরীরের তাপমাত্রা / Temp (°F)',
        ];

        return $labels[$this->metric_type] ?? $this->metric_type;
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->metric_type === 'blood_sugar') {
            if ($this->value_primary < 4.0) {
                return 'warning';
            } // Low sugar
            if ($this->value_primary > 10.0) {
                return 'danger';
            } // High sugar

            return 'success'; // Normal
        } elseif ($this->metric_type === 'blood_pressure') {
            if ($this->value_primary > 140 || ($this->value_secondary && $this->value_secondary > 90)) {
                return 'danger';
            }
            if ($this->value_primary < 90) {
                return 'warning';
            }

            return 'success';
        }

        return 'info';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function familyMember(): BelongsTo
    {
        return $this->belongsTo(FamilyMember::class);
    }
}
