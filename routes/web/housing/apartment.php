<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Housing\ApartmentController;

Route::prefix('apartments')->name('apartments.')->group(function () {
    Route::get('/', [ApartmentController::class, 'index'])->name('index');
    Route::get('/datatable', [ApartmentController::class, 'datatable'])->name('datatable');
    Route::get('/stats', [ApartmentController::class, 'stats'])->name('stats');
    Route::get('/all', [ApartmentController::class, 'all'])->name('all');
    Route::get('/{id}', [ApartmentController::class, 'show'])->name('show');
    Route::put('/{id}', [ApartmentController::class, 'update'])->name('update');
    Route::delete('/{id}', [ApartmentController::class, 'destroy'])->name('destroy');
    Route::patch('/{id}/activate', [ApartmentController::class, 'activate'])->name('activate');
    Route::patch('/{id}/deactivate', [ApartmentController::class, 'deactivate'])->name('deactivate');
}); 