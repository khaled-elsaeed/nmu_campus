<?php

namespace App\Http\Requests\Resident;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\StaffCategory;
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
        return [
            'name_en'           => 'required|string|max:255',
            'name_ar'           => 'nullable|string|max:255',
            'email'             => 'required|email|max:255|unique:users,email',
            'staff_category_id' => 'required|integer|exists:staff_categories,id',
            'unit_id'           => [
                'required_with:staff_category_id',
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!$value) return;

                    $staffCategory = StaffCategory::find($this->input('staff_category_id'));

                    if (!$staffCategory) {
                        $fail('Invalid staff category.');
                        return;
                    }

                    $valid = match ($staffCategory->type) {
                        'faculty'        => Faculty::find($value),
                        'administrative' => Department::find($value),
                        'campus'         => CampusUnit::find($value),
                        default          => false,
                    };

                    if (!$valid) {
                        $fail("Selected unit does not exist for type '{$staffCategory->type}'.");
                    }
                }
            ],
            'gender'       => 'required|in:male,female,other',
            'notes'        => 'nullable|string',
            'national_id'  => 'required|string|unique:staff,national_id',
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
            $staffCategory = StaffCategory::find($this->input('staff_category_id'));

            if (
                $staffCategory &&
                in_array($staffCategory->type, ['faculty', 'administrative', 'campus']) &&
                !$this->filled('unit_id')
            ) {
                $validator->errors()->add('unit_id', 'Unit must be selected for this staff category.');
            }
        });
    }
}
