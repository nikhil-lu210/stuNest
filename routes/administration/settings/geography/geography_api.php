<?php

/**
 * Geography lookup JSON (session-authenticated staff).
 * Included from geography.php inside the settings/geography route group.
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Geography\GeographyLookupController;

Route::get('/api/countries', [GeographyLookupController::class, 'countries'])->name('api.countries');
Route::get('/api/cities', [GeographyLookupController::class, 'cities'])->name('api.cities');
Route::get('/api/areas', [GeographyLookupController::class, 'areas'])->name('api.areas');
