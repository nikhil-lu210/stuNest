<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\Explore\ExploreController;

/* ==============================================
===============< Explore Routes >==============
===============================================*/
Route::controller(ExploreController::class)->prefix('explore')->group(function () {
    Route::get('/', 'index')->name('explore');
    Route::get('cities/{country}', 'cities')->name('explore.cities');
    Route::get('areas/{city}', 'areas')->name('explore.areas');
    Route::post('favorites/{property}', 'toggleFavorite')->name('explore.favorites.toggle');
});
