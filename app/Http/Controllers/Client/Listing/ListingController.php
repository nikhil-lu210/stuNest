<?php

namespace App\Http\Controllers\Client\Listing;

use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use App\Support\ListingPublicId;
use App\Support\SavedPropertyIds;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    /**
     * Public listing detail. URLs use an opaque ref (ListingPublicId) instead of raw numeric ids.
     * Legacy /listings/123 redirects to the canonical ref URL.
     */
    public function show(Request $request, string $slug): View|RedirectResponse
    {
        if (ctype_digit($slug)) {
            return redirect()->route('client.listing.show', [
                'slug' => ListingPublicId::encode((int) $slug),
            ], 301);
        }

        $id = ListingPublicId::decode($slug);
        if ($id !== null) {
            $property = Property::query()
                ->published()
                ->with(['city', 'area', 'country', 'media', 'creator'])
                ->find($id);

            if ($property) {
                $savedIds = SavedPropertyIds::forRequest($request);
                $isSaved = in_array($property->id, $savedIds, true);

                return view('client.listing.show', [
                    'listing' => $this->listingSnapshot($property),
                    'property' => $property,
                    'isDemo' => false,
                    'isSaved' => $isSaved,
                    'galleryUrls' => $this->galleryUrls($property),
                    'amenityRows' => $this->amenityRows($property),
                    'aboutText' => $this->aboutText($property),
                ]);
            }

            abort(404);
        }

        if ($slug === 'example-studio') {
            return $this->demoListing($slug);
        }

        abort(404);
    }

    /**
     * @return array<string, mixed>
     */
    private function listingSnapshot(Property $property): array
    {
        return [
            'title' => $property->display_title,
            'rating' => $property->public_star_rating,
            'reviews' => 0,
            'location' => $property->full_address_line,
            'price_week' => $property->weekly_rent_display,
            'rent_amount' => (int) $property->rent_amount,
            'rent_duration' => $property->rent_duration,
            'slug' => ListingPublicId::encode($property->id),
            'host_name' => $property->creator?->name ?? __('Host'),
            'host_avatar_url' => $property->creator?->getFirstMediaUrl('avatar', 'thumb') ?: null,
            'capacity' => (int) $property->capacity,
            'bedrooms' => (int) $property->bedrooms,
            'bathrooms' => (int) $property->bathrooms,
            'bathroom_type' => $property->bathroom_type,
            'listing_category' => $property->listing_category,
            'property_type' => $property->property_type,
            'is_furnished' => (bool) $property->is_furnished,
            'bills_included' => $property->bills_included,
            'min_contract_length' => $property->min_contract_length,
            'min_contract_label' => $this->contractLengthLabel($property->min_contract_length),
            'deposit_required' => $property->deposit_required,
            'deposit_label' => $this->depositLabel($property->deposit_required),
            'provides_agreement' => (bool) $property->provides_agreement,
            'rent_for' => $property->rent_for,
            'distance_campus' => $property->marketing_uni_line,
            'distance_transit' => $this->distanceTransitLine($property),
        ];
    }

    private function distanceTransitLine(Property $property): ?string
    {
        if ($property->distance_transit_km === null || $property->distance_transit_km === '') {
            return null;
        }

        return number_format((float) $property->distance_transit_km, 1).' '.__('km to transit');
    }

    /**
     * @return array<int, string>
     */
    private function galleryUrls(Property $property): array
    {
        $urls = [];
        foreach ($property->getMedia('property_gallery') as $media) {
            $urls[] = $media->getUrl('optimized') ?: $media->getUrl();
        }

        return $urls;
    }

    /**
     * @return array<int, array{icon: string, label: string}>
     */
    private function amenityRows(Property $property): array
    {
        $map = [
            'wifi' => ['icon' => 'wifi', 'label' => __('Wi‑Fi')],
            'washing_machine' => ['icon' => 'washing-machine', 'label' => __('Washing machine')],
            'tumble_dryer' => ['icon' => 'wind', 'label' => __('Tumble dryer')],
            'dishwasher' => ['icon' => 'utensils', 'label' => __('Dishwasher')],
            'balcony_garden' => ['icon' => 'sun', 'label' => __('Balcony / garden')],
            'desk_in_room' => ['icon' => 'laptop', 'label' => __('Desk in room')],
            'building_gym' => ['icon' => 'dumbbell', 'label' => __('Building gym')],
            'bike_storage' => ['icon' => 'bike', 'label' => __('Bike storage')],
        ];

        $keys = is_array($property->amenities) ? $property->amenities : [];
        $rows = [];
        foreach ($keys as $key) {
            if (isset($map[$key])) {
                $rows[] = $map[$key];
            }
        }

        return $rows;
    }

    private function aboutText(Property $property): string
    {
        $lines = [];

        $lines[] = __('This :category is listed for :audience.', [
            'category' => Str::headline(str_replace('_', ' ', (string) $property->listing_category)),
            'audience' => $property->rent_for
                ? Str::headline(str_replace('_', ' ', (string) $property->rent_for))
                : __('Students'),
        ]);

        $lines[] = __(':beds bedroom(s), :baths bathroom(s), :furn.', [
            'beds' => $property->bedrooms,
            'baths' => $property->bathrooms,
            'furn' => $property->is_furnished ? __('Furnished') : __('Unfurnished'),
        ]);

        if ($property->bathroom_type) {
            $lines[] = __('Bathroom: :type.', ['type' => Str::headline(str_replace('_', ' ', (string) $property->bathroom_type))]);
        }

        $lines[] = __('Rent is shown per :period (see sidebar). Bills: :bills.', [
            'period' => match ($property->rent_duration) {
                'day' => __('day'),
                'month' => __('month'),
                default => __('week'),
            },
            'bills' => match ($property->bills_included) {
                'all' => __('All included'),
                'some' => __('Some included'),
                'none' => __('Not included'),
                default => (string) $property->bills_included,
            },
        ]);

        $sf = is_array($property->suitable_for) ? $property->suitable_for : [];
        if ($sf !== []) {
            $labels = array_map(fn ($v) => Str::headline(str_replace('_', ' ', (string) $v)), $sf);
            $lines[] = __('Suitable for: :list.', ['list' => implode(', ', $labels)]);
        }

        $lines[] = __('Minimum contract: :len.', ['len' => $this->contractLengthLabel($property->min_contract_length)]);

        if ($property->provides_agreement) {
            $lines[] = __('Written tenancy agreement available.');
        }

        return implode("\n\n", array_filter($lines));
    }

    private function contractLengthLabel(string $value): string
    {
        return match ($value) {
            '1_month' => __('1 month'),
            '3_months' => __('3 months'),
            '6_months' => __('6 months'),
            '1_year' => __('1 year'),
            'flexible' => __('Flexible'),
            default => Str::headline(str_replace('_', ' ', $value)),
        };
    }

    private function depositLabel(string $value): string
    {
        return match ($value) {
            'none' => __('No deposit'),
            '1_month' => __('1 month rent'),
            '5_weeks' => __('5 weeks'),
            default => Str::headline(str_replace('_', ' ', $value)),
        };
    }

    private function demoListing(string $slug): View
    {
        $listing = [
            'slug' => $slug,
            'title' => Str::headline(str_replace('-', ' ', $slug)),
            'price_week' => '285',
            'rent_amount' => 285,
            'rating' => '4.9',
            'reviews' => 124,
            'location' => 'Islington, London, UK',
            'rent_duration' => 'week',
            'host_name' => __('Demo host'),
            'host_avatar_url' => null,
            'capacity' => 1,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'bathroom_type' => 'private_ensuite',
            'listing_category' => 'shared_room',
            'property_type' => 'apartment',
            'is_furnished' => true,
            'bills_included' => 'all',
            'min_contract_length' => '1_year',
            'min_contract_label' => __('1 year'),
            'deposit_required' => '1_month',
            'deposit_label' => __('1 month rent'),
            'provides_agreement' => true,
            'rent_for' => 'students',
            'distance_campus' => __('Near campus'),
            'distance_transit' => null,
        ];

        if ($slug === 'example-studio') {
            $listing['title'] = 'The Oxford Studio';
        }

        return view('client.listing.show', [
            'listing' => $listing,
            'property' => null,
            'isDemo' => true,
            'isSaved' => false,
            'galleryUrls' => [],
            'amenityRows' => [
                ['icon' => 'wifi', 'label' => __('Wi‑Fi')],
                ['icon' => 'flame', 'label' => __('Heating')],
                ['icon' => 'washing-machine', 'label' => __('Laundry facilities')],
                ['icon' => 'dumbbell', 'label' => __('Building gym')],
            ],
            'aboutText' => __('The Oxford Studio is a beautifully designed, self-contained living space created specifically for students who want a premium experience.'),
        ]);
    }
}
