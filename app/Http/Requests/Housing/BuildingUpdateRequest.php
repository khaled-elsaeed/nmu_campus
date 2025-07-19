<?php

namespace App\Http\Requests\Housing;

use Illuminate\Foundation\Http\FormRequest;

class BuildingUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');
        return [
            'number' => 'required|string|unique:buildings,number,' . $id,
            'gender_restriction' => 'required|in:male,female,mixed',
            'active' => 'required|boolean',
        ];
    }
} 