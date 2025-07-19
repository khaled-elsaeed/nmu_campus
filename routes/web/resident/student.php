<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Resident\StudentController;

Route::prefix('students')->name('students.')->controller(StudentController::class)->group(function () {
    Route::get('datatable', 'datatable')->name('datatable');
    Route::get('stats', 'stats')->name('stats');
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('{id}', 'show')->name('show');
    Route::put('{id}', 'update')->name('update');
    Route::patch('{id}', 'update');
    Route::delete('{id}', 'destroy')->name('destroy');
}); 