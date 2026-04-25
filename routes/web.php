<?php

use App\Livewire\Auth\StudentRegister;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

include_once __DIR__.'/client/client.php';

Auth::routes(['register' => false]);

Route::middleware('guest')->group(function () {
    Route::get('/register', StudentRegister::class)
        ->name('register');

    Route::get('/register/student', fn () => redirect()->route('register'))
        ->name('register.student');

    Route::get('/register/landlord', fn () => redirect('/register?role=landlord'))
        ->name('register.landlord');

    Route::get('/register/institute', fn () => redirect('/register?role=institute'))
        ->name('register.institute');

    Route::get('/register/agent', fn () => redirect('/register?role=agent'))
        ->name('register.agent');
});

/*==============================================================
======================< Administration Routes >=================
==============================================================*/
Route::middleware(['auth', 'administration.access'])->group(function () {
    include_once __DIR__.'/administration/administration.php';
});

Route::middleware(['auth'])->group(function () {
    // Legacy URL: profile completion now lives on Account Settings.
    Route::get('/client/student/profile/edit', function () {
        $user = auth()->user();
        abort_unless($user instanceof User && $user->hasStudentRole(), 403);

        return redirect()->route('client.student.settings');
    })->name('student.profile.edit');

    include_once __DIR__.'/client/portal/portal.php';
});
