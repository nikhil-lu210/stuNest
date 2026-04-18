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
                    'aboutPlace' => $this->aboutPlaceFromProperty($property),
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

    /**
     * @return array<int, array{title: string, rows: array<int, array{icon: string, label: string, value: string}>}>
     */
    private function aboutPlaceFromProperty(Property $property): array
    {
        $spaceRows = [];

        $bedrooms = (int) $property->bedrooms;
        $bathrooms = (int) $property->bathrooms;
        $bedLbl = $bedrooms === 1 ? __('Bedroom') : __('Bedrooms');
        $bathPhrase = match ((string) $property->bathroom_type) {
            'shared' => $bathrooms.' '.($bathrooms === 1 ? __('shared bathroom') : __('shared bathrooms')),
            'private_ensuite' => $bathrooms.' '.($bathrooms === 1 ? __('private / ensuite bathroom') : __('private / ensuite bathrooms')),
            default => $bathrooms.' '.($bathrooms === 1 ? __('Bathroom') : __('Bathrooms')),
        };
        $spaceRows[] = [
            'icon' => 'layout-template',
            'label' => __('Bedrooms & bathrooms'),
            'value' => $bedrooms.' '.$bedLbl.' • '.$bathPhrase,
        ];

        $spaceRows[] = [
            'icon' => 'sofa',
            'label' => __('Furnishing'),
            'value' => $property->is_furnished ? __('Fully furnished') : __('Unfurnished'),
        ];

        if ($property->bed_type) {
            $spaceRows[] = [
                'icon' => 'bed-double',
                'label' => __('Bed type'),
                'value' => match ((string) $property->bed_type) {
                    'single' => __('Single bed'),
                    'shared_double' => __('Shared double'),
                    default => Str::headline(str_replace('_', ' ', (string) $property->bed_type)),
                },
            ];
        }

        $tenantRows = [];

        $sf = is_array($property->suitable_for) ? $property->suitable_for : [];
        if ($sf !== []) {
            $labels = array_map(fn ($v) => Str::headline(str_replace('_', ' ', (string) $v)), $sf);
            $tenantRows[] = [
                'icon' => 'graduation-cap',
                'label' => __('Suitable for'),
                'value' => implode(' & ', $labels),
            ];
        }

        if ($property->rent_for) {
            $tenantRows[] = [
                'icon' => 'users',
                'label' => __('Household preference'),
                'value' => $this->rentForLabel((string) $property->rent_for),
            ];
        }

        if ($property->listing_category === 'shared_room' && $property->flatmate_vibe) {
            $tenantRows[] = [
                'icon' => 'heart',
                'label' => __('Flatmate vibe'),
                'value' => match ((string) $property->flatmate_vibe) {
                    'all_male' => __('All male'),
                    'all_female' => __('All female'),
                    'mixed' => __('Mixed'),
                    default => Str::headline(str_replace('_', ' ', (string) $property->flatmate_vibe)),
                },
            ];
        }

        $rules = is_array($property->house_rules) ? $property->house_rules : [];
        $ruleLabels = [];
        foreach ($rules as $rule) {
            $ruleLabels[] = $this->houseRuleLabel((string) $rule);
        }
        if ($ruleLabels !== []) {
            $tenantRows[] = [
                'icon' => 'clipboard-list',
                'label' => __('House rules'),
                'value' => implode(' • ', $ruleLabels),
            ];
        }

        $financialRows = [];

        $financialRows[] = [
            'icon' => 'plug',
            'label' => __('Bills'),
            'value' => match ((string) $property->bills_included) {
                'all' => __('All bills included'),
                'some' => __('Some bills included'),
                'none' => __('Bills not included'),
                default => Str::headline(str_replace('_', ' ', (string) $property->bills_included)),
            },
        ];

        $financialRows[] = [
            'icon' => 'calendar',
            'label' => __('Minimum stay'),
            'value' => $this->contractLengthLabel($property->min_contract_length).' '.__('minimum'),
        ];

        $financialRows[] = [
            'icon' => 'file-text',
            'label' => __('Tenancy agreement'),
            'value' => $property->provides_agreement
                ? __('Written tenancy agreement provided')
                : __('Contact landlord for agreement details'),
        ];

        $sections = [
            [
                'title' => __('The Space'),
                'rows' => $spaceRows,
            ],
            [
                'title' => __('Tenant preferences'),
                'rows' => $tenantRows,
            ],
            [
                'title' => __('Financials & contract'),
                'rows' => $financialRows,
            ],
        ];

        return array_values(array_filter($sections, fn (array $s) => count($s['rows']) > 0));
    }

    private function rentForLabel(string $rentFor): string
    {
        return match ($rentFor) {
            'only_boys' => __('Only boys'),
            'only_girls' => __('Only girls'),
            'couples' => __('Couples welcome'),
            'anyone' => __('Anyone welcome'),
            default => Str::headline(str_replace('_', ' ', $rentFor)),
        };
    }

    private function houseRuleLabel(string $rule): string
    {
        return match ($rule) {
            'pet_friendly' => __('Pet friendly'),
            'smoking_allowed' => __('Smoking allowed'),
            'quiet_house' => __('Quiet house'),
            default => Str::headline(str_replace('_', ' ', $rule)),
        };
    }

    /**
     * @param  array<string, mixed>  $listing
     * @return array<int, array{title: string, rows: array<int, array{icon: string, label: string, value: string}>}>
     */
    private function aboutPlaceFromDemoListing(array $listing): array
    {
        $property = new Property;
        $property->forceFill([
            'bedrooms' => (int) ($listing['bedrooms'] ?? 1),
            'bathrooms' => (int) ($listing['bathrooms'] ?? 1),
            'bathroom_type' => (string) ($listing['bathroom_type'] ?? 'shared'),
            'is_furnished' => (bool) ($listing['is_furnished'] ?? false),
            'bed_type' => $listing['bed_type'] ?? null,
            'listing_category' => (string) ($listing['listing_category'] ?? 'shared_room'),
            'suitable_for' => $listing['suitable_for'] ?? [],
            'rent_for' => (string) ($listing['rent_for'] ?? 'anyone'),
            'flatmate_vibe' => $listing['flatmate_vibe'] ?? null,
            'house_rules' => $listing['house_rules'] ?? [],
            'bills_included' => (string) ($listing['bills_included'] ?? 'all'),
            'min_contract_length' => (string) ($listing['min_contract_length'] ?? 'flexible'),
            'provides_agreement' => (bool) ($listing['provides_agreement'] ?? false),
        ]);

        return $this->aboutPlaceFromProperty($property);
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
            'rent_for' => 'only_girls',
            'bed_type' => 'single',
            'flatmate_vibe' => 'mixed',
            'suitable_for' => ['undergraduates', 'postgraduates'],
            'house_rules' => ['quiet_house'],
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
            'aboutPlace' => $this->aboutPlaceFromDemoListing($listing),
        ]);
    }
}
