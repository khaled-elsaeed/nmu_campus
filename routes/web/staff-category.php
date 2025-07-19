<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaffCategoryController;

Route::prefix('staff-categories')->name('staff-categories.')->group(function () {
    Route::get('/', [StaffCategoryController::class, 'index'])->name('index');
    Route::get('/datatable', [StaffCategoryController::class, 'datatable'])->name('datatable');
    Route::get('/stats', [StaffCategoryController::class, 'stats'])->name('stats');
    Route::get('/all', [StaffCategoryController::class, 'all'])->name('all');
    Route::get('/{id}', [StaffCategoryController::class, 'show'])->name('show');
    Route::post('/', [StaffCategoryController::class, 'store'])->name('store');
    Route::put('/{id}', [StaffCategoryController::class, 'update'])->name('update');
    Route::delete('/{id}', [StaffCategoryController::class, 'destroy'])->name('destroy');
});
