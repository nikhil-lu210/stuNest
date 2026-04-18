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
        abort_unless($user && $user->hasStudentRole(), 403);

        $user->loadMissing(['institution']);

        return view('client.student.dashboard.index', [
            'user' => $user,
        ]);
    }

    public function settings(): View
    {
        $user = Auth::user();
        abort_unless($user && $user->hasStudentRole(), 403);

        $user->loadMissing(['institution']);

        return view('client.student.settings', [
            'user' => $user,
        ]);
    }

    public function notifications(): View
    {
        $user = Auth::user();
        abort_unless($user && $user->hasStudentRole(), 403);

        $user->loadMissing(['institution']);

        $notifications = $user->notifications()->latest()->paginate(20);

        return view('client.student.notifications.index', [
            'user' => $user,
            'notifications' => $notifications,
        ]);
    }
}
