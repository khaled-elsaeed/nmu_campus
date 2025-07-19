<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;

Route::resource('countries', CountryController::class); 