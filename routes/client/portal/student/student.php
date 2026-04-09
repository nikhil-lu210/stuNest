<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\Student\Dashboard\DashboardController;

/* ==============================================
===============< Student Routes >==============
===============================================*/
Route::controller(DashboardController::class)->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', 'index')->name('dashboard');
});
