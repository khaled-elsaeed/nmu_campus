<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

// ====================
// Role Routes
// ====================

Route::prefix('roles')
    ->name('roles.')
    ->controller(RoleController::class)
    ->group(function () {
        // ===== Specific Routes First =====
        Route::get('datatable', 'datatable')->name('datatable');
        Route::get('stats', 'stats')->name('stats');
        Route::get('permissions', 'getPermissions')->name('permissions');

        // ===== CRUD Operations =====
        // List & View
        Route::get('/', 'index')->name('index');
        Route::get('{role}', 'show')->name('show');
        
        // Create
        Route::post('/', 'store')->name('store');
        
        // Update
        Route::put('{role}', 'update')->name('update');
        Route::patch('{role}', 'update');
        
        // Delete
        Route::delete('{role}', 'destroy')->name('destroy');
    }); 