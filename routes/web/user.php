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
        Route::get('all', 'all')->name('all');
        Route::get('roles', 'getRoles')->name('roles');
        Route::get('find-by-national-id', 'findByNationalId')->name('findByNationalId');

        // ===== CRUD Operations =====
        // List & View
        Route::get('/', 'index')->name('index');
        Route::get('{id}', 'show')->name('show');
        
        // Create
        Route::post('/', 'store')->name('store');
        
        // Update
        Route::put('{id}', 'update')->name('update');
        Route::patch('{id}', 'update');
        
        // Delete
        Route::delete('{id}', 'destroy')->name('destroy');
    }); 