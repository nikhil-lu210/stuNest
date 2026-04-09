<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\Landlord\Dashboard\DashboardController;

/* ==============================================
===============< Landlord Routes >==============
===============================================*/
Route::controller(DashboardController::class)->prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/dashboard', 'index')->name('dashboard');
});
