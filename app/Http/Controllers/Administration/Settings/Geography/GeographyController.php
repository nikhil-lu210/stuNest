<?php

namespace App\Http\Controllers\Administration\Settings\Geography;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\GeographyImportService;
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
