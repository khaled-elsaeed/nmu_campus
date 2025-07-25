<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reservation\ReservationController;

Route::prefix('reservations')->name('reservations.')->group(function () {
    Route::get('/', [ReservationController::class, 'index'])->name('index');
    Route::get('/datatable', [ReservationController::class, 'datatable'])->name('datatable');
    Route::get('/stats', [ReservationController::class, 'stats'])->name('stats');
    Route::get('/create', [ReservationController::class, 'create'])->name('create');
    Route::get('/check-in', [ReservationController::class, 'showCheckInForm'])->name('check-in');
    Route::post('/checkin', [ReservationController::class, 'checkin'])->name('checkin');
    Route::post('/checkout', [ReservationController::class, 'checkout'])->name('checkout');
    Route::post('/', [ReservationController::class, 'store'])->name('store');
    Route::delete('/{id}', [ReservationController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/cancel', [ReservationController::class, 'cancel'])->name('cancel');
    // Add search by reservation number
    Route::get('/find-by-number', [ReservationController::class, 'findByNumber'])->name('findByNumber');
});