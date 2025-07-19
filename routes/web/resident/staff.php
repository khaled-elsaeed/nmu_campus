<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Resident\StaffController;

Route::prefix('staff')->name('staff.')->group(function () {
    Route::get('/', [StaffController::class, 'index'])->name('index');
    Route::get('/datatable', [StaffController::class, 'datatable'])->name('datatable');
    Route::get('/stats', [StaffController::class, 'stats'])->name('stats');
    Route::get('/{id}', [StaffController::class, 'show'])->name('show');
    Route::post('/', [StaffController::class, 'store'])->name('store');
    Route::put('/{id}', [StaffController::class, 'update'])->name('update');
    Route::delete('/{id}', [StaffController::class, 'destroy'])->name('destroy');
}); 