<?php

namespace App\Services;

use App\Models\Resident\Student;
use App\Models\StudentParent;
use App\Models\Sibling;
use App\Models\EmergencyContact;
use App\Models\User;
use App\Models\StudentArchive;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Country;
use App\Models\Nationality;
use App\Models\Academic\Faculty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        // Get student archive data if available 
        $studentArchive = StudentArchive::where('national_id', '30308218800598')
            ->first();

        // If no archive data, return empty structure
        if (!$studentArchive) {
            return $this->getEmptyProfileStructure();
        }

        return [
            'personal_info' => $this->getPersonalInfo($studentArchive),
            'contact_info' => $this->getContactInfo($studentArchive),
            'academic_info' => $this->getAcademicInfo($studentArchive),
            'parent_info' => $this->getParentInfo($studentArchive),
            'sibling_info' => $this->getSiblingInfo($studentArchive),
        ];
    }

    /**
     * Get personal information
     *
     * @param StudentArchive $studentArchive
     * @return array
     */
    private function getPersonalInfo(StudentArchive $studentArchive): array
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
     * Get contact information
     *
     * @param StudentArchive $studentArchive
     * @return array
     */
    private function getContactInfo(StudentArchive $studentArchive): array
    {
        return [
            'phone' => $studentArchive?->phone,
            'governorate_id' => $this->getGovernorateId($studentArchive?->govern),
            'city_id' => $this->getCityId($studentArchive?->city),
            'street' => $studentArchive?->street,
        ];
    }

    /**
     * Get academic information
     *
     * @param StudentArchive $studentArchive
     * @return array
     */
    private function getAcademicInfo(StudentArchive $studentArchive): array
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
     * Get parent information
     *
     * @param StudentArchive $studentArchive
     * @return array
     */
    private function getParentInfo(StudentArchive $studentArchive): array
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
     * Get sibling information
     *
     * @param StudentArchive $studentArchive
     * @return array
     */
    private function getSiblingInfo(StudentArchive $studentArchive): array
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

        return DB::transaction(function () use ($user, $data) {
            // Get student archive data for certain fields
            $studentArchive = StudentArchive::where('is_deleted', false)
                ->where('national_id', $data['nationalId'])
                ->first();

            // Get or create student record
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
        $student->national_id = $data['nationalId'];
        $student->phone = $data['phoneNumber'];
        $student->governorate_id = $data['governorate'];
        $student->city_id = $data['city'];
        $student->program_id = $data['program'];
        $student->level = $data['academicYear'];
        $student->is_profile_complete = true;

        // Use archive data for sensitive student information
        $student->name_ar = $studentArchive?->name_ar;
        $student->name_en = $studentArchive?->name_en;
        $student->date_of_birth = $studentArchive?->birthdate;
        $student->gender = $studentArchive?->gender;
        $student->academic_email = $studentArchive?->email;
        $student->academic_id = $studentArchive?->academic_id;
        $student->faculty_id = $this->getFacultyId($studentArchive?->candidated_faculty_name);
        $student->gpa = $studentArchive?->gpa;
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

        // Remove nullsafe operator in write context
        $parent->father_name = $data['fatherName'];
        $parent->mother_name = $data['motherName'];
        $parent->phone_number = $data['parentPhone'];
        $parent->is_abroad = $data['isParentAbroad'] === 'yes';

        if ($data['isParentAbroad'] === 'yes') {
            $parent->country_id = $data['abroadCountry'] ?? null;
            $parent->living_with_parent = false;
            $parent->governorate_id = null;
            $parent->city_id = null;
        } else {
            $parent->country_id = null;
            $parent->living_with_parent = $data['livingWithParent'] === 'yes';

            if ($data['livingWithParent'] === 'no') {
                $parent->governorate_id = $data['parentGovernorate'] ?? null;
                $parent->city_id = $data['parentCity'] ?? null;
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
     */
    private function updateSiblingInfo(Student $student, array $data): void
    {
        if ($data['hasSiblingInDorm'] === 'yes') {
            $sibling = $student?->user?->sibling;

            if (!$sibling) {
                $sibling = new Sibling();
                $sibling->user_id = $student?->user_id;
            }

            $sibling->gender = $data['siblingGender'];
            $sibling->name = $data['siblingName'];
            $sibling->national_id = $data['siblingNationalId'];
            $sibling->faculty_id = $data['siblingFaculty'];

            $sibling->save();
        } else {
            // Delete sibling record if exists
            $sibling = $student?->user?->sibling;
            if ($sibling) {
                $sibling->delete();
            }
        }
    }

    /**
     * Update emergency contact
     *
     * @param Student $student
     * @param array $data
     */
    private function updateEmergencyContact(Student $student, array $data): void
    {
        $emergencyContact = $student?->user?->emergencyContact;
        
        if (!$emergencyContact) {
            $emergencyContact = new EmergencyContact();
            $emergencyContact->user_id = $student?->user_id;
        }

        $emergencyContact->name = $data['emergencyName'];
        $emergencyContact->relation = $data['emergencyRelation'];
        $emergencyContact->phone_number = $data['emergencyPhone'];
        $emergencyContact->address = $data['emergencyAddress'];
        
        $emergencyContact->save();
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