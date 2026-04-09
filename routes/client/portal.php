<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\Agent\DashboardController as AgentDashboardController;
use App\Http\Controllers\Client\Institute\DashboardController as InstituteDashboardController;
use App\Http\Controllers\Client\Landlord\DashboardController as LandlordDashboardController;
use App\Http\Controllers\Client\Student\DashboardController as StudentDashboardController;

/*
|--------------------------------------------------------------------------
| Client module — authenticated marketplace users (students, landlords,
| institutions, agents). Staff administration stays under /administration.
|--------------------------------------------------------------------------
*/
Route::prefix('client')
    ->name('client.')
    ->group(function () {
        Route::prefix('student')->name('student.')->group(function () {
            Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        });

        Route::prefix('landlord')->name('landlord.')->group(function () {
            Route::get('/dashboard', [LandlordDashboardController::class, 'index'])->name('dashboard');
        });

        Route::prefix('institute')->name('institute.')->group(function () {
            Route::get('/dashboard', [InstituteDashboardController::class, 'index'])->name('dashboard');
        });

        Route::prefix('agent')->name('agent.')->group(function () {
            Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('dashboard');
        });
    });
