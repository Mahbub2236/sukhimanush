<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PatientReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_number',
        'patient_id',
        'doctor_id',
        'appointment_id',
        'report_title',
        'test_type',
        'diagnostic_center',
        'report_date',
        'file_path',
        'file_type',
        'file_size',
        'patient_notes',
        'doctor_remarks',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date:Y-m-d',
        ];
    }

    protected $appends = [
        'file_url',
        'test_type_label',
    ];

    public function getFileUrlAttribute(): string
    {
        if (empty($this->file_path)) {
            return '';
        }

        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        try {
            return URL::temporarySignedRoute(
                'patient.reports.download',
                now()->addHours(24),
                ['id' => $this->id]
            );
        } catch (\Throwable $e) {
            return url('/api/patient-reports/stream/'.$this->id);
        }
    }

    public function getTestTypeLabelAttribute(): string
    {
        $labels = [
            'blood_test' => 'রক্ত পরীক্ষা (Blood / CBC)',
            'xray' => 'এক্স-রে (X-Ray)',
            'usg' => 'আল্ট্রাসনোগ্রাম (USG)',
            'ecg' => 'ইসিজি (ECG)',
            'urine' => 'প্রস্রাব পরীক্ষা (Urine Test)',
            'mri_ct' => 'এমআরআই / সিটি স্ক্যান (MRI / CT)',
            'pathology' => 'প্যাথলজি (Pathology)',
            'prescription_copy' => 'প্রেসক্রিপশন কপি (Prescription Photo)',
            'other' => 'অন্যান্য রিপোর্ট (Other)',
        ];

        return $labels[$this->test_type] ?? 'মেডিকেল রিপোর্ট';
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public static function generateReportNumber(): string
    {
        $prefix = 'REP-'.date('Y');
        $random = strtoupper(Str::random(10));

        return "{$prefix}-{$random}";
    }
}
