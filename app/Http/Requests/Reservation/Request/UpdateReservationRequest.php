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
        $rules = [
            'period_type' => ['required', Rule::in(['academic', 'calendar'])],
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
            'resident_notes' => ['nullable', 'string'],
            'admin_notes' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'rejection_reason' => ['nullable', 'string'],
        ];

        // period_type is either 'academic' or 'calendar'
        if ($this->input('period_type') === 'academic') {
            $rules['academic_term_id'] = ['required', 'integer', 'exists:academic_terms,id'];
            $rules['requested_check_in_date'] = ['nullable', 'date'];
            $rules['requested_check_out_date'] = ['nullable', 'date', 'after:requested_check_in_date'];
        } elseif ($this->input('period_type') === 'calendar') {
            $rules['academic_term_id'] = ['nullable', 'integer', 'exists:academic_terms,id'];
            $rules['requested_check_in_date'] = ['required', 'date'];
            $rules['requested_check_out_date'] = ['required', 'date', 'after:requested_check_in_date'];
        } else {
            $rules['academic_term_id'] = ['nullable', 'integer', 'exists:academic_terms,id'];
            $rules['requested_check_in_date'] = ['nullable', 'date'];
            $rules['requested_check_out_date'] = ['nullable', 'date', 'after:requested_check_in_date'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'period_type.required' => 'Reservation period is required.',
            'period_type.in' => 'Reservation period must be either academic or calendar.',
            'requested_accommodation_type.required' => 'Accommodation type is required.',
            'requested_accommodation_type.in' => 'Accommodation type must be either room or apartment.',
            'room_type.in' => 'Room type must be either single or double.',
            'academic_term_id.required' => 'Academic term is required for academic period reservations.',
            'requested_check_in_date.required' => 'Check-in date is required for calendar period reservations.',
            'requested_check_out_date.required' => 'Check-out date is required for calendar period reservations.',
            'requested_check_out_date.after' => 'Check-out date must be after check-in date.',
        ];
    }
} 