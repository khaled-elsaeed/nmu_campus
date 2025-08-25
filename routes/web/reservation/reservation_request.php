<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reservation\ReservationRequestController;

Route::prefix('reservation-requests')->name('reservation-requests.')->group(function () {
    Route::get('/', [ReservationRequestController::class, 'index'])->name('index');
    Route::post('/', [ReservationRequestController::class, 'store'])->name('store');
    Route::get('/insights', [ReservationRequestController::class, 'insights'])->name('insights');
    Route::get('/analysis', [ReservationRequestController::class, 'analysis'])->name('analysis');
    Route::get('/stats', [ReservationRequestController::class, 'stats'])->name('stats');
    Route::get('/datatable', [ReservationRequestController::class, 'datatable'])->name('datatable');
    Route::get('/{id}', [ReservationRequestController::class, 'show'])->name('show');
    Route::put('/{id}', [ReservationRequestController::class, 'update'])->name('update');
    Route::post('/{id}', [ReservationRequestController::class, 'accept'])->name('accept');
    Route::delete('/{id}', [ReservationRequestController::class, 'cancel'])->name('cancel');
    
    // Analytics Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/stats', [ReservationRequestController::class, 'analyticsStats'])->name('stats');
        Route::get('/accommodation-types', [ReservationRequestController::class, 'analyticsAccommodationTypes'])->name('accommodation-types');
        Route::get('/room-types', [ReservationRequestController::class, 'analyticsRoomTypes'])->name('room-types');
        Route::get('/faculties', [ReservationRequestController::class, 'analyticsFaculties'])->name('faculties');
        Route::get('/governorates', [ReservationRequestController::class, 'analyticsGovernorates'])->name('governorates');
        Route::get('/programs', [ReservationRequestController::class, 'analyticsPrograms'])->name('programs');
        Route::get('/genders', [ReservationRequestController::class, 'analyticsGenders'])->name('genders');
        Route::get('/sibling-preferences', [ReservationRequestController::class, 'analyticsSiblingPreferences'])->name('sibling-preferences');
        Route::get('/parent-abroad', [ReservationRequestController::class, 'analyticsParentAbroad'])->name('parent-abroad');
    });
});