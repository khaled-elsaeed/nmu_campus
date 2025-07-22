<?php

use App\Http\Controllers\Academic\ProgramController;
use Illuminate\Support\Facades\Route;

// ====================
// Program Routes
// ====================

Route::prefix('programs')
    ->name('programs.')
    ->controller(ProgramController::class)
    ->group(function () {
        // ===== Specific Routes First =====
        Route::get('datatable', 'datatable')->name('datatable');
        Route::get('stats', 'stats')->name('stats');
        Route::get('faculties', 'getFaculties')->name('faculties');
        // No middleware: not sensitive, for dropdown
        Route::get('all/{id}', 'all')->name('all');

        // ===== CRUD Operations =====
        // List & View
        Route::get('/', 'index')->name('index');
        Route::get('{program}', 'show')->name('show');
        
        // Create
        Route::post('/', 'store')->name('store');
        
        // Update
        Route::put('{program}', 'update')->name('update');
        Route::patch('{program}', 'update');
        
        // Delete
        Route::delete('{program}', 'destroy')->name('destroy');
    }); 