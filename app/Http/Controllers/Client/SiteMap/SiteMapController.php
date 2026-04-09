<?php

namespace App\Http\Controllers\Client\SiteMap;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class SiteMapController extends Controller
{
    public function index(): View
    {
        return view('client.site_map.index');
    }
}
