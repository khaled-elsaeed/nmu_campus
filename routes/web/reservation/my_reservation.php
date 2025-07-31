<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reservation\MyReservationController;

Route::prefix('my-reservations')->name('my_reservations.')->group(function () {
    Route::get('/', [MyReservationController::class, 'index'])->name('index');
    Route::get('/card-data', [MyReservationController::class, 'cardData'])->name('cardData');
    Route::post('/', [MyReservationController::class, 'store'])->name('store');
    Route::post('/{id}/cancel', [MyReservationController::class, 'cancel'])->name('cancel');
    Route::post('/checkout', [MyReservationController::class, 'checkout'])->name('checkout');
    Route::delete('/{id}', [MyReservationController::class, 'destroy'])->name('destroy');
});