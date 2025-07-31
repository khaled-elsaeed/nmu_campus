<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Models\Reservation\Reservation;
use App\Notifications\ReservationActivated;
use App\Http\Controllers\HomeController;

Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])
    ->name('language.switch')
    ->where('locale', '[a-zA-Z]{2}');

Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => '[a-zA-Z]{2}'],
], function () {


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
    require __DIR__.'/web/reservation/my_reservation.php';
    require __DIR__.'/web/equipment.php';
    require __DIR__.'/web/user.php';
    require __DIR__.'/web/reservation/reservation_request.php';

    // ====================
    // Payment
    // ====================
    require __DIR__.'/web/payment/payment.php';
    require __DIR__.'/web/payment/insurance.php';

    // ====================
    // Profile Routes
    // ====================
    require __DIR__.'/web/profile.php';

    // ====================
    // Location Routes
    // ====================
    require __DIR__.'/web/geography/country.php';
    require __DIR__.'/web/geography/nationality.php';
    require __DIR__.'/web/geography/city.php';
    require __DIR__.'/web/geography/governorate.php';

    // ====================
    // Test/Debug Routes
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

    require __DIR__.'/web/auth/login.php';
    require __DIR__.'/web/auth/register.php';
    require __DIR__.'/web/auth/password.php';

    Route::middleware(['auth'])->group(function () {

        // Main landing after login (neutral)
        Route::get('/home', [HomeController::class, 'index'])->name('home');
    
        // Analytics/reporting section
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
        // User profile/settings
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    });
});

// ====================
// Auth Routes
// ====================
require __DIR__.'/web/auth/email_verification.php';

