<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ====================
// User Routes
// ====================

Route::prefix('users')
    ->name('users.')
    ->controller(UserController::class)
    ->group(function () {
        // ===== Specific Routes First =====
        Route::get('datatable', 'datatable')->name('datatable');
        Route::get('stats', 'stats')->name('stats');
        Route::get('roles', 'getRoles')->name('roles');

        // ===== CRUD Operations =====
        // List & View
        Route::get('/', 'index')->name('index');
        Route::get('{user}', 'show')->name('show');
        
        // Create
        Route::post('/', 'store')->name('store');
        
        // Update
        Route::put('{user}', 'update')->name('update');
        Route::patch('{user}', 'update');
        
        // Delete
        Route::delete('{user}', 'destroy')->name('destroy');
    }); 