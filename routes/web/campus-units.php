<?php

use App\Http\Controllers\CampusUnitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Campus Units Routes
|--------------------------------------------------------------------------
|
| Here are the routes for campus units management.
|
*/

Route::prefix('campus-units')->name('campus-units.')->group(function () {
    Route::get('/all', [CampusUnitController::class, 'all'])->name('all');
}); 