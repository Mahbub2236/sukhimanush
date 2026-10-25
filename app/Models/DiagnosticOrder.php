<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosticOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'patient_id',
        'doctor_id',
        'prescription_id',
        'diagnostic_center_id',
        'diagnostic_center',
        'test_names',
        'total_amount',
        'commission_rate',
        'platform_commission',
        'patient_discount',
        'sample_collection_type',
        'collection_address',
        'preferred_date',
        'preferred_time',
        'contact_phone',
        'status',
        'report_file',
    ];

    protected function casts(): array
    {
        return [
            'test_names' => 'array',
            'preferred_date' => 'date:Y-m-d',
            'total_amount' => 'float',
            'commission_rate' => 'float',
            'platform_commission' => 'float',
            'patient_discount' => 'float',
        ];
    }

    public function diagnosticCenter(): BelongsTo
    {
        return $this->belongsTo(DiagnosticCenter::class, 'diagnostic_center_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'LAB-'.date('Y');
        $random = strtoupper(substr(uniqid(), -5));

        return "{$prefix}-{$random}";
    }
}
