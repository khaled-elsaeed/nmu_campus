<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ====================
// Home Routes
// ====================

// ===== Resident Home Routes =====
Route::middleware(['auth'])
    ->prefix('resident')
    ->name('resident.')
    ->group(function () {
        // Main Routes
        Route::get('home', [HomeController::class, 'residentHome'])
            ->name('home')
            ->middleware('can:home.resident');
    });