<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Profile routes
Route::prefix('profile')->name('profile.')->group(function () {

    // Resident group inside profile
    Route::prefix('resident')->name('resident.')->group(function () {

        // Student group inside resident
        Route::prefix('student')->name('student.')->group(function () {
            Route::get('complete', [ProfileController::class, 'index'])->name('complete');
            Route::get('fetch', [ProfileController::class, 'fetch'])->name('fetch');
            Route::post('submit', [ProfileController::class, 'submit'])->name('submit');
        });
    });
});