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
        // $user = Auth::user();
        
        // if (!$user) {
        //     throw new \Exception('User not authenticated');
        // }

        // Get student archive data if available (only non-deleted records)
        $studentArchive = StudentArchive::where('is_deleted', false)->first();

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
            'emergency_contact' => $this->getEmergencyContact($studentArchive),
            'terms' => $this->getTerms(),
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
            // Get or create student record
            $student = Student::where('user_id', $user->id)->first();
            
            if (!$student) {
                $student = new Student();
                $student->user_id = $user->id;
            }

            // Update student basic information
            $this->updateStudentBasicInfo($student, $data);
            
            // Update or create parent information
            $this->updateParentInfo($student, $data);
            
            // Update or create sibling information
            $this->updateSiblingInfo($student, $data);
            
            // Update or create emergency contact
            $this->updateEmergencyContact($student, $data);

            $student->save();

            return [
                'student_id' => $student->id,
                'message' => 'Profile saved successfully'
            ];
        });
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

        return $governorate ? $governorate->id : null;
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

        return $city ? $city->id : null;
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

        return $faculty ? $faculty->id : null;
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
                $nationality = Nationality::where('code', $country->code)->first();
            }
        }

        return $nationality ? $nationality->id : null;
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
            'national_id' => $studentArchive->national_id,
            'full_name_arabic' => $studentArchive->name_ar,
            'full_name_english' => $studentArchive->name_en,
            'birth_date' => $studentArchive->birthdate,
            'gender' => $studentArchive->gender ?? 'male',
            'nationality' => $studentArchive->nationality_name,
            'nationality_id' => $this->getNationalityId($studentArchive->nationality_name),
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
            'email' => $studentArchive->email,
            'mobile_number' => $studentArchive->mobile,
            'whatsapp' => $studentArchive->whatsapp,
            'governorate' => $studentArchive->govern,
            'governorate_id' => $this->getGovernorateId($studentArchive->govern),
            'city' => $studentArchive->city,
            'city_id' => $this->getCityId($studentArchive->city),
            'street' => $studentArchive->street,
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
        $certificateCountry = $this->getCountry($studentArchive->cert_country_name); 
        return [
            'student_id' => null, 
            'faculty_id' => $this->getFacultyId($studentArchive->candidated_faculty_name), 
            'program_id' => null, 
            'academic_year' => null, 
            'gpa' => 0.0, // Default GPA
            'actual_score' => $studentArchive->actual_score,
            'actual_percent' => $studentArchive->actual_percent,
            'certificate_type' => $studentArchive->certificate_type_name,
            'certificate_country' => $studentArchive->cert_country_name,
            'certificate_country_id' => $certificateCountry->id,
            'certificate_year' => $studentArchive->cert_year_name,
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
        $parentCountry = $this->getCountry($studentArchive->parent_country_name);
        return [
            'parent_name' => $studentArchive->parent_name,
            'parent_mobile' => $studentArchive->parent_mobile,
            'parent_email' => $studentArchive->parent_email,
            'parent_country' => $studentArchive->parent_country_name,
            'parent_country_id' => $parentCountry->id,
            'is_abroad' => isset($parentCountry->code) && ($parentCountry->code !== self::EGYPT_COUNTRY_CODE),
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

        if ($studentArchive->brother) {
            $data = [
                'has_sibling_in_dorm' => 'yes',
                'sibling_name' => $studentArchive->brother_name,
                'sibling_faculty_id' => $this->getFacultyId($studentArchive->brother_faculty_name),
                'sibling_faculty_name' => $studentArchive->brother_faculty_name,
                'sibling_level' => $studentArchive->brother_level,
            ];
        }

        return $data;
    }

    /**
     * Get emergency contact information
     *
     * @param StudentArchive $studentArchive
     * @return array
     */
    private function getEmergencyContact(StudentArchive $studentArchive): array
    {
        // StudentArchive doesn't have emergency contact fields
        return [];
    }

    /**
     * Get terms information
     *
     * @return array
     */
    private function getTerms(): array
    {
        return [
            'terms_accepted' => false, // Default to false
        ];
    }

    /**
     * Soft delete student archive for the current authenticated user
     *
     * @return array
     */
    public function softDeleteStudentArchive(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $studentArchive = $user->studentArchive()->where('is_deleted', false)->first();
        
        if (!$studentArchive) {
            throw new \Exception('No active student archive found');
        }

        $studentArchive->update(['is_deleted' => true]);

        return [
            'message' => 'Student archive deleted successfully',
            'archive_id' => $studentArchive->id
        ];
    }

    /**
     * Restore soft deleted student archive for the current authenticated user
     *
     * @return array
     */
    public function restoreStudentArchive(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $studentArchive = $user->studentArchive()->where('is_deleted', true)->first();
        
        if (!$studentArchive) {
            throw new \Exception('No deleted student archive found');
        }

        $studentArchive->update(['is_deleted' => false]);

        return [
            'message' => 'Student archive restored successfully',
            'archive_id' => $studentArchive->id
        ];
    }

    /**
     * Get deleted student archive for the current authenticated user (for admin purposes)
     *
     * @return array
     */
    public function getDeletedStudentArchive(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $studentArchive = $user->studentArchive()->where('is_deleted', true)->first();
        
        if (!$studentArchive) {
            return [];
        }

        return [
            'personal_info' => $this->getPersonalInfo($studentArchive),
            'contact_info' => $this->getContactInfo($studentArchive),
            'academic_info' => $this->getAcademicInfo($studentArchive),
            'parent_info' => $this->getParentInfo($studentArchive),
            'sibling_info' => $this->getSiblingInfo($studentArchive),
            'emergency_contact' => $this->getEmergencyContact($studentArchive),
            'terms' => $this->getTerms(),
            'is_deleted' => true,
            'deleted_at' => $studentArchive->updated_at,
        ];
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
     * Update student basic information
     *
     * @param Student $student
     * @param array $data
     */
    private function updateStudentBasicInfo(Student $student, array $data): void
    {
        $student->national_id = $data['nationalId'];
        $student->name_ar = $data['fullNameArabic'];
        $student->name_en = $data['fullNameEnglish'];
        $student->date_of_birth = $data['birthDate'];
        $student->gender = $data['gender'];
        $student->nationality_id = $data['nationality'];
        $student->academic_email = $data['email'];
        $student->phone = $data['mobileNumber'];
        $student->governorate_id = $data['governorate'];
        $student->city_id = $data['city'];
        $student->academic_id = $data['studentId'];
        $student->faculty_id = $data['faculty'];
        $student->program_id = $data['program'];
        $student->level = $data['academicYear'];
        $student->gpa = $data['gpa'];
        $student->is_profile_complete = true;
    }

    /**
     * Update parent information
     *
     * @param Student $student
     * @param array $data
     */
    private function updateParentInfo(Student $student, array $data): void
    {
        $parent = $student->user->parent;
        
        if (!$parent) {
            $parent = new StudentParent();
            $parent->user_id = $student->user_id;
        }

        $parent->father_name = $data['fatherName'];
        $parent->mother_name = $data['motherName'];
        $parent->mobile_number = $data['parentMobile'];
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
            $sibling = $student->user->sibling;
            
            if (!$sibling) {
                $sibling = new Sibling();
                $sibling->user_id = $student->user_id;
            }

            $sibling->gender = $data['siblingGender'];
            $sibling->name = $data['siblingName'];
            $sibling->national_id = $data['siblingNationalId'];
            $sibling->faculty_id = $data['siblingFaculty'];
            
            $sibling->save();
        } else {
            // Delete sibling record if exists
            if ($student->user->sibling) {
                $student->user->sibling->delete();
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
        $emergencyContact = $student->user->emergencyContact;
        
        if (!$emergencyContact) {
            $emergencyContact = new EmergencyContact();
            $emergencyContact->user_id = $student->user_id;
        }

        $emergencyContact->name = $data['emergencyName'];
        $emergencyContact->relation = $data['emergencyRelation'];
        $emergencyContact->mobile_number = $data['emergencyMobile'];
        $emergencyContact->address = $data['emergencyAddress'];
        
        $emergencyContact->save();
    }
}