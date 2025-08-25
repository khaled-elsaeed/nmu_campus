<?php

namespace App\Http\Controllers\Auth;

use App\Services\Auth\AuthLoginService as AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse; 

class LoginController extends Controller
{

    public function __construct(protected AuthService $authService)
    {}

    public function showLoginForm()
    {
        try {
            Log::info('Login form accessed', [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return view('auth.login');

        } catch (\Exception $e) {
            Log::error('Error showing login form', [
                'ip_address' => request()->ip(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Return a basic error response if view fails
            return response('Login form unavailable. Please try again later.', 500);
        }
    }

    /**
     * Handle user login attempt.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember');

            Log::info('Login attempt started', [
                'email' => $credentials['email'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'remember' => $remember
            ]);

            $result = $this->authService->login($credentials, $remember);

            // Handle different error cases with appropriate redirects
            if (!$result['success']) {
                return $this->handleLoginError($result, $request);
            }

            $request->session()->regenerate();

            $redirectTo = $result['redirect_to'] ?? route('login');

            return redirect()->intended($redirectTo);

        } catch (\Exception $e) {
            Log::error('Login process exception', [
                'email' => $request->input('email'),
                'ip_address' => $request->ip(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('login')
                ->withErrors(['email' => 'An unexpected error occurred. Please try again.'])
                ->withInput($request->only('email', 'remember'));
        }
    }

  /**
     * Handle login errors and return appropriate redirect response.
     *
     * @param array $result
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleLoginError(array $result, Request $request)
    {
        try {
            $error = $result['error'];
            $message = $result['message'];

            Log::info('Handling login error', [
                'error_type' => $error,
                'email' => $request->input('email'),
                'ip_address' => $request->ip()
            ]);

            // Handle specific error cases with appropriate redirects
            switch ($error) {
                case 'incomplete_profile':
                    Log::info('Redirecting to complete profile', [
                        'email' => $request->input('email'),
                        'redirect_to' => $result['redirect_to']
                    ]);
                    return redirect()->to($result['redirect_to']);

                case 'password_expired':
                    Log::info('Redirecting to password reset', [
                        'email' => $request->input('email'),
                        'redirect_to' => $result['redirect_to']
                    ]);
                    return redirect()->to($result['redirect_to']);

                case 'email_not_verified':
                    Log::info('Redirecting to email verification', [
                        'email' => $request->input('email')
                    ]);
                    return redirect()->route('login')
                        ->withErrors(['email' => $message])
                        ->withInput($request->only('email', 'remember'));

                case 'account_banned':
                    Log::warning('Account banned login attempt', [
                        'email' => $request->input('email'),
                        'ip_address' => $request->ip()
                    ]);
                    return redirect()->route('login')
                        ->withErrors(['email' => $message])
                        ->withInput($request->only('email', 'remember'));
                default:
                    Log::error('Unknown login error', [
                        'error_type' => $error,
                        'message' => $message,
                        'email' => $request->input('email'),
                        'ip_address' => $request->ip()
                    ]);
                    return redirect()->route('login')
                        ->withErrors(['email' => $message])
                        ->withInput($request->only('email', 'remember'));
            }

        } catch (\Exception $e) {
            Log::error('Error handling login error', [
                'email' => $request->input('email'),
                'ip_address' => $request->ip(),
                'original_error' => $result['error'] ?? 'unknown',
                'exception_message' => $e->getMessage(),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('login')
                ->withErrors(['email' => 'An unexpected error occurred. Please try again.'])
                ->withInput($request->only('email', 'remember'));
        }
    }


    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            
            Log::info('User logout initiated', [
                'user_id' => $user ? $user->id : null,
                'email' => $user ? $user->email : null,
                'ip_address' => $request->ip()
            ]);

            $this->authService->logout();

            Log::info('User logout completed', [
                'ip_address' => $request->ip()
            ]);

            return redirect()->route('login')
                ->with('success', 'You have been logged out successfully.');

        } catch (\Exception $e) {
            Log::error('Logout process exception', [
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Even if logout fails, redirect to login page
            return redirect()->route('login')
                ->with('error', 'An error occurred during logout, but you have been logged out.');
        }
    }
}
