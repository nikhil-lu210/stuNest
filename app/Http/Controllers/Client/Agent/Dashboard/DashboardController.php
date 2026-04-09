<?php

namespace App\Http\Controllers\Client\Agent\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Agent portal — full feature set planned for v2.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        return view('client.agent.dashboard.index');
    }
}
