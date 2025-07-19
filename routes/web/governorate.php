<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GovernorateController;

Route::get('all', [GovernorateController::class, 'all'])->name('all');
Route::resource('governorates', GovernorateController::class); 