<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'family_member_id',
        'prescription_id',
        'medicine_name',
        'dosage',
        'time_slot',
        'meal_instruction',
        'start_date',
        'end_date',
        'status',
        'adherence_logs',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'adherence_logs' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function familyMember(): BelongsTo
    {
        return $this->belongsTo(FamilyMember::class);
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }
}
