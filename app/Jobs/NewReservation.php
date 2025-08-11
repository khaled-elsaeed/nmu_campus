<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\StudentArchive;
use App\Events\UserRegistered;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\BusinessValidationException;

class AuthRegisterService
{
    /**
     * Register a new user using the provided validated data.
     *
     * @param array $validated
     * @return User
     * @throws BusinessValidationException
     */
    public function register(array $validated): User
    {
        $studentArchive = $this->getStudentArchive($validated['national_id']);

        $this->validateStudentArchive($studentArchive);

        $user = $this->createUserFromArchive($studentArchive);

        // Fire the UserRegistered event to trigger email verification
        event(new UserRegistered($user));

        return $user;
    }

    /**
     * Get student archive by national ID
     *
     * @param string $nationalId
     * @return StudentArchive|null
     */
    private function getStudentArchive(string $nationalId): ?StudentArchive
    {
        return StudentArchive::where('national_id', $nationalId)->first();
    }

    /**
     * Validate student archive data
     *
     * @param StudentArchive|null $studentArchive
     * @throws BusinessValidationException
     */
    private function validateStudentArchive(?StudentArchive $studentArchive): void
    {
        if (!$studentArchive) {
            throw new BusinessValidationException('Student not found in archives.');
        }

        if ($studentArchive->user) {
            throw new BusinessValidationException('User already registered for this student.');
        }
    }

    /**
     * Create user from student archive data
     *
     * @param StudentArchive $studentArchive
     * @return User
     */
    private function createUserFromArchive(StudentArchive $studentArchive): User
    {
        $user = new User();

        // Basic information
        $user->national_id = $studentArchive->national_id;
        $user->gender = $studentArchive->gender;
        $user->email = $studentArchive->email;
        
        // Process names (concatenate first and last parts)
        $user->name_en = $this->buildFullName($studentArchive->name_en);
        $user->name_ar = $this->buildFullName($studentArchive->name_ar);

        // Set default password as national ID
        $user->password = Hash::make($studentArchive->national_id);
        $user->force_change_password = true;
        
        // Set email verification timestamp to null (unverified)
        $user->email_verified_at = null;

        $user->save();

        return $user;
    }

    /**
     * Build full name by concatenating first and last parts
     * Takes first part and last part from archive name and concatenates them
     *
     * @param string|null $fullName
     * @return string|null
     */
    private function buildFullName(?string $fullName): ?string
    {
        if (!$fullName) {
            return null;
        }

        // Split the full name into parts
        $nameParts = array_filter(explode(' ', trim($fullName)));

        if (empty($nameParts)) {
            return null;
        }

        // If only one part, return it (capitalize first letter, rest lowercase)
        if (count($nameParts) === 1) {
            return ucfirst(mb_strtolower($nameParts[0]));
        }

        // Get first and last parts, capitalize first letter of each, rest lowercase
        $firstName = ucfirst(mb_strtolower($nameParts[0]));
        $lastName = ucfirst(mb_strtolower(end($nameParts)));

        // Concatenate first and last name with space
        return $firstName . ' ' . $lastName;
    }
}