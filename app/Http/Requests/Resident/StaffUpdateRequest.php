<?php

namespace App\Http\Requests\Resident;

use Illuminate\Foundation\Http\FormRequest;

class StaffUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $staffId = $this->route('staff') ? $this->route('staff')->id : $this->route('id');
        return [
            'staff_id'         => 'required|string|max:255|unique:staff,staff_id,' . $staffId,
            'name_en'          => 'required|string|max:255',
            'name_ar'          => 'nullable|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email,' . $staffId,
            'password'         => 'nullable|string|min:8|confirmed',
            'department_id'    => 'nullable|integer|exists:departments,id',
            'faculty_id'       => 'nullable|integer|exists:faculties,id',
            'staff_category_id'=> 'required|integer|exists:staff_categories,id',
            'gender'           => 'required|in:male,female,other',
            'active'           => 'required|boolean',
        ];
    }
} 