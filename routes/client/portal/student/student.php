<?php

use App\Http\Controllers\Client\Student\Dashboard\DashboardController;
use App\Livewire\Property\CreateListing;
use Illuminate\Support\Facades\Route;

/* ==============================================
===============< Student Routes >==============
===============================================*/
Route::middleware('auth')
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::controller(DashboardController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('dashboard');
            Route::get('/saved', 'saved')->name('saved');
            Route::get('/listings', 'listings')->name('listings.index');
            Route::get('/messages', 'messages')->name('messages');
            Route::get('/settings', 'settings')->name('settings');
            Route::get('/notifications', 'notifications')->name('notifications');
        });

        Route::get('/create-listing', CreateListing::class)->name('create-listing');
        Route::get('/listings/{property}/edit', CreateListing::class)->name('listings.edit');
    });
