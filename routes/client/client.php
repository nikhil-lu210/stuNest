<?php

use Illuminate\Support\Facades\Route;

/* ==============================================
============< Client Routes (public marketing) >============
===============================================*/
Route::name('client.')->group(function () {
    include_once __DIR__.'/marketing/marketing.php';
});
