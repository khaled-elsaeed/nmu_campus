<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Notifications\EmailVerification;

class EmailVerificationController extends Controller
{
    /**
     * Verify the user's email address.
     *
     * @param Request $request
     * @param string $id
     * @param string $hash
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request, string $id, string $hash)
    {
        $user = User::findOrFail($id);

        // Check if the hash matches
        if (!hash_equals((string) $hash, sha1($user->email))) {
            return redirect()->route('login')->withErrors('Invalid verification link.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->withErrors('Email already verified.');

        }

        // Mark email as verified
        $user->markEmailAsVerified();

        return redirect()->route('login')->with('status', 'Email verified successfully. Please login.');
    }
    /**
     * Resend the email verification notification.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'Email already verified.');
        }

        $user->notify(new EmailVerification());

        return redirect()->route('login')->with('status', 'Verification email sent successfully. Please check your inbox.');
    }

    /**
     * Show email verification notice.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function notice(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'Email already verified.');
        }
        return redirect()->route('login')->with('info', 'Please verify your email address before logging in.');
    }
}