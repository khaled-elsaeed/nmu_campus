<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reservation\ReservationRequestController;

Route::prefix('reservation-requests')->name('reservation-requests.')->group(function () {
    Route::get('/', [ReservationRequestController::class, 'index'])->name('index');
    Route::get('/stats', [ReservationRequestController::class, 'stats'])->name('stats');
    Route::get('/datatable', [ReservationRequestController::class, 'datatable'])->name('datatable');
    Route::get('/{id}', [ReservationRequestController::class, 'show'])->name('show');
    Route::put('/{id}', [ReservationRequestController::class, 'update'])->name('update');
    Route::post('/{id}', [ReservationRequestController::class, 'accept'])->name('accept');
    Route::delete('/{id}', [ReservationRequestController::class, 'cancel'])->name('cancel');
}); 