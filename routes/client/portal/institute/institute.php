<?php

use App\Livewire\Institute\InstituteAllApplications;
use App\Livewire\Institute\InstituteApplicationDetails;
use App\Livewire\Institute\InstituteCreateStudent;
use App\Livewire\Institute\InstituteMessages;
use App\Livewire\Institute\InstituteOurApplications;
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
    Route::get('/applications/all', InstituteAllApplications::class)->name('applications.all');
    Route::get('/applications/our', InstituteOurApplications::class)->name('applications.our');
    Route::get('/applications/{applicationId}', InstituteApplicationDetails::class)->name('applications.show');
    Route::get('/properties', InstituteProperties::class)->name('properties.index');
    Route::get('/create-listing', CreateListing::class)->name('create-listing');
    Route::get('/listings/{property}/edit', CreateListing::class)->name('listings.edit');
    Route::view('/settings', 'client.institute.settings.index')->name('settings');
});
