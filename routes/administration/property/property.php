<?php

use App\Http\Controllers\Administration\Property\PropertyController;
use App\Livewire\Property\CreateListing;
use Illuminate\Support\Facades\Route;

Route::prefix('properties')->name('properties.')->group(function () {
    Route::get('/', [PropertyController::class, 'index'])->name('index');
    Route::get('/pending-review', [PropertyController::class, 'pendingReview'])->name('pending');
    Route::get('/live', [PropertyController::class, 'live'])->name('live');
    Route::get('/rented', [PropertyController::class, 'rented'])->name('rented');
    Route::get('/drafts-archived', [PropertyController::class, 'draftsArchived'])->name('drafts_archived');
    Route::get('/geography/cities/{country}', [PropertyController::class, 'citiesJson'])->name('geography.cities');
    Route::get('/geography/areas/{city}', [PropertyController::class, 'areasJson'])->name('geography.areas');
    Route::get('/create', CreateListing::class)->name('create');
    Route::get('/{property}', [PropertyController::class, 'show'])->name('show');
    Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('edit');
    Route::put('/{property}', [PropertyController::class, 'update'])->name('update');
});
