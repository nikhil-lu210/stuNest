<?php

use App\Http\Controllers\Client\Landlord\Dashboard\DashboardController;
use App\Livewire\Landlord\LandlordApplications;
use App\Livewire\Landlord\LandlordMessages;
use App\Livewire\Landlord\LandlordOverview;
use App\Livewire\Landlord\LandlordProperties;
use App\Livewire\Property\CreateListing;
use Illuminate\Support\Facades\Route;

/* ==============================================
===============< Landlord Routes >==============
===============================================*/
Route::prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/dashboard', LandlordOverview::class)->name('dashboard');
    Route::get('/properties', LandlordProperties::class)->name('properties.index');
    Route::get('/applications', LandlordApplications::class)->name('applications.index');
    Route::get('/messages', LandlordMessages::class)->name('messages.index');
    Route::get('/create-listing', CreateListing::class)->name('create-listing');
    Route::get('/listings/{property}/edit', CreateListing::class)->name('listings.edit');
});

Route::controller(DashboardController::class)->prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/notifications', 'notifications')->name('notifications');
    Route::get('/settings', 'settings')->name('settings.index');
});
