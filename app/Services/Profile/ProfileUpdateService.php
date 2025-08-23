<?php

namespace App\Services\Profile;

use App\Models\Resident\Student;
use App\Models\Guardian;
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
            $this->updateGuardianInfo($student, $data);
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

        $student->date_of_birth = $studentArchive?->birthdate ? \Carbon\Carbon::parse($studentArchive->birthdate)->format('Y-m-d') : null;
        $student->academic_email = $studentArchive?->email;
        $student->academic_id = $studentArchive?->academic_id;
        $student->cum_gpa = $studentArchive?->cum_gpa ?? 0.0;
        $student->score = $studentArchive?->actual_score ?? 0.0;
        $student->faculty_id = $this->lookupService->getFacultyId($studentArchive?->candidated_faculty_name);
        $student->nationality_id = $this->lookupService->getNationalityId($studentArchive?->nationality_name);
    }

    /**
     * Update guardian information
     *
     * @param \App\Models\Student $student
     * @param array $data
     */
    private function updateGuardianInfo(Student $student, array $data): void
    {
        $user = $student->user;

        $guardian = $this->isGuardianFoundWithSameData($data);

        if (!$guardian) {
            $guardian = new Guardian();
        }

        $guardian->fill([
            'relationship'   => $data['guardian_relationship'] ?? null,
            'name_en'        => $data['guardian_name_en'] ?? null,
            'name_ar'        => $data['guardian_name_ar'] ?? null,
            'phone'          => $data['guardian_phone'] ?? null,
            'email'          => $data['guardian_email'] ?? null,
            'national_id'    => $data['guardian_national_id'] ?? null,
        ]);

        $isAbroad = ($data['is_guardian_abroad'] ?? false) === true 
                || ($data['is_guardian_abroad'] ?? 'no') === 'yes';

        $guardian->is_abroad = $isAbroad;

        if ($isAbroad) {
            $guardian->country_id         = $data['guardian_abroad_country'] ?? null;
            $guardian->living_with_guardian = false;
            $guardian->governorate_id     = null;
            $guardian->city_id            = null;
        } else {
            $guardian->country_id         = null;
            $guardian->living_with_guardian = ($data['living_with_guardian'] ?? 'no') === 'yes';

            if (($data['living_with_guardian'] ?? 'no') === 'no') {
                $guardian->governorate_id = $data['guardian_governorate'] ?? null;
                $guardian->city_id        = $data['guardian_city'] ?? null;
            } else {
                $guardian->governorate_id = null;
                $guardian->city_id        = null;
            }
        }

        $guardian->save();

        // Attach guardian to user if not already linked
        if (!$user->guardians()->where('guardians.id', $guardian->id)->exists()) {
            $user->guardians()->attach($guardian->id);
        }
    }

    /**
     * Find existing guardian by national_id
     */
    private function isGuardianFoundWithSameData(array $data): ?Guardian
    {
        return Guardian::where('national_id', $data['guardian_national_id'] ?? null)->first();
    }

    /**
     * Update sibling information
     *
     * @param \App\Models\Student $student
     * @param array $data
     */
    private function updateSiblingInfo(Student $student, array $data): void
    {
        $user = $student->user;

        if ($data['has_sibling_in_dorm'] === 'no') {
            $user->siblings()->detach();
            return;
        }

        if ($data['has_sibling_in_dorm'] === 'yes') {

            $sibling = $this->isSiblingFoundWithSameData($data);

            if (!$sibling) {
                $sibling = new Sibling();
            }

            $sibling->fill([
                'gender'        => ($data['sibling_relationship'] === 'brother' ? 'male' : 'female'),
                'relationship'  => $data['sibling_relationship'] ?? null,
                'name_en'       => $data['sibling_name_en'] ?? null,
                'name_ar'       => $data['sibling_name_ar'] ?? null,
                'national_id'   => $data['sibling_national_id'] ?? null,
                'faculty_id'    => $data['sibling_faculty'] ?? null,
            ]);

            $sibling->save();

            // Attach sibling to user if not already linked
            if (!$user->siblings()->where('siblings.id', $sibling->id)->exists()) {
                $user->siblings()->attach($sibling->id);
            }
        }
    }

    private function isSiblingFoundWithSameData(array $data): ?Sibling
    {
        return Sibling::where('national_id', $data['sibling_national_id'] ?? null)->first();
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

        if ($data['is_guardian_abroad'] === 'no') {
            if ($emergencyContact) {
                $emergencyContact->delete();
            }
            return;
        }

        if ($data['is_guardian_abroad'] === 'yes') {
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
