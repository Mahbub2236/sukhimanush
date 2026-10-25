<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalBed extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_name',
        'hospital_type',
        'district',
        'upazila',
        'hotline',
        'emergency_phone',
        'icu_available',
        'icu_total',
        'ccu_available',
        'nicu_available',
        'general_beds_available',
        'last_verified_at',
    ];

    protected $casts = [
        'last_verified_at' => 'datetime',
    ];
}
