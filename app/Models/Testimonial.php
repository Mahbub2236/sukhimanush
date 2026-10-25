<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_name',
        'title',
        'review',
        'rating',
        'avatar',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];
}
