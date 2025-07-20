<?php

namespace App\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;

class AcademicTermStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'season' => 'required|string|in:fall,spring,summer,winter',
            'year' => [
                'required',
                'regex:/^\d{4}-\d{4}$/',
            ],            'semester_number' => 'required|integer|in:1,2,3',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }
} 