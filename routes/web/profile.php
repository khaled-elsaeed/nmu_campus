<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Profile routes
Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('fetch', [ProfileController::class, 'fetch'])->name('fetch');
    Route::post('submit', [ProfileController::class, 'submit'])->name('submit');
});