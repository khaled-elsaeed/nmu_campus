<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true; // Adjust authorization as needed
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'period' => ['required', Rule::in(['long', 'short'])],
            'accommodation_type' => ['required', Rule::in(['room', 'apartment'])],
            'accommodation_id' => ['required', 'integer'],
            'academic_term_id' => ['required_if:period,long', 'nullable', 'integer', 'exists:academic_terms,id'],
            'check_in_date' => ['required_if:period,short', 'nullable', 'date'],
            'check_out_date' => ['required_if:period,short', 'nullable', 'date', 'after:check_in_date'],
            'status' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'double_room_bed_option' => ['nullable', 'string', 'required_if:accommodation_type,room'],
            'equipment' => ['nullable', 'array'],
            'equipment.*.equipment_id' => ['required_with:equipment', 'integer', 'exists:equipment,id'],
            'equipment.*.quantity' => ['nullable', 'integer', 'min:1'],
            'payment' => ['nullable', 'array'],
            'payment.amount' => ['nullable', 'numeric', 'min:0'],
            'payment.status' => ['nullable', 'string'],
            'payment.notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages()
    {
        return [
            'period.required' => 'Reservation period is required.',
            'period.in' => 'Reservation period must be either long or short.',
            'accommodation_type.required' => 'Accommodation type is required.',
            'accommodation_type.in' => 'Accommodation type must be either room or apartment.',
            'accommodation_id.required' => 'Accommodation ID is required.',
            'academic_term_id.required_if' => 'Academic term is required for long-term reservations.',
            'check_in_date.required_if' => 'Check-in date is required for short-term reservations.',
            'check_out_date.required_if' => 'Check-out date is required for short-term reservations.',
            'check_out_date.after' => 'Check-out date must be after check-in date.',
            'equipment.*.equipment_id.required_with' => 'Equipment ID is required for each equipment item.',
            'equipment.*.equipment_id.exists' => 'Selected equipment does not exist.',
            'equipment.*.quantity.min' => 'Equipment quantity must be at least 1.',
        ];
    }
} 