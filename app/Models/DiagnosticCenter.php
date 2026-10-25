<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DiagnosticCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'license_number',
        'division',
        'district',
        'upazila',
        'address',
        'contact_phone',
        'contact_email',
        'commission_rate',
        'discount_rate',
        'home_collection_available',
        'home_collection_fee',
        'available_tests',
        'is_approved',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'available_tests' => 'array',
            'commission_rate' => 'float',
            'discount_rate' => 'float',
            'home_collection_fee' => 'float',
            'home_collection_available' => 'boolean',
            'is_approved' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(DiagnosticOrder::class);
    }

    /**
     * Calculate dynamic commission for a given total bill amount.
     */
    public function calculateCommission(float $totalAmount): float
    {
        $rate = $this->commission_rate ?: 15.00;

        return round($totalAmount * ($rate / 100), 2);
    }

    /**
     * Calculate patient discount for a given total bill amount.
     */
    public function calculateDiscount(float $totalAmount): float
    {
        $rate = $this->discount_rate ?: 0.00;

        return round($totalAmount * ($rate / 100), 2);
    }

    public static function generateSlug(string $name): string
    {
        $slug = Str::slug($name);
        $count = static::where('slug', 'like', "{$slug}%")->count();

        return $count ? "{$slug}-".($count + 1) : $slug;
    }
}
