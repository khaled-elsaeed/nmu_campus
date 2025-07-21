<?php

namespace App\Http\Requests\Reservation\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReservationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'period' => ['required', Rule::in(['long', 'short'])],
            'requested_accommodation_type' => ['required', Rule::in(['room', 'apartment'])],
            'room_type' => ['nullable', Rule::in(['single', 'double'])],
            'requested_double_room_bed_option' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (
                        $this->input('requested_accommodation_type') === 'room' &&
                        $this->input('room_type') === 'double' &&
                        empty($value)
                    ) {
                        $fail('The double room bed option field is required when accommodation type is room and the room is a double room.');
                    }
                }
            ],
            'academic_term_id' => ['required_if:period,long', 'nullable', 'integer', 'exists:academic_terms,id'],
            'requested_check_in_date' => ['required_if:period,short', 'nullable', 'date'],
            'requested_check_out_date' => ['required_if:period,short', 'nullable', 'date', 'after:requested_check_in_date'],
            'resident_notes' => ['nullable', 'string'],
            'admin_notes' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'rejection_reason' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'period.required' => 'Reservation period is required.',
            'period.in' => 'Reservation period must be either long or short.',
            'requested_accommodation_type.required' => 'Accommodation type is required.',
            'requested_accommodation_type.in' => 'Accommodation type must be either room or apartment.',
            'room_type.in' => 'Room type must be either single or double.',
            'academic_term_id.required_if' => 'Academic term is required for long-term reservations.',
            'requested_check_in_date.required_if' => 'Check-in date is required for short-term reservations.',
            'requested_check_out_date.required_if' => 'Check-out date is required for short-term reservations.',
            'requested_check_out_date.after' => 'Check-out date must be after check-in date.',
        ];
    }
} 