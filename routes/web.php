<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Models\Reservation\Reservation;
use App\Notifications\ReservationActivated;

Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])
    ->name('language.switch')
    ->where('locale', '[a-zA-Z]{2}');

Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => '[a-zA-Z]{2}'],
], function () {

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
    // Profile Routes
    // ====================
    require __DIR__.'/web/profile.php';

    // ====================
    // Location Routes
    // ====================
    require __DIR__.'/web/countries.php';
    require __DIR__.'/web/nationalities.php';
    require __DIR__.'/web/city.php';

    Route::prefix('governorates')->name('governorates.')->group(function () {
        require __DIR__.'/web/governorate.php';
    });


    // ====================
    // Test/Debug Routes
    // ====================
    Route::get('/notification', function () {
        $reservation = Reservation::find(1);
        return (new ReservationActivated($reservation))
            ->toMail($reservation->user);
    });

    Route::get('/complete-profile', function () {
        return view('complete-profile');
    });

});
