<?php

namespace App\Http\Controllers\Client\Landlord\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function applications(): View
    {
        return view('client.landlord.dashboard.index', [
            'pageTitle' => __('Applications'),
        ]);
    }

    public function messages(): View
    {
        return view('client.landlord.dashboard.index', [
            'pageTitle' => __('Messages'),
        ]);
    }

    public function settings(): View
    {
        return view('client.landlord.dashboard.index', [
            'pageTitle' => __('Settings'),
        ]);
    }

    public function notifications(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Landlord'), 403);

        $notifications = $user->notifications()->latest()->paginate(20);

        return view('client.landlord.notifications.index', [
            'notifications' => $notifications,
        ]);
    }
}
