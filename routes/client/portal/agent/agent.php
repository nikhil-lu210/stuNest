<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\Agent\Dashboard\DashboardController;

/* ==============================================
===============< Agent Routes (v2) >==============
===============================================*/
Route::controller(DashboardController::class)->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard', 'index')->name('dashboard');
});
