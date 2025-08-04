<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthRegisterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

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
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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
                ]
            ]);

            $result = $this->registerService->register($validated);

            if ($result['success']) {
                return redirect()->route('login')->with('status', $result['message']);
            }

            // Handle specific error types
            return back()->withErrors(['national_id' => $result['message']])->withInput($request->only('national_id'));

        } catch (Exception $e) {
            logger()->error('Registration error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'national_id' => $request->input('national_id'),
                'exception' => $e
            ]);
            return back()->withErrors(['national_id' => 'An unexpected error occurred. Please try again.'])->withInput($request->only('national_id'));
        }
    }
}