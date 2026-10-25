<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
            'department_id' => 'required|exists:departments,id',
            'doctor_id' => 'required|exists:doctors,id',
            'patient_name' => 'required|string|min:3|max:100',
            'patient_email' => 'required|email|max:150',
            'patient_phone' => 'required|string|min:7|max:20',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|string',
            'reason_for_visit' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'Please select a medical department.',
            'doctor_id.required' => 'Please select a doctor.',
            'patient_name.required' => 'Patient full name is required.',
            'patient_email.required' => 'A valid email address is required for appointment confirmation.',
            'patient_phone.required' => 'A contact phone number is required.',
            'appointment_date.required' => 'Please choose an appointment date.',
            'appointment_date.after_or_equal' => 'Appointment date cannot be in the past.',
            'appointment_time.required' => 'Please select an available time slot.',
        ];
    }
}
