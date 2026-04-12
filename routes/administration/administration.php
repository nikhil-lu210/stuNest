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

            // Legacy institute URLs (bookmarks): /administration/settings/institute/... → /administration/institute/...
            Route::get('settings/institute', function () {
                return redirect()->route('administration.institute.index', [], 301);
            });
            Route::get('settings/institute/{path}', function (string $path) {
                return redirect('/administration/institute/'.$path, 301);
            })->where('path', '.*');

            // Institute (URL: /administration/institute/... — not under /settings)
            include_once 'institute/institute.php';

            // settings
            include_once 'settings/settings.php';

            // Property listings
            include_once 'property/property.php';

            // Tenancies & applications
            include_once 'application/application.php';
        });