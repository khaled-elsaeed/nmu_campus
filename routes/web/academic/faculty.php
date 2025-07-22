<?php

use App\Http\Controllers\Academic\FacultyController;
use Illuminate\Support\Facades\Route;

// ====================
// Faculty Routes
// ====================

Route::prefix('faculties')
    ->name('faculties.')
    ->controller(FacultyController::class)
    ->group(function () {
        // ===== Specific Routes First =====
        Route::get('datatable', 'datatable')->name('datatable');
        Route::get('stats', 'stats')->name('stats');
        Route::get('all', 'all')->name('all');

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