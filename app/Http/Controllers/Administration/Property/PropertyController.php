<?php

namespace App\Http\Controllers\Administration\Property;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\Property\PropertyUpdateRequest;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Property\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Property::class);

        return $this->listingView($request, null, __('All properties'), null);
    }

    public function pendingReview(Request $request): View
    {
        $this->authorize('viewAny', Property::class);

        return $this->listingView($request, Property::STATUS_PENDING, __('Pending review'), null);
    }

    public function live(Request $request): View
    {
        $this->authorize('viewAny', Property::class);

        return $this->listingView($request, Property::STATUS_PUBLISHED, __('Live properties'), null);
    }

    public function rented(Request $request): View
    {
        $this->authorize('viewAny', Property::class);

        return $this->listingView($request, Property::STATUS_LET_AGREED, __('Rented'), null);
    }

    public function draftsArchived(Request $request): View
    {
        $this->authorize('viewAny', Property::class);

        return $this->listingView(
            $request,
            null,
            __('Drafts & archived'),
            [Property::STATUS_DRAFT, Property::STATUS_ARCHIVED]
        );
    }

    public function show(Property $property): View
    {
        $this->authorize('view', $property);

        $property->load(['creator', 'country', 'city', 'area']);

        return view('administration.property.show', compact('property'));
    }

    public function citiesJson(Country $country): JsonResponse
    {
        return response()->json(
            City::query()->active()->where('country_id', $country->id)->orderBy('name')->get(['id', 'name'])
        );
    }

    public function areasJson(City $city): JsonResponse
    {
        return response()->json(
            Area::query()->active()->where('city_id', $city->id)->orderBy('name')->get(['id', 'name'])
        );
    }

    public function edit(Property $property): View
    {
        $this->authorize('update', $property);

        $property->load('creator');

        $canManageStatus = Auth::user()?->hasAnyRole(['Developer', 'Super Admin', 'Admin']) ?? false;

        $countries = Country::query()->active()->orderBy('name')->get();
        $countryId = old('country_id', $property->country_id);
        $cityId = old('city_id', $property->city_id);
        $cities = $countryId
            ? City::query()->active()->where('country_id', $countryId)->orderBy('name')->get()
            : collect();
        $areas = $cityId
            ? Area::query()->active()->where('city_id', $cityId)->orderBy('name')->get()
            : collect();

        return view('administration.property.edit', compact('property', 'canManageStatus', 'countries', 'cities', 'areas'));
    }

    public function update(PropertyUpdateRequest $request, Property $property): RedirectResponse
    {
        $data = $request->validated();

        if (($data['bills_included'] ?? '') !== 'some') {
            $data['included_bills'] = [];
        }

        if (($data['listing_category'] ?? '') !== 'shared_room') {
            $data['flatmate_vibe'] = null;
            $data['bed_type'] = null;
        }

        if (! Auth::user()?->hasAnyRole(['Developer', 'Super Admin', 'Admin'])) {
            unset($data['status']);
        }

        foreach (['latitude', 'longitude'] as $coord) {
            if (array_key_exists($coord, $data) && ($data[$coord] === '' || $data[$coord] === null)) {
                $data[$coord] = null;
            }
        }

        $property->update($data);

        toast(__('Property has been updated.'), 'success');

        return redirect()->route('administration.properties.show', $property);
    }

    /**
     * @param  array<int, string>|null  $statusIn
     */
    private function listingView(Request $request, ?string $status, string $pageTitle, ?array $statusIn): View
    {
        $query = Property::query()->with(['creator', 'country', 'city', 'area'])->latest();

        if (! Auth::user()?->hasAnyRole(['Developer', 'Super Admin', 'Admin'])) {
            $query->where('user_id', Auth::id());
        }

        if ($statusIn !== null) {
            $query->whereIn('status', $statusIn);
        } elseif ($status !== null) {
            $query->where('status', $status);
        }

        $properties = $query->paginate(15)->withQueryString();

        return view('administration.property.index', compact('properties', 'pageTitle'));
    }
}
