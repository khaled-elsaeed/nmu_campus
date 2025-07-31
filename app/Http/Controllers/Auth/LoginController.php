<?php

namespace App\Http\Controllers\Auth;

use App\Services\Auth\AuthLoginService as AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;


class LoginController extends Controller
{

    public function __construct(protected AuthService $authService)
    {}

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        $result = $this->authService->login($credentials, $remember);

        // Handle different error cases with appropriate redirects
        if (!$result['success']) {
            return $this->handleLoginError($result, $request);
        }

        // Regenerate session on successful login
        $request->session()->regenerate();

        // Redirect to intended page or dashboard
        return redirect()->intended(route('home'));
    }

    /**
     * Handle login errors with appropriate redirects.
     */
    private function handleLoginError(array $result, Request $request): RedirectResponse
    {
        return match ($result['error']) {
            'email_not_verified' => redirect()->route('verification.notice')
                ->with('email', $result['user']->email)
                ->with('warning', $result['message']),
                
            'account_inactive' => redirect()->route('login')
                ->with('error', $result['message'])
                ->with('contact_support', true),
                
            'invalid_credentials' => redirect()->back()
                ->withErrors(['email' => $result['message']])
                ->withInput($request->except('password')),
                
            default => redirect()->back()
                ->with('error', 'An unexpected error occurred. Please try again.')
                ->withInput($request->except('password'))
        };
    }


    public function logout(Request $request)
    {
        $this->authService->logout();

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
}
