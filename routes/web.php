<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

/*==============================================================
======================< Administration Routes >=================
==============================================================*/
Route::middleware(['auth'])->group(function () {
    include_once 'administration/administration.php';
    include_once 'landlord/landlord.php';
    include_once 'student/student.php';
});
