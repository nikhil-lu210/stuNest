<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

include_once __DIR__.'/client/client.php';

Auth::routes();

/*==============================================================
======================< Administration Routes >=================
==============================================================*/
Route::middleware(['auth'])->group(function () {
    include_once __DIR__.'/administration/administration.php';
    include_once __DIR__.'/client/portal/portal.php';
});
