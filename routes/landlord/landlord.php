<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landlord\DashboardController;

/*
|--------------------------------------------------------------------------
| Landlord (marketplace) — URL prefix: /landlord
|--------------------------------------------------------------------------
*/
Route::prefix('landlord')
    ->name('landlord.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
