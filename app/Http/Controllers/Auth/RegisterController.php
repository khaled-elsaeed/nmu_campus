<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthRegisterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    protected AuthRegisterService $registerService;

    public function __construct(AuthRegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application using national ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'national_id' => [
                    'required',
                    'string',
                    'size:14',
                    'regex:/^[0-9]{14}$/'
                ],
            ]);

            $user = $this->registerService->register($validated);

            if ($user) {
                return redirect()->route('login')->with('status', 'Registration successful! Please check your email to verify your account.');
            }

            return back()->withErrors(['national_id' => 'Registration failed. Please try again.']);
        } catch (BusinessValidationException $e) {
            return back()->withErrors(['national_id' => $e->getMessage()]);
        } catch (Exception $e) {
            return back()->withErrors(['national_id' => 'An unexpected error occurred. Please try again.']);
        }
    }
}