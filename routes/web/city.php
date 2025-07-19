<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;

Route::get('all/{id}', [CityController::class, 'all'])->name('all');

Route::resource('cities', CityController::class); 