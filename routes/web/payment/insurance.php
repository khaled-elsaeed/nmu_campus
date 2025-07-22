<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\InsuranceController;

Route::prefix('insurances')->name('insurances.')->group(function () {
    Route::get('/', [InsuranceController::class, 'index'])->name('index');
    Route::get('/datatable', [InsuranceController::class, 'datatable'])->name('datatable');
    Route::get('/stats', [InsuranceController::class, 'stats'])->name('stats');
    Route::get('/all', [InsuranceController::class, 'all'])->name('all');
    Route::get('/{id}', [InsuranceController::class, 'show'])->name('show');
    Route::post('/', [InsuranceController::class, 'store'])->name('store');
    Route::put('/{id}', [InsuranceController::class, 'update'])->name('update');
    Route::delete('/{id}', [InsuranceController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/cancel', [InsuranceController::class, 'cancel'])->name('cancel');
    Route::post('/{id}/refund', [InsuranceController::class, 'refund'])->name('refund');
}); 