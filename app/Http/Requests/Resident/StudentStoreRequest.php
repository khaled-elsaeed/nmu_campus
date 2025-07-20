<?php

namespace App\Http\Requests\Resident;

use Illuminate\Foundation\Http\FormRequest;

class StudentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_id'      => 'required|string|max:255|unique:students,academic_id',
            'national_id'      => 'required|string|max:255|unique:students,national_id',
            'name_en'          => 'required|string|max:255',
            'name_ar'          => 'nullable|string|max:255',
            'academic_email'   => 'required|email|max:255|unique:students,academic_email|unique:students,academic_email',
            'phone'            => 'required|string|max:20',
            'date_of_birth'    => 'required|date',
            'gender'           => 'required|in:male,female,other',
            'academic_year'    => 'required|string|max:50',
            'faculty_id'       => 'required|integer|exists:faculties,id',
            'program_id'       => 'required|integer|exists:programs,id',
            'governorate_id'   => 'required|integer|exists:governorates,id',
            'city_id'          => 'required|integer|exists:cities,id',
            'address'          => 'required|string|max:255',
        ];
    }
} 