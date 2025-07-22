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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $rules = [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'period_type' => ['required', Rule::in(['academic', 'calendar'])],
            'accommodation_type' => ['required', Rule::in(['room', 'apartment'])],
            'accommodation_id' => ['required', 'integer'],
            'status' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            // Custom validation for double_room_bed_option
            'double_room_bed_option' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (
                        $this->input('accommodation_type') === 'room' &&
                        ($this->input('room_type') === 'double' || $this->input('is_double_room')) &&
                        empty($value)
                    ) {
                        $fail('The double room bed option field is required when accommodation type is room and the room is a double room.');
                    }
                }
            ],
            'equipment' => ['nullable', 'array'],
            'equipment.*.equipment_id' => ['required_with:equipment', 'integer', 'exists:equipment,id'],
            'equipment.*.quantity' => ['nullable', 'integer', 'min:1'],
            'payment' => ['nullable', 'array'],
            'payment.amount' => ['nullable', 'numeric', 'min:0'],
            'payment.status' => ['nullable', 'string'],
            'payment.notes' => ['nullable', 'string'],
        ];

        // period_type is either 'academic' or 'calendar'
        if ($this->input('period_type') === 'academic') {
            $rules['academic_term_id'] = ['required', 'integer', 'exists:academic_terms,id'];
            $rules['check_in_date'] = ['nullable', 'date'];
            $rules['check_out_date'] = ['nullable', 'date', 'after:check_in_date'];
        } elseif ($this->input('period_type') === 'calendar') {
            $rules['academic_term_id'] = ['nullable', 'integer', 'exists:academic_terms,id'];
            $rules['check_in_date'] = ['required', 'date'];
            $rules['check_out_date'] = ['required', 'date', 'after:check_in_date'];
        } else {
            $rules['academic_term_id'] = ['nullable', 'integer', 'exists:academic_terms,id'];
            $rules['check_in_date'] = ['nullable', 'date'];
            $rules['check_out_date'] = ['nullable', 'date', 'after:check_in_date'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages()
    {
        return [
            'period_type.required' => 'Reservation period is required.',
            'period_type.in' => 'Reservation period must be either academic or calendar.',
            'accommodation_type.required' => 'Accommodation type is required.',
            'accommodation_type.in' => 'Accommodation type must be either room or apartment.',
            'accommodation_id.required' => 'Accommodation ID is required.',
            'academic_term_id.required' => 'Academic term is required for academic period reservations.',
            'academic_term_id.exists' => 'Selected academic term does not exist.',
            'check_in_date.required' => 'Check-in date is required for calendar period reservations.',
            'check_in_date.date' => 'Check-in date must be a valid date.',
            'check_out_date.required' => 'Check-out date is required for calendar period reservations.',
            'check_out_date.date' => 'Check-out date must be a valid date.',
            'check_out_date.after' => 'Check-out date must be after check-in date.',
            'equipment.*.equipment_id.required_with' => 'Equipment ID is required for each equipment item.',
            'equipment.*.equipment_id.exists' => 'Selected equipment does not exist.',
            'equipment.*.quantity.min' => 'Equipment quantity must be at least 1.',
        ];
    }
} 