<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class AuthLoginService
{
    /**
     * Attempt to log in the user with the given credentials.
     * 
     * @param array $credentials
     * @param bool $remember
     * @return array Returns result with status and user data
     */
    public function login(array $credentials, bool $remember = false): array
    {
        $user = User::where('email', $credentials['email'] ?? null)->first();

        // Perform general validation checks
        $validationResult = $this->performGeneralValidation($user);
        if (!$validationResult['success']) {
            return $validationResult;
        }

        // Perform profile-dependent validation checks
        $profileValidationResult = $this->performProfileDependentValidation($user);
        if (!$profileValidationResult['success']) {
            // For incomplete profiles, still attempt login but redirect to completion
            if ($profileValidationResult['error'] === 'incomplete_profile') {
                $loginResult = $this->attemptLogin($credentials, $remember);
                if ($loginResult['success']) {
                    return array_merge($profileValidationResult, [
                        'redirect_to' => $this->getProfileCompletionRedirect($user)
                    ]);
                }
                return $loginResult; // Return login failure if credentials are wrong
            }
            
            return array_merge($profileValidationResult, [
                'redirect_to' => $this->getProfileCompletionRedirect($user)
            ]);
        }

        // Attempt the actual login
        $loginResult = $this->attemptLogin($credentials, $remember);
        if (!$loginResult['success']) {
            return $loginResult;
        }

        return $this->buildSuccessResponse($loginResult['user']);
    }

    /**
     * Log out the currently authenticated user.
     *
     * @return array
     */
    public function logout(): array
    {
        if (!Auth::check()) {
            return $this->buildResponse(
                false,
                'not_authenticated',
                __('No user is currently logged in.')
            );
        }

        $user = Auth::user();
        
        Auth::logout();
        
        // Regenerate session to prevent fixation attacks
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->buildResponse(
            true,
            null,
            __('Logout successful.'),
            $user
        );
    }

    /**
     * Check if user needs to change password after login.
     *
     * @param User $user
     * @return bool
     */
    public function shouldForcePasswordChange(User $user): bool
    {
        return $user->shouldForcePasswordChange();
    }

    // =============================================
    // PRIVATE HELPER METHODS
    // =============================================

    /**
     * Attempt to authenticate the user with given credentials.
     *
     * @param array $credentials
     * @param bool $remember
     * @return array
     */
    private function attemptLogin(array $credentials, bool $remember): array
    {
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            $this->updateLastLogin($user);

            return $this->buildResponse(
                true,
                null,
                __('Login successful.'),
                $user
            );
        }

        return $this->buildResponse(
            false,
            'invalid_credentials',
            __('Invalid email or password.')
        );
    }

    /**
     * Build successful login response with redirect.
     *
     * @param User $user
     * @return array
     */
    private function buildSuccessResponse(User $user): array
    {
        return $this->buildResponse(
            true,
            null,
            'Login successful.',
            $user,
            ['redirect_to' => $this->getSuccessRedirect($user)]
        );
    }

    // =============================================
    // VALIDATION METHODS
    // =============================================

    /**
     * Perform general validation checks on the user.
     *
     * @param User|null $user
     * @return array
     */
    private function performGeneralValidation(?User $user): array
    {
        if (!$user) {
            return $this->buildResponse(
                false,
                'invalid_credentials',
                __('Invalid email or password.')
            );
        }

        if ($user->isBanned()) {
            $banDetails = $user->getBanDetails(); // Fixed: Added method call to get ban details
            return $this->buildResponse(
                false,
                'account_banned',
                'Your account is banned. Please contact support.',
                $user,
                ['ban_details' => $banDetails]
            );
        }

        if (!$user->hasVerifiedEmail()) {
            return $this->buildResponse(
                false,
                'email_not_verified',
                'Please verify your email address before logging in.',
                $user
            );
        }

        return $this->buildResponse(true, null, __('General validation passed.'), $user);
    }

    /**
     * Perform profile-dependent validation checks.
     *
     * @param User $user
     * @return array
     */
    private function performProfileDependentValidation(User $user): array
    {
        if ($user->hasRole('resident')) {
            return $this->validateResidentProfile($user);
        } 
        
        if ($user->hasRole('staff')) {
            return $this->validateStaffProfile($user);
        }
        
        if ($user->hasRole('admin')) {
            return $this->validateAdminProfile($user);
        }

        return $this->buildResponse(
            false,
            'invalid_role',
            __('Invalid user role for profile validation.'),
            $user
        );
    }

    /**
     * Validate resident profile completeness.
     *
     * @param User $user
     * @return array
     */
    private function validateResidentProfile(User $user): array
    {
        // Check if resident profile exists and is complete
        if (!$user->resident || !$user->resident->exists || !$user->isProfileComplete()) {
            return $this->buildResponse(
                false,
                'incomplete_profile',
                __('Please complete your resident profile before proceeding.'),
                $user
            );
        }

        // Additional resident-specific validations can be added here
        if ($user->resident->needs_document_verification) {
            return $this->buildResponse(
                false,
                'documents_pending',
                __('Your documents are pending verification.'),
                $user
            );
        }

        return $this->buildResponse(true, null, __('Resident profile validation passed.'), $user);
    }

    /**
     * Validate staff profile completeness.
     *
     * @param User $user
     * @return array
     */
    private function validateStaffProfile(User $user): array
    {
        if (!$user->staff || !$user->staff->exists) {
            return $this->buildResponse(
                false,
                'incomplete_profile',
                __('Please complete your staff profile before proceeding.'),
                $user
            );
        }

        return $this->buildResponse(true, null, __('Staff profile validation passed.'), $user);
    }

    /**
     * Validate admin profile.
     *
     * @param User $user
     * @return array
     */
    private function validateAdminProfile(User $user): array
    {
        // Admins typically don't need additional profile validation
        return $this->buildResponse(true, null, __('Admin profile validation passed.'), $user);
    }

    // =============================================
    // REDIRECT METHODS
    // =============================================

    /**
     * Get redirect URL for profile completion based on user role.
     *
     * @param User $user
     * @return string
     */
    private function getProfileCompletionRedirect(User $user): string
    {
        if ($user->hasRole('resident')) {
            return route('profile.resident.student.complete');
        }
        
        // Default fallback
        return route('login');
    }

    /**
     * Get redirect URL after successful login based on user role.
     *
     * @param User $user
     * @return string
     */
    private function getSuccessRedirect(User $user): string
    {
        if ($user->hasRole('resident')) {
            return route('resident.dashboard');
        } 
        
        if ($user->hasRole('staff')) {
            return route('staff.dashboard');
        }
        
        if ($user->hasRole('admin')) {
            return route('dashboard.admin.index');
        }

        // Default fallback
        return route('dashboard');
    }

    // =============================================
    // UTILITY METHODS
    // =============================================

    /**
     * Update the user's last login timestamp.
     *
     * @param User $user
     * @return void
     */
    private function updateLastLogin(User $user): void
    {
        $user->update(['last_login' => now()]);
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