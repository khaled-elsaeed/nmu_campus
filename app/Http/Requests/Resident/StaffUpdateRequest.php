<?php

namespace App\Http\Requests\Resident;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\StaffCategory;
use App\Models\Resident\Staff;
use App\Models\Academic\Faculty;
use App\Models\Department;
use App\Models\CampusUnit;

class StaffUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $staffId = $this->route('id'); // Use consistent route key
        $staff   = Staff::find($staffId);
        $userId  = $staff?->user_id;

        return [
            'name_en'           => 'required|string|max:255',
            'name_ar'           => 'nullable|string|max:255',
            'email'             => 'required|email|max:255|unique:users,email,' . ($userId ?? 'NULL'),
            'staff_category_id' => 'required|integer|exists:staff_categories,id',
            'unit_id'           => [
                'required_with:staff_category_id',
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!$value) return;

                    $categoryId = $this->input('staff_category_id');
                    if (!$categoryId) return;

                    $category = StaffCategory::find($categoryId);
                    if (!$category) {
                        $fail('Invalid staff category.');
                        return;
                    }

                    $valid = match ($category->type) {
                        'faculty'        => Faculty::find($value),
                        'administrative' => Department::find($value),
                        'campus'         => CampusUnit::find($value),
                        default          => false,
                    };

                    if (!$valid) {
                        $fail("Selected {$category->type} unit does not exist.");
                    }
                }
            ],
            'gender'            => 'required|in:male,female,other',
            'notes'             => 'nullable|string',
            'national_id'       => 'required|string|unique:staff,national_id,' . $staffId,
        ];
    }

    public function messages(): array
    {
        return [
            'unit_id.required_with' => 'Unit must be selected for this staff category.',
            'unit_id.integer'       => 'Unit ID must be a valid number.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $categoryId = $this->input('staff_category_id');
            $unitId     = $this->input('unit_id');

            if ($categoryId && !$unitId) {
                $category = StaffCategory::find($categoryId);
                if ($category && in_array($category->type, ['faculty', 'administrative', 'campus'])) {
                    $validator->errors()->add('unit_id', 'Unit must be selected for this staff category.');
                }
            }
        });
    }
}
