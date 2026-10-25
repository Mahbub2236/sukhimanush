<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorChamberRequest extends FormRequest
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
            'chamber_name' => 'required|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:1000',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'upazila' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'visiting_days' => 'nullable|array',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'slot_duration_minutes' => 'nullable|integer|min:5|max:120',
            'consultation_fee' => 'nullable|numeric|min:0',
            'is_primary' => 'nullable|boolean',
        ];
    }
}
