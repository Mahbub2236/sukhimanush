<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_name',
        'generic_name',
        'dosage_form',
        'strength',
        'company',
        'unit_price',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'float',
        ];
    }
}
