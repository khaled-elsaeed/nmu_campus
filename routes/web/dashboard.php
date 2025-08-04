<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// ====================
// Dashboard Routes
// ====================

// ===== Admin Dashboard Routes =====
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Specific Routes First
        Route::get('dashboard/stats', [DashboardController::class, 'adminStats'])
            ->name('dashboard.stats')
            ->middleware('can:dashboard.admin');

        // Main Routes
        Route::get('dashboard', [DashboardController::class, 'adminDashboard'])
            ->name('dashboard')
            ->middleware('can:dashboard.admin');
    });

