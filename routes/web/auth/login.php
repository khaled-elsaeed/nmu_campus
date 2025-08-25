<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// ====================
// Login Routes
// ====================

// Root route redirects to login
Route::get('/', [LoginController::class, 'showLoginForm']);

// Login form display
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

// Login form submission
Route::post('login', [LoginController::class, 'login'])->name('login.submit');

// Logout route
Route::post('logout', [LoginController::class, 'logout'])->name('logout');