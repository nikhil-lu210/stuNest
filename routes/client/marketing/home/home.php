<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\Home\HomeController;

/* ==============================================
===============< Home Routes >==============
===============================================*/
Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
});
