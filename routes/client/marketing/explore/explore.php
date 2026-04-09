<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\Explore\ExploreController;

/* ==============================================
===============< Explore Routes >==============
===============================================*/
Route::controller(ExploreController::class)->prefix('explore')->group(function () {
    Route::get('/', 'index')->name('explore');
});
