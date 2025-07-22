<?php

namespace App\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class AcademicTermUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');
        return [
            'season' => 'required|string|in:fall,spring,summer,winter',
            'year' => [
                'required',
                'regex:/^\d{4}-\d{4}$/',
            ],
            'semester_number' => 'required|integer|in:1,2,3',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }
} 