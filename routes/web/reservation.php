<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;

Route::prefix('reservations')->name('reservations.')->group(function () {
    Route::get('/', [ReservationController::class, 'index'])->name('index');
    Route::get('/datatable', [ReservationController::class, 'datatable'])->name('datatable');
    Route::get('/stats', [ReservationController::class, 'stats'])->name('stats');
    Route::get('/{id}', [ReservationController::class, 'show'])->name('show');
    Route::put('/{id}', [ReservationController::class, 'update'])->name('update');
    Route::delete('/{id}', [ReservationController::class, 'destroy'])->name('destroy');
}); 