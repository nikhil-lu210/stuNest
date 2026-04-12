<?php

use Illuminate\Support\Facades\Route;

/* ==============================================
============< Settings Routes >============
===============================================*/
Route::prefix('settings')
        ->name('settings.')
        ->group(function () {
            // user
            include_once 'user/user.php';

            // geography (countries / cities / areas)
            include_once 'geography/geography.php';
            
            // rolepermission
            include_once 'rolepermission/rolepermission.php';
        });