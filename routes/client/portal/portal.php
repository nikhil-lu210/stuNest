<?php

use Illuminate\Support\Facades\Route;

/* ==============================================
============< Client portal (authenticated) >============
===============================================*/
Route::prefix('client')
        ->name('client.')
        ->group(function () {
            include_once __DIR__.'/student/student.php';
            include_once __DIR__.'/landlord/landlord.php';
            include_once __DIR__.'/institute/institute.php';
            include_once __DIR__.'/agent/agent.php';
        });
