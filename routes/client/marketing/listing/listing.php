<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\Listing\ListingController;

/* ==============================================
===============< Listing Routes >==============
===============================================*/
Route::controller(ListingController::class)->prefix('listings')->group(function () {
    Route::get('/{slug}', 'show')->name('listing.show');
});
