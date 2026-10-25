<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmbulanceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'driver_or_contact_name',
        'phone',
        'vehicle_type',
        'district',
        'upazila',
        'base_fare',
        'per_km_rate',
        'is_available_24_7',
    ];

    protected $casts = [
        'base_fare' => 'decimal:2',
        'per_km_rate' => 'decimal:2',
        'is_available_24_7' => 'boolean',
    ];
}
