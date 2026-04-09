<?php

namespace App\Http\Controllers\Client\Listing;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    /**
     * Demo listing detail — replace with Eloquent model when listings exist.
     */
    public function show(string $slug): View
    {
        $listing = [
            'slug' => $slug,
            'title' => Str::headline(str_replace('-', ' ', $slug)),
            'price_week' => '285',
            'rating' => '4.9',
            'reviews' => 124,
            'location' => 'Islington, London, UK',
        ];

        if ($slug === 'example-studio') {
            $listing['title'] = 'The Oxford Studio';
        }

        return view('client.listing.show', compact('listing'));
    }
}
