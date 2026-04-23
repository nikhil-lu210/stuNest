<?php

use App\Livewire\Institute\InstituteCreateStudent;
use App\Livewire\Institute\InstituteMessages;
use App\Livewire\Institute\InstituteProperties;
use App\Livewire\Institute\InstituteStudents;
use App\Livewire\InstituteDashboard;
use App\Livewire\Property\CreateListing;
use Illuminate\Support\Facades\Route;

/* ==============================================
===============< Institute Routes (v2) >==============
===============================================*/
Route::prefix('institute')->name('institute.')->group(function () {
    Route::get('/dashboard', InstituteDashboard::class)->name('dashboard');
    Route::get('/messages', InstituteMessages::class)->name('messages.index');
    Route::get('/students', InstituteStudents::class)->name('students.index');
    Route::get('/students/unverified', InstituteStudents::class)->name('students.unverified');
    Route::get('/students/create', InstituteCreateStudent::class)->name('students.create');
    Route::get('/properties', InstituteProperties::class)->name('properties.index');
    Route::get('/create-listing', CreateListing::class)->name('create-listing');
    Route::get('/listings/{property}/edit', CreateListing::class)->name('listings.edit');
    Route::view('/settings', 'client.institute.settings.index')->name('settings');
});
