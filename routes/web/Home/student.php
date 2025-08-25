<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Home\StudentHomeController;

// Group all student home routes under a single prefix and controller
Route::prefix('home-student')
    ->name('home.student.')
    ->controller(StudentHomeController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/user-detail', 'userDetail')->name('user-detail');
        Route::get('/active-reservation-details', 'activeReservationDetails')->name('active-reservation-details');
        Route::get('/active-reservation-neighbors', 'getActiveReservationNeighbors')->name('active-reservation-neighbors');
        Route::get('/upcoming-events', 'getUpcomingEvents')->name('upcoming-events');
        Route::get('/reservation-requests', 'getReservationRequests')->name('reservation-requests');
        Route::get('/new-request-data', 'getNewRequestData')->name('new-request-data');
    });