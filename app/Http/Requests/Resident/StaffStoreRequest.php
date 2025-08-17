<?php

namespace App\Http\Requests\Resident;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Academic\Faculty;
use App\Models\Department;
use App\Models\CampusUnit;

class StaffStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name_en'           => 'required|string|max:255',
            'name_ar'           => 'nullable|string|max:255',
            'email'             => 'required|email|max:255|unique:users,email',
            'unit_type'        => 'required|in:faculty,administrative,campus',
            'unit_id'           => 'required|integer',
            'gender'            => 'required|in:male,female,other',
            'notes'             => 'nullable|string',
            'national_id'      => 'required|string|unique:staff,national_id',
        ];

        // Dynamic unit_id validation
        switch ($this->input('unit_type')) {
            case 'faculty':
                $rules['unit_id'] .= '|exists:faculties,id';
                break;
            case 'administrative':
                $rules['unit_id'] .= '|exists:departments,id';
                break;
            case 'campus':
                $rules['unit_id'] .= '|exists:campus_units,id';
                break;
        }
        return $rules;
    }
}
