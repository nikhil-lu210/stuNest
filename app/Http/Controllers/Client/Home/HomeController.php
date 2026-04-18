<?php

namespace App\Http\Controllers\Client\Home;

use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProperties = Property::query()
            ->published()
            ->with(['city', 'area', 'country', 'media'])
            ->latest()
            ->limit(4)
            ->get();

        return view('client.home.index', [
            'featuredProperties' => $featuredProperties,
        ]);
    }
}
