<?php

use App\Http\Controllers\Geography\CountryController;
use Illuminate\Support\Facades\Route;

// ====================
// Country Routes
// ====================

Route::prefix('countries')
    ->name('countries.')
    ->controller(CountryController::class)
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