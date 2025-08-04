<?php

namespace App\Services;

use App\Models\Resident\Student;
use App\Models\StudentParent;
use App\Models\Sibling;
use App\Models\EmergencyContact;
use App\Models\StudentArchive;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Country;
use App\Models\Nationality;
use App\Models\Academic\Faculty;
use App\Exceptions\BusinessValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProfileService
{
    /**
     * The country code for Egypt, used to determine if parent is local or abroad.
     */
    protected const EGYPT_COUNTRY_CODE = 'EG';

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
            'street' => $student?->street, // Not available in student model
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
            'gpa' => 0.0,
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
                'name' => null,
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
            'name' => $parent?->name_en ?? $parent?->name_ar,
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
                'name' => null,
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
            'name' => $sibling?->name_en ?? $sibling?->name_ar,
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
            'nationality_id' => $this->getNationalityId($studentArchive?->nationality_name),
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
            'governorate_id' => $this->getGovernorateId($studentArchive?->govern),
            'city_id' => $this->getCityId($studentArchive?->city),
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
        $certificateCountry = $this->getCountry($studentArchive?->cert_country_name);
        return [
            'academic_id' => $studentArchive?->academic_id,
            'faculty_id' => $this->getFacultyId($studentArchive?->candidated_faculty_name),
            'program_id' => null,
            'academic_year' => null,
            'gpa' => $studentArchive?->cum_gpa ?? 0.0,
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
        $parentCountry = $this->getCountry($studentArchive?->parent_country_name);
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
                'faculty_id' => $this->getFacultyId($studentArchive?->brother_faculty_name),
                'faculty_name' => $studentArchive?->brother_faculty_name,
            ];
        }

        return $data;
    }

    /**
     * Get empty profile structure
     *
     * @return array
     */
    private function getEmptyProfileStructure(): array
    {
        return [
            'personal_info' => [],
            'contact_info' => [],
            'academic_info' => [],
            'parent_info' => [],
            'sibling_info' => [
                'has_sibling_in_dorm' => 'no'
            ],
            'emergency_contact' => [],
            'terms' => [
                'terms_accepted' => false
            ],
        ];
    }

    /**
     * Save profile data for the current authenticated user
     *
     * @param array $data
     * @return array
     */
    public function saveProfileData(array $data): array
    {
        $user = Auth::user();

        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $studentArchive = $user?->studentArchive;

        $user = $studentArchive?->user;

        return DB::transaction(function () use ($user, $data, $studentArchive) {
            $student = Student::where('user_id', $user?->id)->first();
            if (!$student) {
                $student = new Student();
                $student->user_id = $user?->id;
            }

            // Update all sections
            $this->updateStudentBasicInfo($student, $data, $studentArchive);
            $this->updateParentInfo($student, $data);
            $this->updateSiblingInfo($student, $data);
            $this->updateEmergencyContact($student, $data);

            $student?->save();

            return [
                'student_id' => $student?->id,
                'message' => 'Profile saved successfully'
            ];
        });
    }

    /**
     * Update student basic information
     *
     * @param Student $student
     * @param array $data
     * @param StudentArchive|null $studentArchive
     */
    private function updateStudentBasicInfo(Student $student, array $data, ?StudentArchive $studentArchive): void
    {
        $student->phone = $data['phone'] ?? null;
        $student->governorate_id = $data['governorate'] ?? null;
        $student->city_id = $data['city'] ?? null;
        $student->street = $data['street'] ?? null;
        $student->faculty_id = $data['faculty'] ?? null;
        $student->program_id = $data['program'] ?? null;
        $student->level = $data['academic_year'] ?? null;
        $student->is_profile_complete = true;

        // Use archive data for sensitive student information
        $student->name_ar = $studentArchive?->name_ar;
        $student->name_en = $studentArchive?->name_en;
        $student->national_id = $studentArchive?->national_id;

        $student->date_of_birth = $studentArchive?->birthdate;
        $student->gender = $studentArchive?->gender;
        $student->academic_email = $studentArchive?->email;
        $student->academic_id = $studentArchive?->academic_id;
        $student->faculty_id = $this->getFacultyId($studentArchive?->candidated_faculty_name);
        $student->nationality_id = $this->getNationalityId($studentArchive?->nationality_name);

    }

    /**
     * Update parent information
     *
     * @param Student $student
     * @param array $data
     */
    private function updateParentInfo(Student $student, array $data): void
    {
        $parent = $student?->user?->parent;

        if (!$parent) {
            $parent = new StudentParent();
            $parent->user_id = $student?->user_id;
        }

        $parent->relation = $data['parent_relationship'] ?? null;
        $parent->name_en = $data['parent_name_en'] ?? null;
        $parent->name_ar = $data['parent_name_ar'] ?? null;
        $parent->phone = $data['parent_phone'] ?? null;
        $parent->email = $data['parent_email'] ?? null;
        $parent->national_id = $data['parent_national_id'] ?? null;
        $parent->is_abroad = ($data['is_abroad'] ?? false) === true || ($data['is_abroad'] ?? 'no') === 'yes';

        if ($parent->is_abroad) {
            $parent->country_id = $data['parent_country_id'] ?? null;
            $parent->living_with_parent = false;
            $parent->governorate_id = null;
            $parent->city_id = null;
        } else {
            $parent->country_id = null;
            $parent->living_with_parent = ($data['living_with_parent'] ?? 'no') === 'yes';

            if (($data['living_with_parent'] ?? 'no') === 'no') {
                $parent->governorate_id = $data['parent_governorate_id'] ?? null;
                $parent->city_id = $data['parent_city_id'] ?? null;
            } else {
                $parent->governorate_id = null;
                $parent->city_id = null;
            }
        }

        $parent->save();
    }

    /**
     * Update sibling information
     *
     * @param Student $student
     * @param array $data
     * @return void
     */
    private function updateSiblingInfo(Student $student, array $data): void
    {
        $sibling = $student?->user?->sibling;

        if ($data['has_sibling_in_dorm'] === 'no') {
            if ($sibling) {
                $sibling->delete();
            }
            return;
        }

        if ($data['has_sibling_in_dorm'] === 'yes') {
            if (!$sibling) {
                $sibling = new Sibling();
                $sibling->user_id = $student?->user_id;
            }

            $sibling->gender = $data['sibling_gender'] ?? null;
            $sibling->relation = isset($data['sibling_gender']) && $data['sibling_gender'] === 'male' ? 'brother' : 'sister';
            $sibling->name_en = $data['sibling_name_en'] ?? null;
            $sibling->name_ar = $data['sibling_name_ar'] ?? null;
            $sibling->national_id = $data['sibling_national_id'] ?? null;
            $sibling->faculty_id = $data['sibling_faculty_id'] ?? null;

            $sibling->save();
        }
    }

    /**
     * Update emergency contact information
     *
     * @param Student $student
     * @param array $data
     * @return void
     */
    private function updateEmergencyContact(Student $student, array $data): void
    {
        $emergencyContact = $student?->user?->emergencyContact;

        if ($data['is_parent_abroad'] === 'no') {
            if ($emergencyContact) {
                $emergencyContact->delete();
            }
            return;
        }

        if ($data['is_parent_abroad'] === 'yes') {
            if (!$emergencyContact) {
                $emergencyContact = new EmergencyContact();
                $emergencyContact->user_id = $student?->user_id;
            }

            $emergencyContact->relation = $data['emergency_contact_relationship'] ?? null;
            $emergencyContact->name = $data['emergency_contact_name'] ?? null;
            $emergencyContact->phone_number = $data['emergency_contact_phone'] ?? null;

            $emergencyContact->save();
        }
    }


    /**
     * Get governorate ID by name
     *
     * @param string|null $governorateName
     * @return int|null
     */
    private function getGovernorateId(?string $governorateName): ?int
    {
        if (!$governorateName) {
            return null;
        }

        $governorate = Governorate::where('name_ar', $governorateName)
            ->orWhere('name_en', $governorateName)
            ->first();

        return $governorate?->id;
    }

    /**
     * Get city ID by name
     *
     * @param string|null $cityName
     * @return int|null
     */
    private function getCityId(?string $cityName): ?int
    {
        if (!$cityName) {
            return null;
        }

        $city = City::where('name_ar', $cityName)
            ->orWhere('name_en', $cityName)
            ->first();

        return $city?->id;
    }

    /**
     * Get faculty ID by name
     *
     * @param string|null $facultyName
     * @return int|null
     */
    private function getFacultyId(?string $facultyName): ?int
    {
        if (!$facultyName) {
            return null;
        }

        $faculty = Faculty::where('name_ar', $facultyName)
            ->orWhere('name_en', $facultyName)
            ->first();

        return $faculty?->id;
    }

    /**
     * Get country by name
     *
     * @param string|null $countryName
     * @return Country|null
     */
    private function getCountry(?string $countryName): ?Country
    {
        if (!$countryName) {
            return null;
        }

        return Country::where('name_ar', $countryName)
            ->orWhere('name_en', $countryName)
            ->first();
    }

    /**
     * Get nationality ID by name
     *
     * @param string|null $nationalityName
     * @return int|null
     */
    private function getNationalityId(?string $nationalityName): ?int
    {
        if (!$nationalityName) {
            return null;
        }

        $nationality = Nationality::where('name_ar', $nationalityName)
            ->orWhere('name_en', $nationalityName)
            ->first();

        if (!$nationality) {
            $country = Country::where('name_ar', $nationalityName)
                ->orWhere('name_en', $nationalityName)
                ->first();
            if ($country) {
                $nationality = Nationality::where('code', $country?->code)->first();
            }
        }

        return $nationality?->id;
    }
}
