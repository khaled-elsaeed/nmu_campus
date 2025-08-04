<?php

namespace App\Http\Requests\Profile\Resident\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\City;
use App\Models\Academic\Program;

class CompleteRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            // ============================================
            // Step 1: Personal Information
            // ============================================
            'name_ar' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[\x{0600}-\x{06FF}\x{0020}]+$/u' // Arabic characters and spaces
            ],
            'name_en' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[A-Za-z\s]+$/' // English letters and spaces
            ],
            'national_id' => [
                'sometimes',
                'string',
                'size:14',
                'regex:/^[0-9]{14}$/' // Exactly 14 digits
            ],
            'birth_date' => [
                'sometimes',
                'date',
                'before:-17 years'
            ],
            'gender' => [
                'sometimes',
                'in:male,female'
            ],
            'nationality' => [
                'sometimes',
                'exists:nationalities,id'
            ],

            // ============================================
            // Step 2: Contact Information
            // ============================================
            'governorate' => [
                'required',
                'exists:governorates,id'
            ],
            'city' => [
                'required',
                'exists:cities,id'
            ],
            'street' => [
                'required',
                'string',
                'min:5',
                'max:255'
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^(010|011|012|015)[0-9]{8}$/' // Egyptian mobile format
            ],

            // ============================================
            // Step 3: Academic Information
            // ============================================
            'faculty' => [
                'required',
                'exists:faculties,id'
            ],
            'program' => [
                'required',
                'exists:programs,id'
            ],
            'academic_year' => [
                'required',
                'integer',
                'between:1,5'
            ],
            'gpa' => [
                'required_if:gpa_available,true',
                'numeric',
                'between:0,4'
            ],
            'gpa_available' => [
                'required',
                'boolean'
            ],
            'score' => [
                'required_if:gpa_available,false',
                'numeric',
                'between:0,100'
            ],
            'academic_id' => [
                'required',
                'string',
                'regex:/^[0-9]{8,12}$/' // 8-12 digits
            ],
            'academic_email' => [
                'required',
                'email',
                'regex:/^[A-Za-z0-9._%+-]+@nmu\.edu\.eg$/' // University email format
            ],

            // ============================================
            // Step 4: Parent Information
            // ============================================
            'parent_relationship' => [
                'required',
                'in:father,mother'
            ],
            'parent_name_ar' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\x{0600}-\x{06FF}\x{0020}]+$/u' // Arabic characters and spaces
            ],
            'parent_name_en' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z\s]+$/' // English letters and spaces
            ],
            'parent_phone' => [
                'required',
                'string'
            ],
            'parent_email' => [
                'nullable',
                'email'
            ],
            'parent_national_id' => [
                'nullable',
                'string',
                'size:14',
                'regex:/^[0-9]{14}$/',
            ],
            'is_parent_abroad' => [
                'required',
                'in:yes,no'
            ],

            // Conditional Parent Fields
            'abroad_country' => [
                'required_if:is_parent_abroad,yes',
                'nullable',
                'exists:countries,id'
            ],
            'living_with_parent' => [
                'required_if:is_parent_abroad,no',
                'nullable',
                'in:yes,no'
            ],
            'parent_governorate' => [
                'required_if:living_with_parent,no',
                'nullable',
                'exists:governorates,id'
            ],
            'parent_city' => [
                'required_if:living_with_parent,no',
                'nullable',
                'exists:cities,id'
            ],

            // ============================================
            // Step 5: Sibling Information
            // ============================================
            'has_sibling_in_dorm' => [
                'required',
                'in:yes,no'
            ],
            'sibling_gender' => [
                'required_if:has_sibling_in_dorm,yes',
                'nullable',
                'in:male,female'
            ],
            'sibling_name_ar' => [
                'required_if:has_sibling_in_dorm,yes',
                'nullable',
                'string',
                'max:255',
                'regex:/^[\x{0600}-\x{06FF}\x{0020}]+$/u' // Arabic characters and spaces
            ],
            'sibling_name_en' => [
                'required_if:has_sibling_in_dorm,yes',
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z\s]+$/' // English letters and spaces
            ],
            'sibling_national_id' => [
                'required_if:has_sibling_in_dorm,yes',
                'nullable',
                'string',
                'size:14',
                'regex:/^[0-9]{14}$/', // Exactly 14 digits
                'different:national_id'
            ],
            'sibling_faculty' => [
                'required_if:has_sibling_in_dorm,yes',
                'nullable',
                'exists:faculties,id'
            ],

            // ============================================
            // Step 6: Emergency Contact
            // ============================================
            'emergency_contact_name_ar' => [
                'required_if:is_parent_abroad,yes',
                'nullable',
                'string',
                'max:255',
                'regex:/^[\x{0600}-\x{06FF}\x{0020}]+$/u' // Arabic characters and spaces
            ],
            'emergency_contact_name_en' => [
                'required_if:is_parent_abroad,yes',
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z\s]+$/' // English letters and spaces
            ],
            'emergency_contact_relationship' => [
                'required_if:is_parent_abroad,yes',
                'nullable',
                'in:father,mother,brother,sister,other'
            ],
            'emergency_contact_phone' => [
                'required_if:is_parent_abroad,yes',
                'nullable',
                'string',
                'regex:/^(010|011|012|015)[0-9]{8}$/', // Egyptian mobile format
                'different:phone'
            ],
            'emergency_contact_governorate' => [
                'required_if:is_parent_abroad,yes',
                'nullable',
                'exists:governorates,id'
            ],
            'emergency_contact_city' => [
                'required_if:is_parent_abroad,yes',
                'nullable',
                'exists:cities,id'
            ],
            'emergency_contact_street' => [
                'required_if:is_parent_abroad,yes',
                'nullable',
                'string',
                'min:5',
                'max:255'
            ],

            // ============================================
            // Step 7: Terms and Conditions
            // ============================================
            'terms_checkbox' => [ // Fixed naming convention
                'required',
                'accepted'
            ],
        ];

        // Dynamic parent phone validation based on abroad status
        if ($this->input('is_parent_abroad') === 'yes') {
            $rules['parent_phone'][] = 'regex:/^\+?[1-9][0-9]{6,15}$/'; // International format
        } else {
            $rules['parent_phone'][] = 'regex:/^(010|011|012|015)[0-9]{8}$/'; // Egyptian format
        }

        return $rules;
    }

    /**
     * Get the validation messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            // ============================================
            // Personal Information Messages
            // ============================================
            'name_ar.regex' => 'Arabic name must contain only Arabic characters and spaces.',
            'name_en.regex' => 'English name must contain only English letters and spaces.',
            'national_id.size' => 'National ID must be exactly 14 digits.',
            'national_id.regex' => 'National ID must contain only numbers.',
            'birth_date.before' => 'You must be at least 17 years old.',

            // ============================================
            // Contact Information Messages
            // ============================================
            'governorate.required' => 'Please select your governorate.',
            'governorate.exists' => 'Selected governorate is invalid.',
            'city.required' => 'Please select your city.',
            'city.exists' => 'Selected city is invalid.',
            'street.required' => 'Street address is required.',
            'street.min' => 'Street address must be at least 5 characters.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Phone number must be a valid Egyptian mobile number (010/011/012/015 followed by 8 digits).',

            // ============================================
            // Academic Information Messages
            // ============================================
            'faculty.required' => 'Please select your faculty.',
            'faculty.exists' => 'Selected faculty is invalid.',
            'program.required' => 'Please select your program.',
            'program.exists' => 'Selected program is invalid.',
            'academic_year.required' => 'Please select your academic year.',
            'academic_year.between' => 'Academic year must be between 1 and 5.',
            'gpa.required' => 'GPA is required.',
            'gpa.between' => 'GPA must be between 0.0 and 4.0.',
            'academic_id.required' => 'Student ID is required.',
            'academic_id.regex' => 'Student ID must be 8-12 digits.',
            'academic_email.required' => 'University email is required.',
            'academic_email.email' => 'Please enter a valid email address.',
            'academic_email.regex' => 'Please use your university email address (@nmu.edu.eg).',

            // ============================================
            // Parent Information Messages
            // ============================================
            'parent_relationship.required' => 'Please select your relationship to the parent.',
            'parent_name_ar.required' => 'Parent\'s Arabic name is required.',
            'parent_name_ar.regex' => 'Parent\'s Arabic name must contain only Arabic characters and spaces.',
            'parent_name_en.required' => 'Parent\'s English name is required.',
            'parent_name_en.regex' => 'Parent\'s English name must contain only English letters and spaces.',
            'parent_phone.required' => 'Parent\'s phone number is required.',
            'parent_phone.regex' => 'Please enter a valid phone number format.',
            'is_parent_abroad.required' => 'Please specify if parent lives abroad.',
            'abroad_country.required_if' => 'Please select the country where your parent lives.',
            'living_with_parent.required_if' => 'Please specify if you live with your parent.',
            'parent_governorate.required_if' => 'Please select parent\'s governorate.',
            'parent_city.required_if' => 'Please select parent\'s city.',

            // ============================================
            // Sibling Information Messages
            // ============================================
            'has_sibling_in_dorm.required' => 'Please specify if you have a sibling in the dorm.',
            'sibling_gender.required_if' => 'Please select sibling\'s gender.',
            'sibling_name_ar.required_if' => 'Sibling\'s Arabic name is required.',
            'sibling_name_ar.regex' => 'Sibling\'s Arabic name must contain only Arabic characters and spaces.',
            'sibling_name_en.required_if' => 'Sibling\'s English name is required.',
            'sibling_name_en.regex' => 'Sibling\'s English name must contain only English letters and spaces.',
            'sibling_national_id.required_if' => 'Sibling\'s National ID is required.',
            'sibling_national_id.size' => 'Sibling\'s National ID must be exactly 14 digits.',
            'sibling_national_id.regex' => 'Sibling\'s National ID must contain only numbers.',
            'sibling_national_id.different' => 'Sibling\'s National ID must be different from yours.',
            'sibling_faculty.required_if' => 'Please select sibling\'s faculty.',

            // ============================================
            // Emergency Contact Messages
            // ============================================
            'emergency_contact_name_ar.required_if' => 'Emergency contact name is required when parent is abroad.',
            'emergency_contact_name_ar.regex' => 'Emergency contact name must contain only Arabic characters and spaces.',
            'emergency_contact_name_en.required_if' => 'Emergency contact name is required when parent is abroad.',
            'emergency_contact_name_en.regex' => 'Emergency contact name must contain only English letters and spaces.',
            'emergency_contact_relationship.required_if' => 'Please specify your relationship to the emergency contact.',
            'emergency_contact_phone.required_if' => 'Emergency contact phone is required when parent is abroad.',
            'emergency_contact_phone.regex' => 'Emergency contact phone must be a valid Egyptian mobile number.',
            'emergency_contact_phone.different' => 'Emergency contact number must be different from your mobile number.',
            'emergency_contact_governorate.required_if' => 'Please select emergency contact\'s governorate.',
            'emergency_contact_governorate.exists' => 'Selected emergency contact governorate is invalid.',
            'emergency_contact_city.required_if' => 'Please select emergency contact\'s city.',
            'emergency_contact_city.exists' => 'Selected emergency contact city is invalid.',
            'emergency_contact_street.required_if' => 'Emergency contact street address is required.',
            'emergency_contact_street.min' => 'Emergency contact street address must be at least 5 characters.',
            'emergency_contact_street.max' => 'Emergency contact street address must not exceed 255 characters.',
            // ============================================
            // Terms Messages
            // ============================================
            'terms_checkbox.required' => 'You must accept the terms and conditions.',
            'terms_checkbox.accepted' => 'You must accept the terms and conditions to proceed.',
            'terms_Checkbox.accepted' => 'You must accept the terms and conditions to proceed.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name_ar' => 'Arabic name',
            'name_en' => 'English name',
            'national_id' => 'National ID',
            'birth_date' => 'Birth date',
            'phone' => 'Phone number',
            'academic_id' => 'Student ID',
            'academic_email' => 'University email',
            'parent_name_ar' => 'Parent Arabic name',
            'parent_name_en' => 'Parent English name',
            'parent_phone' => 'Parent phone',
            'parent_email' => 'Parent email',
            'parent_national_id' => 'Parent National ID',
            'is_parent_abroad' => 'Is parent abroad',
            'abroad_country' => 'Country where parent lives',
            'living_with_parent' => 'Living with parent',
            'parent_governorate' => 'Parent governorate',
            'parent_city' => 'Parent city',
            'governorate' => 'Your governorate',
            'city' => 'Your city',
            'street' => 'Street address',
            'sibling_name_ar' => 'Sibling Arabic name',
            'sibling_name_en' => 'Sibling English name',
            'sibling_national_id' => 'Sibling National ID',
            'emergency_contact_name_ar' => 'Emergency contact name (Arabic)',
            'emergency_contact_name_en' => 'Emergency contact name (English)',
            'emergency_contact_phone' => 'Emergency contact phone',
            'emergency_contact_governorate' => 'Emergency contact governorate',
            'emergency_contact_city' => 'Emergency contact city',
            'emergency_contact_street' => 'Emergency contact street address',
            'terms_checkbox' => 'Terms and conditions',
            'terms_Checkbox' => 'Terms and conditions',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateParentPhoneFormat($validator);
            $this->validateCityGovernorateRelationship($validator);
            $this->validateParentCityGovernorateRelationship($validator);
            $this->validateProgramFacultyRelationship($validator);
        });
    }

    /**
     * Validate parent phone format based on abroad status.
     */
    private function validateParentPhoneFormat($validator): void
    {
        if ($this->input('is_parent_abroad') === 'yes') {
            $phone = $this->input('parent_phone');
            if ($phone && !preg_match('/^\+?[1-9][0-9]{6,15}$/', $phone)) {
                $validator->errors()->add('parent_phone', 'Please enter a valid international phone number.');
            }
        }
    }

    /**
     * Validate that city belongs to selected governorate.
     */
    private function validateCityGovernorateRelationship($validator): void
    {
        if ($this->input('governorate') && $this->input('city')) {
            $city = City::find($this->input('city'));
            if ($city && $city->governorate_id != $this->input('governorate')) {
                $validator->errors()->add('city', 'Selected city does not belong to the selected governorate.');
            }
        }
    }

    /**
     * Validate that parent city belongs to selected parent governorate.
     */
    private function validateParentCityGovernorateRelationship($validator): void
    {
        if ($this->input('parent_governorate') && $this->input('parent_city')) {
            $city = City::find($this->input('parent_city'));
            if ($city && $city->governorate_id != $this->input('parent_governorate')) {
                $validator->errors()->add('parent_city', 'Selected parent city does not belong to the selected parent governorate.');
            }
        }
    }

    /**
     * Validate that program belongs to selected faculty.
     */
    private function validateProgramFacultyRelationship($validator): void
    {
        if ($this->input('faculty') && $this->input('program')) {
            $program = Program::find($this->input('program'));
            if ($program && $program->faculty_id != $this->input('faculty')) {
                $validator->errors()->add('program', 'Selected program does not belong to the selected faculty.');
            }
        }
    }
}