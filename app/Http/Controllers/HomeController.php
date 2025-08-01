<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('resident')) {
            return view('home.resident');
        } elseif ($user->hasRole('admin')) {
            return view('home.admin');
        }

        return redirect()->route('login')->withErrors(['error' => 'Unauthorized access. Please log in.']);
    }
}
