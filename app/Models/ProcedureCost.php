<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_bn',
        'category',
        'min_cost',
        'max_cost',
        'cost_breakdown',
        'typical_stay_days',
        'included_services_bn',
        'excluded_services_bn',
        'verification_badge',
    ];

    protected $casts = [
        'cost_breakdown' => 'array',
        'min_cost' => 'decimal:2',
        'max_cost' => 'decimal:2',
    ];
}
