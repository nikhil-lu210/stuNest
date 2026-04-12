<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Administration\Settings\Institute\InstituteController;
use App\Http\Controllers\Administration\Settings\Institute\InstituteRepresentativeController;

Route::controller(InstituteController::class)
    ->prefix('institute')
    ->name('institute.')
    ->group(function () {
        Route::get('/all', 'index')->name('index')->can('Institute Read');
        Route::get('/create', 'create')->name('create')->can('Institute Create');
        Route::post('/store', 'store')->name('store')->can('Institute Create');
        Route::get('/all/show/{institute}', 'show')->name('show')->can('Institute Read');
        Route::get('/edit/{institute}', 'edit')->name('edit')->can('Institute Update');
        Route::post('/update/{institute}', 'update')->name('update')->can('Institute Update');
    });

Route::controller(InstituteRepresentativeController::class)
    ->prefix('institute')
    ->name('institute.')
    ->group(function () {
        Route::get('/representatives/all', 'index')->name('representatives.index')->can('Institute Read');
        Route::get('/representatives/create', 'createEntry')->name('representatives.create.entry')->can('Institute Update');
        Route::get('/{institute}/representatives/create', 'create')->name('representatives.create')->can('Institute Update');
        Route::post('/{institute}/representatives/store', 'store')->name('representatives.store')->can('Institute Update');
        Route::get('/{institute}/representatives/destroy/{representative}', 'destroy')->name('representatives.destroy')->can('Institute Update');
    });
