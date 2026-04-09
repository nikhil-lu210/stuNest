<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\SiteMap\SiteMapController;

/* ==============================================
===============< Site map Routes >==============
===============================================*/
Route::controller(SiteMapController::class)->prefix('site-map')->group(function () {
    Route::get('/', 'index')->name('site_map');
});
