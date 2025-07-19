<?php

use App\Http\Controllers\Academic\AcademicTermController;
use Illuminate\Support\Facades\Route;

// ====================
// Term Routes
// ====================

Route::prefix('academic-terms')
    ->name('academic_terms.')
    ->controller(AcademicTermController::class)
    ->group(function () {
        // ===== Specific Routes First =====
        Route::get('datatable', 'datatable')->name('datatable');
        Route::get('stats', 'stats')->name('stats');
        // No middleware: not sensitive, for dropdown
        Route::get('all', 'all')->name('all');
        Route::get('all-with-inactive', 'allWithInactive')->name('all.with_inactive');

        // ===== CRUD Operations =====
        // List & View
        Route::get('/', 'index')->name('index');
        Route::get('{term}', 'show')->name('show');
        
        // Create
        Route::post('/', 'store')->name('store');
        
        // Update
        Route::put('{term}', 'update')->name('update');
        Route::patch('{term}', 'update');
        
        // Delete
        Route::delete('{term}', 'destroy')->name('destroy');

        Route::post('{term}/start', 'startTerm')->name('start');
        Route::post('{term}/end', 'endTerm')->name('end');
        Route::post('{term}/activate', 'activateTerm')->name('activate');
        Route::post('{term}/deactivate', 'deactivateTerm')->name('deactivate');
    });