<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Administration\UserManagement\AgentController;
use App\Http\Controllers\Administration\UserManagement\LandlordController;
use App\Http\Controllers\Administration\UserManagement\StudentController;
use App\Http\Controllers\Administration\UserManagement\UserDirectoryController;

Route::controller(UserDirectoryController::class)->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', 'allUsers')->name('index')->can('User Read');
        Route::view('/create', 'administration.user.create-user-page')->name('create')->can('User Create');
    });

    Route::prefix('landlords')->name('landlords.')->group(function () {
        Route::get('/', 'landlordsIndex')->name('index')->can('User Read');
        Route::get('/pending', 'landlordsPending')->name('pending')->can('User Read');
        Route::get('/rejected', 'landlordsRejected')->name('rejected')->can('User Read');
        Route::get('/create', [LandlordController::class, 'create'])->name('create')->can('User Create');
        Route::post('/create', [LandlordController::class, 'store'])->name('store')->can('User Create');
    });

    Route::prefix('agents')->name('agents.')->group(function () {
        Route::get('/', 'agentsIndex')->name('index')->can('User Read');
        Route::get('/pending', 'agentsPending')->name('pending')->can('User Read');
        Route::get('/rejected', 'agentsRejected')->name('rejected')->can('User Read');
        Route::get('/create', [AgentController::class, 'create'])->name('create')->can('User Create');
        Route::post('/create', [AgentController::class, 'store'])->name('store')->can('User Create');
    });

    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', 'studentsIndex')->name('index')->can('User Read');
        Route::get('/unverified', 'studentsUnverified')->name('unverified')->can('User Read');
        Route::get('/branches', [StudentController::class, 'getBranches'])->name('branches')->can('User Create');
        Route::get('/create', [StudentController::class, 'create'])->name('create')->can('User Create');
        Route::post('/create', [StudentController::class, 'store'])->name('store')->can('User Create');
    });
});
