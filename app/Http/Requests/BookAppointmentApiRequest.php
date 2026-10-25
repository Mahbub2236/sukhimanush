<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BookAppointmentApiRequest extends FormRequest
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
            'doctor_id' => 'required|exists:doctors,id',
            'chamber_id' => 'nullable|integer',
            'patient_name' => 'required|string|min:3|max:100',
            'patient_email' => 'required|email|max:150',
            'patient_phone' => 'required|string|min:7|max:20',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|string',
            'consultation_type' => 'nullable|in:in_chamber,online_video,in_person',
            'family_member_id' => 'nullable|exists:family_members,id',
            'district' => 'nullable|string',
            'reason_for_visit' => 'nullable|string|max:1000',
            'patient_id' => 'nullable|exists:users,id',
            'payment_method' => 'nullable|string',
        ];
    }
}
