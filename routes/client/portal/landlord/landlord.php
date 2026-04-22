<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\Landlord\Dashboard\DashboardController;
use App\Livewire\Landlord\LandlordOverview;
use App\Livewire\Landlord\LandlordProperties;
use App\Livewire\Property\CreateListing;

/* ==============================================
===============< Landlord Routes >==============
===============================================*/
Route::prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/dashboard', LandlordOverview::class)->name('dashboard');
    Route::get('/properties', LandlordProperties::class)->name('properties.index');
    Route::get('/create-listing', CreateListing::class)->name('create-listing');
    Route::get('/listings/{property}/edit', CreateListing::class)->name('listings.edit');
});

Route::controller(DashboardController::class)->prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/applications', 'applications')->name('applications.index');
    Route::get('/messages', 'messages')->name('messages.index');
    Route::get('/notifications', 'notifications')->name('notifications');
    Route::get('/settings', 'settings')->name('settings.index');
});
