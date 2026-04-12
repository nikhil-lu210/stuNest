<?php

use App\Http\Controllers\Administration\Application\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::prefix('tenancies')->name('tenancies.')->group(function () {
    Route::get('/applications', [ApplicationController::class, 'index'])->name('index');
});
