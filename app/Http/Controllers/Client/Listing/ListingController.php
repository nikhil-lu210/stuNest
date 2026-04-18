<?php

namespace App\Http\Controllers\Client\Listing;

use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    /**
     * Public listing detail. Numeric {slug} loads a published property from the database;
     * other slugs keep the legacy demo behaviour for old links.
     */
    public function show(string $slug): View
    {
        if (ctype_digit($slug)) {
            $property = Property::query()
                ->published()
                ->with(['city', 'area', 'country', 'media'])
                ->findOrFail((int) $slug);

            $listing = [
                'title' => $property->display_title,
                'rating' => $property->public_star_rating,
                'reviews' => 0,
                'location' => $property->full_address_line,
                'price_week' => $property->weekly_rent_display,
                'slug' => (string) $property->id,
                'property' => $property,
            ];

            return view('client.listing.show', compact('listing'));
        }

        $listing = [
            'slug' => $slug,
            'title' => Str::headline(str_replace('-', ' ', $slug)),
            'price_week' => '285',
            'rating' => '4.9',
            'reviews' => 124,
            'location' => 'Islington, London, UK',
            'property' => null,
        ];

        if ($slug === 'example-studio') {
            $listing['title'] = 'The Oxford Studio';
        }

        return view('client.listing.show', compact('listing'));
    }
}
