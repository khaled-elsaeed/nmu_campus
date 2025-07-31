<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();


        // if ($user->hasRole('resident')) {
        //     return redirect()->route('home.resident');
        // }
            return view('home.resident');

        // Default fallback
        // return redirect()->route('login')->withErrors(['error' => 'Unauthorized access. Please log in.']);
    }
}