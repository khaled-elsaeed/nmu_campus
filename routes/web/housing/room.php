<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Housing\RoomController;

Route::prefix('rooms')->name('rooms.')->group(function () {
    Route::get('/', [RoomController::class, 'index'])->name('index');
    Route::get('/datatable', [RoomController::class, 'datatable'])->name('datatable');
    Route::get('/stats', [RoomController::class, 'stats'])->name('stats');
    Route::get('/all', [RoomController::class, 'all'])->name('all');
    Route::get('/{id}', [RoomController::class, 'show'])->name('show');
    Route::put('/{id}', [RoomController::class, 'update'])->name('update');
    Route::delete('/{id}', [RoomController::class, 'destroy'])->name('destroy');
    Route::patch('/{id}/activate', [RoomController::class, 'activate'])->name('activate');
    Route::patch('/{id}/deactivate', [RoomController::class, 'deactivate'])->name('deactivate');
}); 