<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\PaymentController;

Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::get('/datatable', [PaymentController::class, 'datatable'])->name('datatable');
    Route::get('/stats', [PaymentController::class, 'stats'])->name('stats');
    Route::get('/all', [PaymentController::class, 'all'])->name('all');
    Route::get('/payment/{id}/details', [PaymentController::class, 'details'])->name('details');
    Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
    Route::post('/', [PaymentController::class, 'store'])->name('store');
    Route::put('/{id}', [PaymentController::class, 'update'])->name('update');
    Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('destroy');
}); 