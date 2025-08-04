<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserBan;
use App\Models\StudentArchive;
use App\Events\UserRegistered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthRegisterService
{
    /**
     * Register a new user with validation checks.
     *
     * @param array $userData
     * @return array
     */
    public function register(array $userData): array
    {
        $validationResult = $this->validateRegistrationEligibility($userData['national_id'] ?? null);

        if (!$validationResult['success']) {
            return $validationResult;
        }

        $user = DB::transaction(function () use ($userData) {
            $user = $this->createUser($userData);
            UserRegistered::dispatch($user);
            return $user;
        });

        return $this->buildResponse(
            true,
            null,
            'Registration successful. Please check your email for verification.',
            $user
        );
    }


    /**
     * Comprehensive validation for registration eligibility.
     * Combines ban check and existing account check.
     *
     * @param string|null $nationalId
     * @return array
     */
    private function validateRegistrationEligibility(?string $nationalId): array
    {
        if (empty($nationalId)) {
            return $this->buildResponse(
                false,
                'national_id_required',
                'National ID is required.'
            );
        }

        // Check if national ID is associated with a university student
        if (!StudentArchive::isUniversityStudent($nationalId)) {
            return $this->buildResponse(
                false,
                'no_student_record',
                'No university student record was found associated with the provided national ID.'
            );
        }

        // Check if national ID is banned
        if (UserBan::isBannedByNationalId($nationalId)) {
            return $this->buildResponse(
                false,
                'national_id_banned',
                'This national ID is banned from registration.'
            );
        }

        // Check if already has account
        if ($this->nationalIdExists($nationalId)) {
            return $this->buildResponse(
                false,
                'account_exists',
                'An account with this national ID already exists.'
            );
        }

        return $this->buildResponse(
            true,
            null,
            'National ID can register.'
        );
    }

    /**
     * Check if national ID already exists.
     *
     * @param string $nationalId
     * @return bool
     */
    private function nationalIdExists(string $nationalId): bool
    {
        return StudentArchive::where('national_id', $nationalId)
            ->whereHas('user')
            ->exists();
    }

    /**
     * Create a new user.
     *
     * @param array $userData
     * @return User
     */
    private function createUser(array $userData): User
    {
        // Get student data from the archive
        $studentArchive = StudentArchive::where('national_id', $userData['national_id'])->first();

        $archiveIncompleteData = $this->checkForRequiredData($studentArchive);

        if ($archiveIncompleteData) {
            return $this->buildResponse(
                false,
                'archive_incomplete_data',
                'Student archive is missing required information.'
            );
        }

        // Extract and format first and last names
        $formattedNameEn = $this->extractFirstLastName($studentArchive->name_en);
        $formattedNameAr = $this->extractFirstLastName($studentArchive->name_ar);

        // Create user with data from student archive
        $user = User::create([
            'name_en' => $formattedNameEn,
            'name_ar' => $formattedNameAr,
            'email' => $studentArchive->academic_email ?? $studentArchive->email,
            'password' => Hash::make('password'),
            'gender' => $studentArchive->gender,
            'force_change_password' => true,
        ]);

        // Assign Spatie role 'resident'
        $user->assignRole('resident');

        // Link the student archive to this user
        $studentArchive->update(['user_id' => $user->id]);

        return $user;
    }

    /**
     * Check if the student archive has all required data.
     *
     * @param StudentArchive $studentArchive
     * @return bool
     */
    private function checkForRequiredData(StudentArchive $studentArchive): array
    {
        $incompleteData = [];

        if (empty($studentArchive->name_en)) {
            $incompleteData['name_en'] = 'English name is required.';
        }

        if (empty($studentArchive->name_ar)) {
            $incompleteData['name_ar'] = 'Arabic name is required.';
        }

        if (empty($studentArchive->email)) {
            $incompleteData['email'] = 'Email is required.';
        }

        return $incompleteData;
    }

    /**
     * Extract first and last name from full name and capitalize properly.
     *
     * @param string $fullName
     * @return string
     */
    private function extractFirstLastName(string $fullName): string
    {
        if (empty($fullName)) {
            return '';
        }

        // Split the name into parts and remove empty elements
        $nameParts = array_filter(explode(' ', trim($fullName)));
        
        if (empty($nameParts)) {
            return '';
        }

        // If only one name part, return it capitalized
        if (count($nameParts) === 1) {
            return ucfirst(strtolower($nameParts[0]));
        }

        // Get first and last name
        $firstName = ucfirst(strtolower($nameParts[0]));
        $lastName = ucfirst(strtolower(end($nameParts)));

        return $firstName . ' ' . $lastName;
    }

    /**
     * Build a standardized response array.
     *
     * @param bool $success
     * @param string|null $error
     * @param string $message
     * @param User|null $user
     * @param array $additional
     * @return array
     */
    private function buildResponse(
        bool $success, 
        ?string $error, 
        string $message, 
        ?User $user = null, 
        array $additional = []
    ): array {
        return array_merge([
            'success' => $success,
            'error' => $error,
            'message' => $message,
            'user' => $user,
        ], $additional);
    }
}