<?php

namespace App\Http\Controllers\Client\Landlord\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function properties(): View
    {
        return view('client.landlord.dashboard.index', [
            'pageTitle' => __('My Properties'),
        ]);
    }

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
}
