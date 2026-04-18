<?php

namespace App\Http\Controllers\Client\Explore;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Property\Property;
use App\Support\CountryMapCentroids;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExploreController extends Controller
{
    public function index(Request $request): View
    {
        $query = $this->buildExploreQuery($request);

        $properties = (clone $query)->latest()->paginate(12)->withQueryString();

        $q = trim((string) $request->input('q', ''));

        $countryIds = (clone $query)->reorder()
            ->select('properties.country_id')
            ->distinct()
            ->pluck('properties.country_id')
            ->unique()
            ->filter()
            ->values();

        $countryNames = $countryIds->isEmpty()
            ? []
            : Country::query()->active()->whereIn('id', $countryIds)->orderBy('name')->pluck('name')->all();

        $placesContextLine = $this->placesContextLine($q, $countryNames);
        $explorePageTitle = $this->explorePageTitle($request, $q, $countryNames);

        $savedIds = $this->savedPropertyIdsForUser($request);

        $guests = max(1, min(10, (int) $request->input('guests', 1)));

        $filters = $this->collectFilterParams($request, $guests);

        $propertyTypeState = $filters['property_type'] ?? '';
        if ($propertyTypeState === '' && $request->boolean('studio')) {
            $propertyTypeState = 'studio';
        }

        $filterState = $this->buildFilterState($request, $filters, $propertyTypeState);

        $mapMarkers = $properties->map(function (Property $property) {
            [$lat, $lng] = $this->markerLatLng($property);

            return [
                'id' => $property->id,
                'lat' => $lat,
                'lng' => $lng,
                'price' => (int) $property->rent_amount,
                'title' => $property->display_title,
                'url' => route('client.listing.show', ['slug' => $property->id]),
            ];
        })->values()->all();

        $countriesForFilter = Country::active()->orderBy('name')->get(['id', 'name']);

        $citiesForFilter = collect();
        if ($request->filled('country_id')) {
            $citiesForFilter = City::query()
                ->active()
                ->where('country_id', (int) $request->input('country_id'))
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        $areasForFilter = collect();
        if ($request->filled('city_id')) {
            $areasForFilter = Area::query()
                ->active()
                ->where('city_id', (int) $request->input('city_id'))
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return view('client.explore.index', compact(
            'properties',
            'filters',
            'filterState',
            'savedIds',
            'mapMarkers',
            'placesContextLine',
            'explorePageTitle',
            'countriesForFilter',
            'citiesForFilter',
            'areasForFilter'
        ));
    }

    public function cities(Country $country): JsonResponse
    {
        abort_unless($country->is_active, 404);

        return response()->json(
            City::query()
                ->active()
                ->where('country_id', $country->id)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    public function areas(City $city): JsonResponse
    {
        abort_unless($city->is_active, 404);

        return response()->json(
            Area::query()
                ->active()
                ->where('city_id', $city->id)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function collectFilterParams(Request $request, int $guests): array
    {
        return [
            'q' => trim((string) $request->input('q', '')),
            'move_in' => $request->input('move_in'),
            'guests' => $guests,
            'rent_period' => $request->input('rent_period'),
            'price_min' => $request->input('price_min'),
            'price_max' => $request->input('price_max'),
            'distance_max' => $request->input('distance_max'),
            'distance_transit_max' => $request->input('distance_transit_max'),
            'country_id' => $request->input('country_id'),
            'city_id' => $request->input('city_id'),
            'area_id' => $request->input('area_id'),
            'property_type' => $request->input('property_type'),
            'listing_category' => $request->input('listing_category'),
            'bedrooms_min' => $request->input('bedrooms_min'),
            'bedrooms_max' => $request->input('bedrooms_max'),
            'bathrooms' => $request->input('bathrooms'),
            'bed_type' => $request->input('bed_type'),
            'bathroom_type' => $request->input('bathroom_type'),
            'bills_included' => $request->input('bills_included'),
            'min_contract_length' => $request->input('min_contract_length'),
            'deposit_required' => $request->input('deposit_required'),
            'rent_for' => $request->input('rent_for'),
            'flatmate_vibe' => $request->input('flatmate_vibe'),
            'ensuite' => $request->boolean('ensuite'),
            'gym' => $request->boolean('gym'),
            'bills' => $request->boolean('bills'),
            'furnished' => $request->boolean('furnished'),
            'wifi' => $request->boolean('wifi'),
            'provides_agreement' => $request->boolean('provides_agreement'),
            'suitable_for' => (array) $request->input('suitable_for', []),
            'house_rules' => (array) $request->input('house_rules', []),
            'amenities' => (array) $request->input('amenities', []),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function buildFilterState(Request $request, array $filters, string $propertyTypeState): array
    {
        $rentPeriod = $filters['rent_period'] ?? '';
        $priceMin = $filters['price_min'] ?? '';
        $priceMax = $filters['price_max'] ?? '';
        if ($rentPeriod === '' && (($priceMin !== null && $priceMin !== '') || ($priceMax !== null && $priceMax !== ''))) {
            $rentPeriod = 'week';
        }

        $amenityExtras = array_values(array_filter(
            array_diff((array) $request->input('amenities', []), ['wifi', 'building_gym'])
        ));

        $billsIncluded = $filters['bills_included'] ?? '';
        $billsBool = $request->boolean('bills');
        if ($billsIncluded === '' && $billsBool) {
            $billsIncluded = 'all';
            $billsBool = false;
        }

        return [
            'rent_period' => $rentPeriod,
            'price_min' => $filters['price_min'] ?? '',
            'price_max' => $filters['price_max'] ?? '',
            'distance_max' => $filters['distance_max'] ?? '',
            'distance_transit_max' => $filters['distance_transit_max'] ?? '',
            'country_id' => $filters['country_id'] !== null && $filters['country_id'] !== '' ? (string) $filters['country_id'] : '',
            'city_id' => $filters['city_id'] !== null && $filters['city_id'] !== '' ? (string) $filters['city_id'] : '',
            'area_id' => $filters['area_id'] !== null && $filters['area_id'] !== '' ? (string) $filters['area_id'] : '',
            'property_type' => $propertyTypeState,
            'listing_category' => $filters['listing_category'] ?? '',
            'bedrooms_min' => $filters['bedrooms_min'] !== null && $filters['bedrooms_min'] !== '' ? (string) $filters['bedrooms_min'] : '',
            'bedrooms_max' => $filters['bedrooms_max'] !== null && $filters['bedrooms_max'] !== '' ? (string) $filters['bedrooms_max'] : '',
            'bathrooms' => $filters['bathrooms'] !== null && $filters['bathrooms'] !== '' ? (string) $filters['bathrooms'] : '',
            'bed_type' => $filters['bed_type'] ?? '',
            'bathroom_type' => $filters['bathroom_type'] ?? '',
            'bills_included' => $billsIncluded,
            'min_contract_length' => $filters['min_contract_length'] ?? '',
            'deposit_required' => $filters['deposit_required'] ?? '',
            'rent_for' => $filters['rent_for'] ?? '',
            'flatmate_vibe' => $filters['flatmate_vibe'] ?? '',
            'ensuite' => $request->boolean('ensuite'),
            'gym' => $request->boolean('gym'),
            'bills' => $billsBool,
            'furnished' => $request->boolean('furnished'),
            'wifi' => $request->boolean('wifi'),
            'provides_agreement' => $request->boolean('provides_agreement'),
            'suitable_for' => array_values(array_filter((array) $request->input('suitable_for', []))),
            'house_rules' => array_values(array_filter((array) $request->input('house_rules', []))),
            'amenities' => $amenityExtras,
        ];
    }

    /**
     * @param  array<int, string>  $countryNames
     */
    private function placesContextLine(string $q, array $countryNames): string
    {
        if ($q !== '') {
            return __('in :place', ['place' => $q]);
        }

        if (count($countryNames) === 1) {
            return __('in :place', ['place' => $countryNames[0]]);
        }

        if (count($countryNames) >= 2 && count($countryNames) <= 3) {
            return __('in :places', ['places' => implode(', ', $countryNames)]);
        }

        if (count($countryNames) > 3) {
            return __('across :count countries', ['count' => count($countryNames)]);
        }

        return '';
    }

    /**
     * @param  array<int, string>  $countryNames
     */
    private function explorePageTitle(Request $request, string $q, array $countryNames): string
    {
        if ($q !== '') {
            return __('Explore :place', ['place' => $q]).' | '.config('app.name');
        }

        if ($request->filled('country_id')) {
            $country = Country::active()->find((int) $request->input('country_id'));
            if ($country) {
                return __('Explore :place', ['place' => $country->name]).' | '.config('app.name');
            }
        }

        if (count($countryNames) === 1) {
            return __('Explore :place', ['place' => $countryNames[0]]).' | '.config('app.name');
        }

        return __('Explore').' | '.config('app.name');
    }

    /**
     * @return array{0: float, 1: float}
     */
    private function markerLatLng(Property $property): array
    {
        $lat = $property->latitude;
        $lng = $property->longitude;
        if ($lat !== null && $lng !== null) {
            return [(float) $lat, (float) $lng];
        }

        $iso = $property->country?->iso_code;
        [$baseLat, $baseLng] = CountryMapCentroids::forIso($iso);
        $seed = (int) $property->id;

        return [
            $baseLat + (($seed % 19) - 9) * 0.045,
            $baseLng + (($seed % 17) - 8) * 0.045,
        ];
    }

    private function buildExploreQuery(Request $request): Builder
    {
        $query = Property::query()
            ->published()
            ->with(['city', 'area', 'country', 'media']);

        $q = trim((string) $request->input('q', ''));
        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where(function ($sub) use ($like) {
                $sub->whereHas('city', function ($cq) use ($like) {
                    $cq->where('name', 'like', $like);
                })->orWhereHas('area', function ($aq) use ($like) {
                    $aq->where('name', 'like', $like);
                });
            });
        }

        $guests = (int) $request->input('guests', 1);
        $guests = max(1, min(10, $guests));
        $query->where('capacity', '>=', $guests);

        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $hasPrice = ($priceMin !== null && $priceMin !== '') || ($priceMax !== null && $priceMax !== '');

        if ($request->filled('rent_period')) {
            $query->where('rent_duration', $request->input('rent_period'));
        } elseif ($hasPrice) {
            $query->where('rent_duration', 'week');
        }

        if ($priceMin !== null && $priceMin !== '') {
            $query->where('rent_amount', '>=', (int) $priceMin);
        }

        if ($priceMax !== null && $priceMax !== '') {
            $query->where('rent_amount', '<=', (int) $priceMax);
        }

        $propertyType = $request->input('property_type');
        if ($request->boolean('studio') && ($propertyType === null || $propertyType === '')) {
            $propertyType = 'studio';
        }
        if ($propertyType !== null && $propertyType !== '') {
            $query->where('property_type', $propertyType);
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', (int) $request->input('country_id'));
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', (int) $request->input('city_id'));
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', (int) $request->input('area_id'));
        }

        if ($request->filled('listing_category')) {
            $query->where('listing_category', $request->input('listing_category'));
        }

        if ($request->filled('bedrooms_min')) {
            $query->where('bedrooms', '>=', (int) $request->input('bedrooms_min'));
        }

        if ($request->filled('bedrooms_max')) {
            $query->where('bedrooms', '<=', (int) $request->input('bedrooms_max'));
        }

        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', (int) $request->input('bathrooms'));
        }

        if ($request->filled('bed_type')) {
            $query->where('bed_type', $request->input('bed_type'));
        }

        if ($request->filled('bathroom_type')) {
            $query->where('bathroom_type', $request->input('bathroom_type'));
        } elseif ($request->boolean('ensuite')) {
            $query->where('bathroom_type', 'private_ensuite');
        }

        if ($request->filled('bills_included')) {
            $query->where('bills_included', $request->input('bills_included'));
        } elseif ($request->boolean('bills')) {
            $query->where('bills_included', 'all');
        }

        if ($request->filled('min_contract_length')) {
            $query->where('min_contract_length', $request->input('min_contract_length'));
        }

        if ($request->filled('deposit_required')) {
            $query->where('deposit_required', $request->input('deposit_required'));
        }

        if ($request->filled('rent_for')) {
            $query->where('rent_for', $request->input('rent_for'));
        }

        if ($request->filled('flatmate_vibe')) {
            $query->where('flatmate_vibe', $request->input('flatmate_vibe'));
        }

        if ($request->boolean('provides_agreement')) {
            $query->where('provides_agreement', true);
        }

        if ($request->boolean('furnished')) {
            $query->where('is_furnished', true);
        }

        foreach ((array) $request->input('suitable_for', []) as $value) {
            if ($value !== null && $value !== '') {
                $query->whereJsonContains('suitable_for', $value);
            }
        }

        foreach ((array) $request->input('house_rules', []) as $value) {
            if ($value !== null && $value !== '') {
                $query->whereJsonContains('house_rules', $value);
            }
        }

        $amenityKeys = array_unique(array_filter(array_merge(
            (array) $request->input('amenities', []),
            $request->boolean('wifi') ? ['wifi'] : [],
            $request->boolean('gym') ? ['building_gym'] : []
        )));
        foreach ($amenityKeys as $a) {
            $query->whereJsonContains('amenities', $a);
        }

        $distanceMax = $request->input('distance_max');
        if ($distanceMax !== null && $distanceMax !== '') {
            $query->whereNotNull('distance_university_km')
                ->where('distance_university_km', '<=', (float) $distanceMax);
        }

        $distanceTransitMax = $request->input('distance_transit_max');
        if ($distanceTransitMax !== null && $distanceTransitMax !== '') {
            $query->whereNotNull('distance_transit_km')
                ->where('distance_transit_km', '<=', (float) $distanceTransitMax);
        }

        return $query;
    }

    public function toggleFavorite(Request $request, Property $property): JsonResponse
    {
        abort_unless($property->status === Property::STATUS_PUBLISHED, 404);

        if (! $request->user()) {
            return response()->json([
                'message' => __('Sign in to save properties.'),
                'login_url' => route('login').'?redirect='.urlencode($request->fullUrl()),
            ], 401);
        }

        if (! Schema::hasTable('saved_properties')) {
            return response()->json([
                'saved' => $this->toggleFavoriteInSession($property),
            ]);
        }

        $user = $request->user();
        $changes = $user->savedProperties()->toggle($property->id);
        $saved = count($changes['attached'] ?? []) > 0;

        return response()->json(['saved' => $saved]);
    }

    /**
     * @return array<int, int>
     */
    private function savedPropertyIdsForUser(Request $request): array
    {
        if (! $request->user()) {
            return [];
        }

        if (Schema::hasTable('saved_properties')) {
            return DB::table('saved_properties')
                ->where('user_id', $request->user()->id)
                ->pluck('property_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        $ids = session()->get('explore_saved_property_ids', []);
        if (! is_array($ids)) {
            return [];
        }

        return array_values(array_unique(array_map('intval', $ids)));
    }

    private function toggleFavoriteInSession(Property $property): bool
    {
        $ids = session()->get('explore_saved_property_ids', []);
        if (! is_array($ids)) {
            $ids = [];
        }
        $ids = array_values(array_unique(array_map('intval', $ids)));
        $key = array_search($property->id, $ids, true);
        if ($key !== false) {
            unset($ids[$key]);
            session()->put('explore_saved_property_ids', array_values($ids));

            return false;
        }
        $ids[] = $property->id;
        session()->put('explore_saved_property_ids', $ids);

        return true;
    }
}
