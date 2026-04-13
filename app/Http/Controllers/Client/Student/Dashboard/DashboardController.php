<?php

namespace App\Http\Controllers\Client\Student\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user && $user->hasRole('Student'), 403);

        $user->loadMissing(['institution']);

        return view('client.student.dashboard.index', [
            'user' => $user,
        ]);
    }
}
