<?php

use App\Http\Controllers\Client\Agent\DashboardController as AgentDashboardController;
use App\Http\Controllers\Client\Landlord\DashboardController as LandlordDashboardController;
use App\Http\Controllers\Client\Student\DashboardController as StudentDashboardController;
use App\Livewire\Institute\InstituteCreateStudent;
use App\Livewire\Institute\InstituteOverview;
use App\Livewire\Institute\InstituteStudents;
use Illuminate\Support\Facades\Route;

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
            Route::get('/dashboard', InstituteOverview::class)->name('dashboard');
            Route::get('/students', InstituteStudents::class)->name('students.index');
            Route::get('/students/unverified', InstituteStudents::class)->name('students.unverified');
            Route::get('/students/create', InstituteCreateStudent::class)->name('students.create');
            Route::view('/settings', 'client.institute.settings.index')->name('settings');
        });

        Route::prefix('agent')->name('agent.')->group(function () {
            Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('dashboard');
        });
    });
