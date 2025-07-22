<?php

use Illuminate\Support\Facades\Route;

// ====================
// Home Routes
// ====================
Route::get('/', fn () => view('home.admin'));
Route::get('/home', fn () => view('home.admin'))->name('home');

// ====================
// Housing Routes
// ====================
Route::prefix('housing')->name('housing.')->group(function () {
    require __DIR__.'/web/housing/building.php';
    require __DIR__.'/web/housing/apartment.php';
    require __DIR__.'/web/housing/room.php';
});

// ====================
// Resident Routes
// ====================
Route::prefix('resident')->name('resident.')->group(function () {
    require __DIR__.'/web/resident/student.php';
    require __DIR__.'/web/resident/staff.php';
});

// ====================
// Academic Routes
// ====================
Route::prefix('academic')->name('academic.')->group(function () {
    require __DIR__.'/web/academic/faculty.php';
    require __DIR__.'/web/academic/program.php';
    require __DIR__.'/web/academic/academic_term.php';
});

// ====================
// Staff & Department & Campus Units
// ====================
require __DIR__.'/web/staff-category.php';
require __DIR__.'/web/department.php';
require __DIR__.'/web/campus-units.php';

// ====================
// Reservation & Equipment & User
// ====================
require __DIR__.'/web/reservation/reservation.php';
require __DIR__.'/web/equipment.php';
require __DIR__.'/web/user.php';
require __DIR__.'/web/reservation/reservation_request.php';

// ====================
// Payment
// ====================
require __DIR__.'/web/payment/payment.php';
require __DIR__.'/web/payment/insurance.php';

// ====================
// Location Routes
// ====================
Route::prefix('countries')->name('countries.')->group(function () {
    require __DIR__.'/web/country.php';
});
Route::prefix('governorates')->name('governorates.')->group(function () {
    require __DIR__.'/web/governorate.php';
});
Route::prefix('cities')->name('cities.')->group(function () {
    require __DIR__.'/web/city.php';
});

// ====================
// Test/Debug Routes
// ====================
use App\Models\Reservation\Reservation;
use App\Notifications\ReservationActivated;

Route::get('/notification', function () {
    $reservation = Reservation::find(1);
    return (new ReservationActivated($reservation))
        ->toMail($reservation->user);
});
