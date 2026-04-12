<?php

namespace App\Http\Controllers\Administration\Settings\Geography;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\GeographyImportService;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GeographyController extends Controller
{
    public function index()
    {
        $countries = Country::query()->withCount(['cities'])->orderBy('name')->get();

        return view('administration.settings.geography.index', compact('countries'));
    }

    public function countryCities(Country $country)
    {
        $cities = $country->cities()->withCount('areas')->orderBy('sort_order')->orderBy('name')->get();

        return view('administration.settings.geography.country-cities', compact('country', 'cities'));
    }

    public function cityAreas(City $city)
    {
        $city->load('country');
        $areas = $city->areas()->orderBy('sort_order')->orderBy('name')->get();

        return view('administration.settings.geography.city-areas', compact('city', 'areas'));
    }

    public function import(Request $request, GeographyImportService $service)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:json,txt', 'max:5120'],
        ]);

        $contents = file_get_contents($request->file('file')->getRealPath());
        $data = json_decode($contents, true);

        if (! is_array($data)) {
            alert(__('Invalid JSON'), __('The uploaded file could not be parsed.'), 'error');

            return redirect()->back();
        }

        try {
            $counts = $service->importFromArray($data);
            toast(
                __('Import complete. Countries: :c, Cities: :t, Areas: :a', [
                    'c' => $counts['countries'],
                    't' => $counts['cities'],
                    'a' => $counts['areas'],
                ]),
                'success'
            );
        } catch (\Throwable $e) {
            alert(__('Import failed'), $e->getMessage(), 'error');

            return redirect()->back();
        }

        return redirect()->route('administration.settings.geography.index');
    }

    public function downloadSample(): StreamedResponse
    {
        $path = public_path('samples/geography-import-sample.json');

        return response()->download($path, 'geography-import-sample.json', [
            'Content-Type' => 'application/json',
        ]);
    }

    public function storeCountry(Request $request)
    {
        $validated = $request->validate([
            'iso_code' => ['required', 'string', 'size:2', 'regex:/^[A-Za-z]{2}$/', 'unique:countries,iso_code'],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Country::create([
            'iso_code' => strtoupper($validated['iso_code']),
            'name' => $validated['name'],
            'is_active' => $request->boolean('is_active'),
        ]);

        toast(__('Country added.'), 'success');

        return redirect()->route('administration.settings.geography.index');
    }

    public function storeCity(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('cities')->where('country_id', $country->id)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $nextOrder = (int) ($country->cities()->max('sort_order') ?? 0) + 1;
        $sortOrder = $validated['sort_order'] ?? $nextOrder;

        $country->cities()->create([
            'name' => $validated['name'],
            'sort_order' => $sortOrder,
            'is_active' => $request->boolean('is_active'),
        ]);

        toast(__('City added.'), 'success');

        return redirect()->route('administration.settings.geography.countries.show', $country);
    }

    public function storeArea(Request $request, City $city)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('areas')->where('city_id', $city->id)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $nextOrder = (int) ($city->areas()->max('sort_order') ?? 0) + 1;
        $sortOrder = $validated['sort_order'] ?? $nextOrder;

        $city->areas()->create([
            'name' => $validated['name'],
            'sort_order' => $sortOrder,
            'is_active' => $request->boolean('is_active'),
        ]);

        toast(__('Area added.'), 'success');

        return redirect()->route('administration.settings.geography.cities.show', $city);
    }

    public function toggleCountry(Country $country)
    {
        $country->is_active = ! $country->is_active;
        $country->save();

        toast($country->is_active ? __('Country enabled.') : __('Country disabled.'), 'success');

        return redirect()->back();
    }

    public function toggleCity(City $city)
    {
        $city->is_active = ! $city->is_active;
        $city->save();

        toast($city->is_active ? __('City enabled.') : __('City disabled.'), 'success');

        return redirect()->back();
    }

    public function toggleArea(Area $area)
    {
        $area->is_active = ! $area->is_active;
        $area->save();

        toast($area->is_active ? __('Area enabled.') : __('Area disabled.'), 'success');

        return redirect()->back();
    }
}
