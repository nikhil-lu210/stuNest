<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\DashboardController;

/*
|--------------------------------------------------------------------------
| Student (marketplace) — URL prefix: /student
|--------------------------------------------------------------------------
*/
Route::prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
