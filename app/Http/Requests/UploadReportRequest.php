<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'report_file' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:20480',
            'report_title' => 'required|string|max:255',
            'test_type' => 'nullable|string|max:50',
            'diagnostic_center' => 'nullable|string|max:255',
            'report_date' => 'nullable|date',
            'doctor_id' => 'nullable|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'patient_notes' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
