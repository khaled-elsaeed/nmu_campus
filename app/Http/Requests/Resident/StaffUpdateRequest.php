<?php

namespace App\Http\Requests\Resident;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\StaffCategory;

class StaffUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $staffId = $this->route('id');
        $userId = null;
        if ($staffId) {
            $staff = \App\Models\Resident\Staff::find($staffId);
            $userId = $staff ? $staff->user_id : null;
        }
        return [
            'name_en'          => 'required|string|max:255',
            'name_ar'          => 'nullable|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email,' . ($userId ?? 'NULL'),
            'staff_category_id'=> 'required|integer|exists:staff_categories,id',
            'unit_id'          => [
                'required_with:staff_category_id',
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        return; // Allow null values
                    }
                    
                    $staffCategoryId = $this->input('staff_category_id');
                    if (!$staffCategoryId) {
                        return;
                    }
                    
                    $staffCategory = StaffCategory::find($staffCategoryId);
                    if (!$staffCategory) {
                        $fail('Invalid staff category.');
                        return;
                    }
                    
                    // Validate unit_id based on staff category type
                    switch ($staffCategory->type) {
                        case 'faculty':
                            if (!\App\Models\Academic\Faculty::find($value)) {
                                $fail('Selected faculty does not exist.');
                            }
                            break;
                        case 'administrative':
                            if (!\App\Models\Department::find($value)) {
                                $fail('Selected department does not exist.');
                            }
                            break;
                        case 'campus':
                            if (!\App\Models\CampusUnit::find($value)) {
                                $fail('Selected campus unit does not exist.');
                            }
                            break;
                        default:
                            $fail('Invalid staff category type.');
                    }
                }
            ],
            'gender'           => 'required|in:male,female,other',
            'notes'            => 'nullable|string',
            'national_id' => 'required|string|unique:staff,national_id,' . $this->route('staff'),
        ];
    }

    public function messages(): array
    {
        return [
            'unit_id.required_with' => 'Unit must be selected for this staff category.',
            'unit_id.integer' => 'Unit ID must be a valid number.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $staffCategoryId = $this->input('staff_category_id');
            $unitId = $this->input('unit_id');
            
            if ($staffCategoryId && !$unitId) {
                $staffCategory = StaffCategory::find($staffCategoryId);
                if ($staffCategory && in_array($staffCategory->type, ['faculty', 'administrative', 'campus'])) {
                    $validator->errors()->add('unit_id', 'Unit must be selected for this staff category.');
                }
            }
        });
    }
} 