<?php

namespace App\Http\Requests\Resident;

use Illuminate\Foundation\Http\FormRequest;

class StudentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $studentId = $this->getStudentId();

        return [
            // Academic Information
            'academic_id' => [
                'required',
                'digits:9',
                'unique:students,academic_id,' . $studentId,
            ],
            'level' => [
                'required',
                'integer',
                'between:1,5',
            ],
            'faculty_id' => [
                'required',
                'integer',
                'exists:faculties,id',
            ],
            'program_id' => [
                'required',
                'integer',
                'exists:programs,id',
            ],

            // Personal Information
            'national_id' => [
                'required',
                'string',
                'max:255',
                'unique:students,national_id,' . $studentId,
                function ($attribute, $value, $fail) {
                    $this->validateNationalIdOrPassport($attribute, $value, $fail);
                }
            ],
            'name_en' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
            'name_ar' => [
                'nullable',
                'string',
                'min:2',
                'max:255',
            ],
            'date_of_birth' => [
                'required',
                'date',
            ],

            // Contact Information
            'academic_email' => [
                'required',
                'string',
                'max:255',
                'unique:students,academic_email,' . $studentId,
                'unique:users,email,' . $studentId,
                function ($attribute, $value, $fail) {
                    $this->validateAcademicEmail($attribute, $value, $fail);
                }
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $this->validateEgyptianMobileNumber($attribute, $value, $fail);
                }
            ],

            // Address Information
            'governorate_id' => [
                'required',
                'integer',
                'exists:governorates,id',
            ],
            'city_id' => [
                'required',
                'integer',
                'exists:cities,id',
            ],
            'address' => [
                'required',
                'string',
                'max:255',
            ],

            // Status
            'is_profile_complete' => [
                'required',
                'boolean',
            ],
        ];
    }

    /**
     * Get the student ID from the route.
     */
    private function getStudentId(): ?int
    {
        return $this->route('student') ? $this->route('student')->id : $this->route('id');
    }

    /**
     * Validate national ID or passport format.
     */
    private function validateNationalIdOrPassport(string $attribute, string $value, callable $fail): void
    {
        // Egyptian national ID: 14 digits
        $isNationalId = preg_match('/^\d{14}$/', $value);
        
        // Passport: 8-9 alphanumeric characters (at least one letter)
        $isPassport = preg_match('/^(?=.*[A-Za-z])[A-Za-z0-9]{8,9}$/', $value);
        
        if (!$isNationalId && !$isPassport) {
            $fail(__('The :attribute must be a valid Egyptian national ID (14 digits) or a valid passport number (8-9 alphanumeric characters).'));
        }
    }

    /**
     * Validate academic email format (e.g., ahmed221144154@nmu.edu.eg).
     */
    private function validateAcademicEmail(string $attribute, string $value, callable $fail): void
    {
        // Ensure the email matches: letters + 9 digits + @nmu.edu.eg
        if (!preg_match('/^[a-zA-Z]+[0-9]{9}@nmu\.edu\.eg$/i', strtolower($value))) {
            $fail(__(
                'The :attribute must be a valid NMU academic email in the format nameacademicid@nmu.edu.eg (e.g., ahmed221144154@nmu.edu.eg).'
            ));
        }
    }


    /**
     * Validate Egyptian mobile number format.
     */
    private function validateEgyptianMobileNumber(string $attribute, string $value, callable $fail): void
    {
        // Egyptian mobile number: starts with 01, followed by 9 digits (total 11 digits)
        if (!preg_match('/^01[0-9]{9}$/', $value)) {
            $fail(__('The :attribute must be a valid Egyptian mobile number (e.g., 01XXXXXXXXX).'));
        }
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'academic_id' => 'Academic ID',
            'national_id' => 'National ID/Passport',
            'name_en' => 'Name (English)',
            'name_ar' => 'Name (Arabic)',
            'academic_email' => 'Academic Email',
            'phone' => 'Phone Number',
            'date_of_birth' => 'Date of Birth',
            'level' => 'Academic Level',
            'faculty_id' => 'Faculty',
            'program_id' => 'Program',
            'governorate_id' => 'Governorate',
            'city_id' => 'City',
            'address' => 'Address',
            'is_profile_complete' => 'Profile Completion Status',
        ];
    }
}