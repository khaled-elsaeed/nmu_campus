<?php

namespace App\Http\Requests\Resident;

use Illuminate\Foundation\Http\FormRequest;

class StaffStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_en'          => 'required|string|max:255',
            'name_ar'          => 'nullable|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email',
            'password'         => 'required|string|min:8|confirmed',
            'department_id'    => 'nullable|integer|exists:departments,id',
            'faculty_id'       => 'nullable|integer|exists:faculties,id',
            'staff_category_id'=> 'required|integer|exists:staff_categories,id',
            'gender'           => 'required|in:male,female,other',
            'active'           => 'required|boolean',
        ];
    }
} 