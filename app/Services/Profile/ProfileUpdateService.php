<?php

namespace App\Services\Profile;

use App\Models\Resident\Student;
use App\Models\Guardian;
use App\Models\Sibling;
use App\Models\EmergencyContact;
use App\Models\StudentArchive;
use App\Models\Reservation\ReservationRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

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
     * @throws Exception
     */
    public function saveProfileData(array $data): array
    {
        $user = Auth::user();

        if (!$user) {
            throw new Exception('User not authenticated');
        }

        $studentArchive = $user->studentArchive;
        $user = $studentArchive?->user ?? $user;

        return DB::transaction(function () use ($user, $data, $studentArchive) {
            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                $student = new Student();
                $student->user_id = $user->id;
            }

            $this->updateStudentBasicInfo($student, $data, $studentArchive);
            $this->updateGuardianInfo($user, $data);
            $this->updateSiblingInfo($user, $data);
            $this->updateEmergencyContact($user, $data);

            $student->save();

            return [
                'student_id' => $student->id,
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
        if ($studentArchive) {
            $student->name_ar = $studentArchive->name_ar;
            $student->name_en = $studentArchive->name_en;
            $student->national_id = $studentArchive->national_id;
            $student->date_of_birth = $studentArchive->birthdate ? 
                \Carbon\Carbon::parse($studentArchive->birthdate)->format('Y-m-d') : null;
            $student->academic_email = $studentArchive->email;
            $student->academic_id = $studentArchive->academic_id;
            $student->cum_gpa = $studentArchive->cum_gpa ?? 0.0;
            $student->score = $studentArchive->actual_score ?? 0.0;
            $student->faculty_id = $this->lookupService->getFacultyId($studentArchive->candidated_faculty_name);
            $student->nationality_id = $this->lookupService->getNationalityId($studentArchive->nationality_name);
        }
    }

    /**
     * Update guardian information
     *
     * @param User $user    
     * @param array $data
     */
    private function updateGuardianInfo(User $user, array $data): void
    {
        $guardian = $this->lookupService->findGuardianByNationalId($data['guardian_national_id'] ?? null);

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
            $guardian->country_id = $data['guardian_abroad_country'] ?? null;
            $guardian->living_with_guardian = false;
            $guardian->governorate_id = null;
            $guardian->city_id = null;
        } else {
            $guardian->country_id = null;
            $guardian->living_with_guardian = ($data['living_with_guardian'] ?? 'no') === 'yes';

            if (($data['living_with_guardian'] ?? 'no') === 'no') {
                $guardian->governorate_id = $data['guardian_governorate'] ?? null;
                $guardian->city_id = $data['guardian_city'] ?? null;
            } else {
                $guardian->governorate_id = null;
                $guardian->city_id = null;
            }
        }

        $guardian->save();

        // Attach guardian to user if not already linked
        if (!$user->guardians()->where('guardians.id', $guardian->id)->exists()) {
            $user->guardians()->attach($guardian->id);
        }
    }

    /**
     * Update sibling information
     *
     * @param User $user
     * @param array $data
     */
    private function updateSiblingInfo(User $user, array $data): void
    {
        if (($data['has_sibling_in_dorm'] ?? 'no') === 'no') {
            $user->siblings()->detach();
            return;
        }

        if (($data['has_sibling_in_dorm'] ?? 'no') === 'yes' && isset($data['siblings'])) {
            foreach ($data['siblings'] as $siblingData) {
                $this->processSingleSibling($user, $siblingData);
            }
        }
    }

    /**
     * Process a single sibling record
     *
     * @param User $user
     * @param array $siblingData
     */
    private function processSingleSibling(User $user, array $siblingData): void
    {
        $sibling = $this->lookupService->findSiblingByNationalId($siblingData['national_id'] ?? null);

        if (!$sibling) {
            $sibling = new Sibling();
        }

        Log::info('Sibling data being processed:', $siblingData);

        $sibling->fill([
            'relationship' => $siblingData['relationship'] ?? null,
            'name_en'      => $siblingData['name_en'] ?? null,
            'name_ar'      => $siblingData['name_ar'] ?? null,
            'national_id'  => $siblingData['national_id'] ?? null,
            'faculty_id'   => $siblingData['faculty'] ?? null,
            'gender'       => ($siblingData['relationship'] ?? null) === 'brother' ? 'male' : 'female',
            'academic_level' => $siblingData['academic_level'] ?? null
        ]);

        $sibling->save();

        // Attach sibling to user if not already linked
        if (!$user->siblings()->where('siblings.id', $sibling->id)->exists()) {
            $user->siblings()->attach($sibling->id);
        }
    }


    /**
     * Update emergency contact information
     *
     * @param User $user
     * @param array $data
     */
    private function updateEmergencyContact(User $user, array $data): void
    {
        $emergencyContact = $user->emergencyContact;

        if (($data['is_guardian_abroad'] ?? 'no') === 'no') {
            if ($emergencyContact) {
                $emergencyContact->delete();
            }
            return;
        }

        if (($data['is_guardian_abroad'] ?? 'no') === 'yes') {
            if (!$emergencyContact) {
                $emergencyContact = new EmergencyContact();
                $emergencyContact->user_id = $user->id;
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