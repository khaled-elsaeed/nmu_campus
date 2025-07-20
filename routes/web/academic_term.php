<?php

use App\Http\Controllers\Academic\AcademicTermController;
use Illuminate\Support\Facades\Route;

// ====================
// Academic Term Routes
// ====================

Route::prefix('academic-terms')
    ->name('academic_terms.')
    ->controller(AcademicTermController::class)
    ->group(function () {
        // ===== Data & Stats =====
        Route::get('datatable', 'datatable')->name('datatable');
        Route::get('stats', 'stats')->name('stats');
        Route::get('all', 'all')->name('all'); 
        Route::get('all-with-inactive', 'allWithInactive')->name('all.with_inactive');

        // ===== CRUD Operations =====
        // index
        Route::get('/', 'index')->name('index');
        // View
        Route::get('{id}', 'show')->name('show');
        // Create
        Route::post('/', 'store')->name('store');
        // Update
        Route::put('{id}', 'update')->name('update');
        // Delete
        Route::delete('{id}', 'destroy')->name('destroy');

        // ===== State Transitions =====
        Route::post('{id}/start', 'startTerm')->name('start');
        Route::post('{id}/end', 'endTerm')->name('end');
        Route::patch('{id}/activate', 'activate')->name('activate');
        Route::patch('{id}/deactivate', 'deactivate')->name('deactivate');
    });