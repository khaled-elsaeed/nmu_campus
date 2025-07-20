<?php

namespace App\Http\Requests\Housing;

use Illuminate\Foundation\Http\FormRequest;

class BuildingStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'number' => 'required|string|unique:buildings,number',
            'total_apartments' => 'required|integer|min:1',
            'rooms_per_apartment' => 'required|integer|min:1',
            'gender_restriction' => 'required|in:male,female,mixed',
            'apartments' => 'nullable|array',
            'apartments.*.double_rooms' => 'array',
            'apartments.*.double_rooms.*' => 'integer',
            'has_double_rooms' => 'nullable|in:on,off',
        ];
    }
} 