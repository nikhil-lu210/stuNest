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

            // institute
            include_once 'institute/institute.php';
            
            // rolepermission
            include_once 'rolepermission/rolepermission.php';
        });