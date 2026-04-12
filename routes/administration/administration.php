<?php

use Illuminate\Support\Facades\Route;

/* ==============================================
============< Administration Routes >============
===============================================*/
Route::prefix('administration')
        ->name('administration.')
        ->group(function () {
            // notification
            include_once 'notification/notification.php';

            // Dashboard
            include_once 'dashboard/dashboard.php';

            // User management (flattened navigation — placeholder pages)
            include_once 'user_management/user_management.php';
            
            // Profile
            include_once 'profile/profile.php';

            // settings
            include_once 'settings/settings.php';

            // Property listings
            include_once 'property/property.php';

            // Tenancies & applications
            include_once 'application/application.php';
        });