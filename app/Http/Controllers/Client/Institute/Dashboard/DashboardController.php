<?php

namespace App\Http\Controllers\Client\Institute\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Institution portal — full feature set planned for v2.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        return view('client.institute.dashboard.index');
    }
}
