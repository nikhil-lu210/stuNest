<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\Institute\Dashboard\DashboardController;

/* ==============================================
===============< Institute Routes (v2) >==============
===============================================*/
Route::controller(DashboardController::class)->prefix('institute')->name('institute.')->group(function () {
    Route::get('/dashboard', 'index')->name('dashboard');
});
