<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Housing\BuildingController;

// Group custom endpoints for buildings
Route::prefix('buildings')->name('buildings.')->controller(BuildingController::class)->group(function () {
    // DataTable AJAX endpoint
    Route::get('datatable', 'datatable')->name('datatable');
    // Stats endpoint
    Route::get('stats', 'stats')->name('stats');

    Route::get('all','all')->name('all');


    // Resourceful routes for buildings (excluding create/edit)
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('{id}', 'show')->name('show');
    Route::put('{id}', 'update')->name('update');
    Route::patch('{id}', 'update');
    Route::delete('{id}', 'destroy')->name('destroy');
    Route::patch('{id}/activate', 'activate')->name('activate');
    Route::patch('{id}/deactivate', 'deactivate')->name('deactivate');
});