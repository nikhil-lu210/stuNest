<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Administration\Settings\Geography\GeographyController;
use App\Http\Controllers\Administration\Settings\Geography\GeographyApiController;

Route::prefix('geography')
    ->name('geography.')
    ->group(function () {
        Route::get('/api/countries', [GeographyApiController::class, 'countries'])->name('api.countries');
        Route::get('/api/cities', [GeographyApiController::class, 'cities'])->name('api.cities');
        Route::get('/api/areas', [GeographyApiController::class, 'areas'])->name('api.areas');

        Route::get('/', [GeographyController::class, 'index'])->name('index')->can('Geography Read');
        Route::get('/import/sample', [GeographyController::class, 'downloadSample'])->name('import.sample')->can('Geography Read');
        Route::post('/import', [GeographyController::class, 'import'])->name('import')->can('Geography Create');

        Route::get('/countries/{country}', [GeographyController::class, 'countryCities'])->name('countries.show')->can('Geography Read');
        Route::post('/countries/{country}/toggle', [GeographyController::class, 'toggleCountry'])->name('countries.toggle')->can('Geography Update');

        Route::get('/cities/{city}', [GeographyController::class, 'cityAreas'])->name('cities.show')->can('Geography Read');
        Route::post('/cities/{city}/toggle', [GeographyController::class, 'toggleCity'])->name('cities.toggle')->can('Geography Update');

        Route::post('/areas/{area}/toggle', [GeographyController::class, 'toggleArea'])->name('areas.toggle')->can('Geography Update');
    });
