<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Housing\BuildingController;

Route::get('/', function () {
    return view('home.admin');
});

Route::get('/home', function () {
    return view('home.admin');
})->name('home');

// Housing routes moved to routes/web/housing/building.php
Route::prefix('housing')->name('housing.')->group(function () {
    require __DIR__.'/web/housing/building.php';
    require __DIR__.'/web/housing/apartment.php';
    require __DIR__.'/web/housing/room.php';
});

Route::prefix('resident')->name('resident.')->group(function () {
    require __DIR__.'/web/resident/student.php';
    require __DIR__.'/web/resident/staff.php';
});

Route::prefix('academic')->name('academic.')->group(function(){
    require __DIR__.'/web/faculty.php';
    require __DIR__.'/web/program.php';
    require __DIR__.'/web/academic_term.php';

});

    require __DIR__.'/web/staff-category.php';
    require __DIR__.'/web/department.php';
    require __DIR__.'/web/reservation.php';
    require __DIR__.'/web/academic_term.php';



Route::prefix('countries')->name('countries.')->group(function () {
    require __DIR__.'/web/country.php';
});

Route::prefix('governorates')->name('governorates.')->group(function () {
    require __DIR__.'/web/governorate.php';
});

Route::prefix('cities')->name('cities.')->group(function () {
    require __DIR__.'/web/city.php';
});
