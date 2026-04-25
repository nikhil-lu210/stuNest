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
});

/*==============================================================
======================< Administration Routes >=================
==============================================================*/
Route::middleware(['auth', 'administration.access'])->group(function () {
    include_once __DIR__.'/administration/administration.php';
});

Route::middleware(['auth'])->group(function () {
    Route::get('/client/student/profile/edit', function () {
        $user = auth()->user();
        abort_unless($user instanceof User && $user->hasStudentRole(), 403);

        return view('client.student.profile-edit', [
            'user' => $user,
        ]);
    })->name('student.profile.edit');

    include_once __DIR__.'/client/portal/portal.php';
});
