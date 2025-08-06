<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Models\Reservation\Reservation;
use App\Notifications\ReservationActivated;

// ====================
// Language Routes (Global)
// ====================
Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])
    ->name('language.switch')
    ->where('locale', '[a-zA-Z]{2}');

// ====================
// Auth Routes (Global - Outside Locale)
// ====================
require __DIR__.'/web/auth/email_verification.php';

// ====================
// Authentication Routes
// ====================
require __DIR__.'/web/auth/login.php';
require __DIR__.'/web/auth/register.php';
require __DIR__.'/web/auth/password.php';

// ====================
// Protected Routes (Require Authentication)
// ====================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard & Main Pages
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    //=====================
    // Dashboard Management
    //=====================
    require __DIR__.'/web/dashboard.php';


    // ====================
    // Housing Management
    // ====================
    Route::prefix('housing')->name('housing.')->group(function () {
        require __DIR__.'/web/housing/building.php';
        require __DIR__.'/web/housing/apartment.php';
        require __DIR__.'/web/housing/room.php';
    });

    // ====================
    // Resident Management
    // ====================
    Route::prefix('resident')->name('resident.')->group(function () {
        require __DIR__.'/web/resident/student.php';
        require __DIR__.'/web/resident/staff.php';
    });

    // ====================
    // Academic Management
    // ====================
    Route::prefix('academic')->name('academic.')->group(function () {
        require __DIR__.'/web/academic/faculty.php';
        require __DIR__.'/web/academic/program.php';
        require __DIR__.'/web/academic/academic_term.php';
    });

    // ====================
    // Organizational Structure
    // ====================
    require __DIR__.'/web/staff-category.php';
    require __DIR__.'/web/department.php';
    require __DIR__.'/web/campus-units.php';

    // ====================
    // Reservation System
    // ====================
    require __DIR__.'/web/reservation/reservation.php';
    require __DIR__.'/web/reservation/my_reservation.php';
    require __DIR__.'/web/reservation/reservation_request.php';

    // ====================
    // Equipment Management
    // ====================
    require __DIR__.'/web/equipment.php';

    // ====================
    // User Management
    // ====================
    require __DIR__.'/web/user.php';

    // ====================
    // Payment System
    // ====================
    require __DIR__.'/web/payment/payment.php';
    require __DIR__.'/web/payment/insurance.php';

    // ====================
    // Profile Management
    // ====================
    require __DIR__.'/web/profile.php';

    // ====================
    // Geography/Location Data
    // ====================
    require __DIR__.'/web/geography/country.php';
    require __DIR__.'/web/geography/nationality.php';
    require __DIR__.'/web/geography/city.php';
    require __DIR__.'/web/geography/governorate.php';
});

// ====================
// Test/Debug Routes (Development Only)
// ====================
Route::get('/notification', function () {
    $reservation = Reservation::find(1);
    if (!$reservation || !$reservation->user) {
        abort(404, 'Reservation or user not found.');
    }
    return (new ReservationActivated($reservation))
        ->toMail($reservation->user);
});

Route::get('/complete-profile', function () {
    return view('complete-profile');
});
