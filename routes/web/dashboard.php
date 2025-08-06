<?php

use App\Http\Controllers\Dashboard\AdminDashboardController;
use Illuminate\Support\Facades\Route;

// ====================
// Dashboard Routes
// ====================

// ===== Admin Dashboard Routes =====
Route::prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {

        Route::controller(AdminDashboardController::class)
            ->group(function () {
                Route::get('admin', 'index')
                    ->name('admin.index');

                Route::get('admin/stats', 'stats')
                    ->name('admin.stats');
            });
    });