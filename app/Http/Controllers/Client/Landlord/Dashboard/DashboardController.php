<?php

namespace App\Http\Controllers\Client\Landlord\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('client.landlord.dashboard.index');
    }
}
