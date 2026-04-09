<?php

namespace App\Http\Controllers\Client\Explore;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ExploreController extends Controller
{
    public function index(): View
    {
        return view('client.explore.index');
    }
}
