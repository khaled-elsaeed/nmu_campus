<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;

Route::prefix('departments')->name('departments.')->group(function () {
    Route::get('/', [DepartmentController::class, 'index'])->name('index');
    Route::get('/datatable', [DepartmentController::class, 'datatable'])->name('datatable');
    Route::get('/stats', [DepartmentController::class, 'stats'])->name('stats');
    Route::get('/all', [DepartmentController::class, 'all'])->name('all');
    Route::get('/{id}', [DepartmentController::class, 'show'])->name('show');
    Route::post('/', [DepartmentController::class, 'store'])->name('store');
    Route::put('/{id}', [DepartmentController::class, 'update'])->name('update');
    Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('destroy');
}); 