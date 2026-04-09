<?php

use Illuminate\Support\Facades\Route;

/* ==============================================
===============< Legal (static views) >==============
===============================================*/
Route::view('/terms', 'client.legal.terms')->name('terms');
Route::view('/privacy', 'client.legal.privacy')->name('privacy');
