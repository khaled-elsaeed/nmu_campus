<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;
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
        // Find user by email
        $user = User::where('email', $credentials['email'] ?? null)->first();

        // Check if user exists
        if (!$user) {
            return [
                'success' => false,
                'error' => 'invalid_credentials',
                'message' => 'Invalid email or password.',
                'user' => null
            ];
        }

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            return [
                'success' => false,
                'error' => 'email_not_verified',
                'message' => 'Please verify your email address before logging in.',
                'user' => $user
            ];
        }


        // Attempt login
        if (Auth::attempt($credentials, $remember)) {
            return [
                'success' => true,
                'error' => null,
                'message' => 'Login successful.',
                'user' => Auth::user()
            ];
        }

        return [
            'success' => false,
            'error' => 'invalid_credentials',
            'message' => 'Invalid email or password.',
            'user' => null
        ];
    }

    /**
     * Log out the currently authenticated user.
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
        
        // Regenerate session to prevent fixation attacks
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}