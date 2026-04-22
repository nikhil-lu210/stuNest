<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\Landlord\Dashboard\DashboardController;
use App\Livewire\Landlord\LandlordOverview;

/* ==============================================
===============< Landlord Routes >==============
===============================================*/
Route::prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/dashboard', LandlordOverview::class)->name('dashboard');
});

Route::controller(DashboardController::class)->prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/properties', 'properties')->name('properties.index');
    Route::get('/applications', 'applications')->name('applications.index');
    Route::get('/messages', 'messages')->name('messages.index');
    Route::get('/notifications', 'notifications')->name('notifications');
    Route::get('/settings', 'settings')->name('settings.index');
});
