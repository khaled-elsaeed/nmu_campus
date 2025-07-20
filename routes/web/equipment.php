<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipmentController;

Route::prefix('equipment')->name('equipment.')->group(function () {
    Route::get('/all/', [EquipmentController::class, 'all'])->name('all');
}); 