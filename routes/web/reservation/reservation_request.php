<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reservation\ReservationRequestController;

Route::prefix('reservation-requests')->name('reservation-requests.')->group(function () {
    Route::get('/', [ReservationRequestController::class, 'index'])->name('index');
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
        Route::get('/overview', [ReservationRequestController::class, 'analyticsOverview'])->name('overview');
        Route::get('/accommodation-types', [ReservationRequestController::class, 'analyticsAccommodationTypes'])->name('accommodation-types');
        Route::get('/room-types', [ReservationRequestController::class, 'analyticsRoomTypes'])->name('room-types');
        Route::get('/bed-counts', [ReservationRequestController::class, 'analyticsBedCounts'])->name('bed-counts');
        Route::get('/faculties', [ReservationRequestController::class, 'analyticsFaculties'])->name('faculties');
        Route::get('/programs', [ReservationRequestController::class, 'analyticsPrograms'])->name('programs');
        Route::get('/genders', [ReservationRequestController::class, 'analyticsGenders'])->name('genders');
        Route::get('/monthly-trends', [ReservationRequestController::class, 'analyticsMonthlyTrends'])->name('monthly-trends');
        Route::get('/sibling-preferences', [ReservationRequestController::class, 'analyticsSiblingPreferences'])->name('sibling-preferences');
        Route::get('/status-timeline', [ReservationRequestController::class, 'analyticsStatusTimeline'])->name('status-timeline');
        Route::get('/period-types', [ReservationRequestController::class, 'analyticsPeriodTypes'])->name('period-types');
        Route::get('/summary-stats', [ReservationRequestController::class, 'analyticsSummaryStats'])->name('summary-stats');
        Route::get('/export', [ReservationRequestController::class, 'analyticsExport'])->name('export');
    });
});