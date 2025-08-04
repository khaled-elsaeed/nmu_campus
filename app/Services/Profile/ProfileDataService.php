<?php

namespace App\Services\Profile;

use App\Models\Resident\Student;
use App\Models\StudentArchive;
use App\Exceptions\BusinessValidationException;

class ProfileDataService
{
    /**
     * The country code for Egypt, used to determine if parent is local or abroad.
     */
    protected const EGYPT_COUNTRY_CODE = 'EG';

    /**
     * @var LookupService
     */
    private $lookupService;

    public function __construct(LookupService $lookupService)
    {
        $this->lookupService = $lookupService;
    }

    /**
     * Get profile data for the current authenticated user
     *
     * @return array
     */
    public function getProfileData(): array
    {
        $user = auth()->user();

        // First check if user has an active student record
        if ($user?->student) {
            return $this->getProfileDataFromStudent($user->student);
        }

        // If no student record, check for archive data
        $studentArchive = StudentArchive::where('user_id', $user->id)->first();
        
        if ($studentArchive) {
            return $this->getProfileDataFromArchive($studentArchive);
        }

        // If neither student nor archive data exists, throw exception
        throw new BusinessValidationException('No student record found.');
    }

    /**
     * Get profile data from student record
     *
     * @param Student $student
     * @return array
     */
    private function getProfileDataFromStudent($student): array
    {
        return [
            'personal_info' => $this->getPersonalInfoFromStudent($student),
            'contact_info' => $this->getContactInfoFromStudent($student),
            'academic_info' => $this->getAcademicInfoFromStudent($student),
            'parent_info' => $this->getParentInfoFromStudent($student),
            'sibling_info' => $this->getSiblingInfoFromStudent($student),
            'emergency_contact' => $this->getEmergencyContact($student),
        ];
    }

    /**
     * Get profile data from archive record
     *
     * @param StudentArchive $studentArchive
     * @return array
     */
    private function getProfileDataFromArchive(StudentArchive $studentArchive): array
    {
        return [
            'personal_info' => $this->getPersonalInfoFromArchive($studentArchive),
            'contact_info' => $this->getContactInfoFromArchive($studentArchive),
            'academic_info' => $this->getAcademicInfoFromArchive($studentArchive),
            'parent_info' => $this->getParentInfoFromArchive($studentArchive),
            'sibling_info' => $this->getSiblingInfoFromArchive($studentArchive),
        ];
    }

    /**
     * Get personal information from student record
     *
     * @param Student $student
     * @return array
     */
    private function getPersonalInfoFromStudent($student): array
    {
        return [
            'national_id' => $student?->national_id,
            'name_ar' => $student?->name_ar,
            'name_en' => $student?->name_en,
            'birthdate' => $student?->date_of_birth,
            'gender' => $student?->gender ?? 'male',
            'nationality_id' => $student?->nationality_id,
        ];
    }

    /**
     * Get contact information from student record
     *
     * @param Student $student
     * @return array
     */
    private function getContactInfoFromStudent($student): array
    {
        return [
            'phone' => $student?->phone,
            'governorate_id' => $student?->governorate_id,
            'city_id' => $student?->city_id,
            'street' => $student?->street,
        ];
    }

    /**
     * Get academic information from student record
     *
     * @param Student $student
     * @return array
     */
    private function getAcademicInfoFromStudent($student): array
    {
        return [
            'academic_id' => $student?->academic_id,
            'faculty_id' => $student?->faculty_id,
            'program_id' => $student?->program_id,
            'academic_year' => $student?->level,
            'gpa' => $student?->cum_gpa ?? 0.0,
            'score' => $student?->score,
            'academic_email' => $student?->academic_email,
            'actual_score' => null,
            'actual_percent' => null,
            'certificate_type' => null,
            'certificate_country' => null,
            'certificate_country_id' => null,
            'certificate_year' => null,
        ];
    }

    /**
     * Get parent information from student record
     *
     * @param Student $student
     * @return array
     */
    private function getParentInfoFromStudent($student): array
    {
        $parent = $student?->user?->parent;
        
        if (!$parent) {
            return [
                'relation' => null,
                'name_en' => null,
                'name_ar' => null,
                'national_id' => null,
                'phone' => null,
                'email' => null,
                'is_abroad' => false,
                'governorate_id' => null,
                'city_id' => null,
                'country_id' => null,
            ];
        }

        return [
            'relation' => $parent?->relation,
            'name_en' => $parent?->name_en,
            'name_ar' => $parent?->name_ar,
            'national_id' => $parent?->national_id,
            'phone' => $parent?->phone,
            'email' => $parent?->email,
            'is_abroad' => $parent?->is_abroad ?? false,
            'governorate_id' => $parent?->governorate_id,
            'city_id' => $parent?->city_id,
            'country_id' => $parent?->country_id,
        ];
    }

    /**
     * Get sibling information from student record
     *
     * @param Student $student
     * @return array
     */
    private function getSiblingInfoFromStudent($student): array
    {
        $sibling = $student?->user?->sibling;
        
        if (!$sibling) {
            return [
                'has_sibling_in_dorm' => 'no',
                'name_en' => null,
                'name_ar' => null,
                'national_id' => null,
                'gender' => null,
                'date_of_birth' => null,
                'relationship' => null,
                'academic_level' => null,
                'notes' => null,
                'faculty_id' => null,
            ];
        }

        return [
            'has_sibling_in_dorm' => 'yes',
            'name_en' => $sibling?->name_en,
            'name_ar' => $sibling?->name_ar,
            'national_id' => $sibling?->national_id,
            'gender' => $sibling?->gender,
            'date_of_birth' => $sibling?->date_of_birth,
            'relationship' => $sibling?->relationship,
            'academic_level' => $sibling?->academic_level,
            'notes' => $sibling?->notes,
            'faculty_id' => $sibling?->faculty_id,
        ];
    }

    /**
     * Get emergency contact information from student record
     *
     * @param Student $student
     * @return array|null
     */
    private function getEmergencyContact($student): ?array
    {
        $emergencyContact = $student?->user?->emergencyContact;

        if (!$emergencyContact) {
            return null;
        }

        return [
            'name_en' => $emergencyContact->name_en ?? null,
            'name_ar' => $emergencyContact->name_ar ?? null,
            'phone' => $emergencyContact->phone ?? null,
            'relationship' => $emergencyContact->relationship ?? null,
            'governorate_id' => $emergencyContact->governorate_id ?? null,
            'city_id' => $emergencyContact->city_id ?? null,
            'street' => $emergencyContact->street ?? null,
            'notes' => $emergencyContact->notes ?? null,
        ];
    }

    /**
     * Get personal information from archive record
     *
     * @param StudentArchive $studentArchive
     * @return array
     */
    private function getPersonalInfoFromArchive(StudentArchive $studentArchive): array
    {
        return [
            'national_id' => $studentArchive?->national_id,
            'name_ar' => $studentArchive?->name_ar,
            'name_en' => $studentArchive?->name_en,
            'birthdate' => $studentArchive?->birthdate,
            'gender' => $studentArchive?->gender ?? 'male',
            'nationality_id' => $this->lookupService->getNationalityId($studentArchive?->nationality_name),
        ];
    }

    /**
     * Get contact information from archive record
     *
     * @param StudentArchive $studentArchive
     * @return array
     */
    private function getContactInfoFromArchive(StudentArchive $studentArchive): array
    {
        return [
            'phone' => $studentArchive?->phone,
            'governorate_id' => $this->lookupService->getGovernorateId($studentArchive?->govern),
            'city_id' => $this->lookupService->getCityId($studentArchive?->city),
            'street' => $studentArchive?->street,
        ];
    }

    /**
     * Get academic information from archive record
     *
     * @param StudentArchive $studentArchive
     * @return array
     */
    private function getAcademicInfoFromArchive(StudentArchive $studentArchive): array
    {
        $certificateCountry = $this->lookupService->getCountry($studentArchive?->cert_country_name);
        return [
            'academic_id' => $studentArchive?->academic_id,
            'faculty_id' => $this->lookupService->getFacultyId($studentArchive?->candidated_faculty_name),
            'program_id' => null,
            'academic_year' => null,
            'gpa' => $studentArchive?->cum_gpa ?? 0.0,
            'score' => $studentArchive?->actual_score ?? 0.0,
            'gpa_available' => $studentArchive?->cum_gpa ?? false,
            'academic_email' => $studentArchive?->academic_email,
            'actual_score' => $studentArchive?->actual_score,
            'actual_percent' => $studentArchive?->actual_percent,
            'certificate_type' => $studentArchive?->certificate_type_name,
            'certificate_country' => $studentArchive?->cert_country_name,
            'certificate_country_id' => $certificateCountry?->id,
            'certificate_year' => $studentArchive?->cert_year_name,
        ];
    }

    /**
     * Get parent information from archive record
     *
     * @param StudentArchive $studentArchive
     * @return array
     */
    private function getParentInfoFromArchive(StudentArchive $studentArchive): array
    {
        $parentCountry = $this->lookupService->getCountry($studentArchive?->parent_country_name);
        return [
            'name' => $studentArchive?->parent_name,
            'phone' => $studentArchive?->parent_phone,
            'email' => $studentArchive?->parent_email,
            'country_id' => $parentCountry?->id,
            'is_abroad' => isset($parentCountry?->code) && ($parentCountry?->code !== self::EGYPT_COUNTRY_CODE),
        ];
    }

    /**
     * Get sibling information from archive record
     *
     * @param StudentArchive $studentArchive
     * @return array
     */
    private function getSiblingInfoFromArchive(StudentArchive $studentArchive): array
    {
        $data = [
            'has_sibling_in_dorm' => 'no'
        ];

        if ($studentArchive?->brother) {
            $data = [
                'has_sibling_in_dorm' => 'yes',
                'name' => $studentArchive?->brother_name,
                'faculty_id' => $this->lookupService->getFacultyId($studentArchive?->brother_faculty_name),
            ];
        }

        return $data;
    }
}
