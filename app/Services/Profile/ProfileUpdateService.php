<?php

namespace App\Services\Profile;

use App\Models\Resident\Student;
use App\Models\StudentParent;
use App\Models\Sibling;
use App\Models\EmergencyContact;
use App\Models\StudentArchive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProfileUpdateService
{
    /**
     * @var LookupService
     */
    private $lookupService;

    public function __construct(LookupService $lookupService)
    {
        $this->lookupService = $lookupService;
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
        $student->academic_email = $studentArchive?->email;
        $student->academic_id = $studentArchive?->academic_id;
        $student->cum_gpa = $studentArchive?->cum_gpa ?? 0.0;
        $student->score = $studentArchive?->actual_score ?? 0.0;
        $student->faculty_id = $this->lookupService->getFacultyId($studentArchive?->candidated_faculty_name);
        $student->nationality_id = $this->lookupService->getNationalityId($studentArchive?->nationality_name);
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

        $parent->relationship = $data['parent_relationship'] ?? null;
        $parent->name_en = $data['parent_name_en'] ?? null;
        $parent->name_ar = $data['parent_name_ar'] ?? null;
        $parent->phone = $data['parent_phone'] ?? null;
        $parent->email = $data['parent_email'] ?? null;
        $parent->national_id = $data['parent_national_id'] ?? null;
        $parent->is_abroad = ($data['is_parent_abroad'] ?? false) === true || ($data['is_parent_abroad'] ?? 'no') === 'yes';

        if ($parent->is_abroad) {
            $parent->country_id = $data['parent_abroad_country'] ?? null;
            $parent->living_with_parent = false;
            $parent->governorate_id = null;
            $parent->city_id = null;
        } else {
            $parent->country_id = null;
            $parent->living_with_parent = ($data['living_with_parent'] ?? 'no') === 'yes';

            if (($data['living_with_parent'] ?? 'no') === 'no') {
                $parent->governorate_id = $data['parent_governorate'] ?? null;
                $parent->city_id = $data['parent_city'] ?? null;
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
            $sibling->relationship = isset($data['sibling_gender']) && $data['sibling_gender'] === 'male' ? 'brother' : 'sister';
            $sibling->name_en = $data['sibling_name_en'] ?? null;
            $sibling->name_ar = $data['sibling_name_ar'] ?? null;
            $sibling->national_id = $data['sibling_national_id'] ?? null;
            $sibling->faculty_id = $data['sibling_faculty'] ?? null;
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

            $emergencyContact->relationship = $data['emergency_contact_relationship'] ?? null;
            $emergencyContact->name_ar = $data['emergency_contact_name_ar'] ?? null;
            $emergencyContact->name_en = $data['emergency_contact_name_en'] ?? null;
            $emergencyContact->phone = $data['emergency_contact_phone'] ?? null;
            $emergencyContact->governorate_id = $data['emergency_contact_governorate'] ?? null;
            $emergencyContact->city_id = $data['emergency_contact_city'] ?? null;
            $emergencyContact->street = $data['emergency_contact_street'] ?? null;
            $emergencyContact->notes = $data['emergency_contact_notes'] ?? null;

            $emergencyContact->save();
        }
    }
}
