<?php

namespace App\Http\Controllers\Api\Geography;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * JSON endpoints for admin UI (Select2 / cascading dropdowns).
 * Registered under web + auth; not the same as routes/api.php Sanctum group.
 */
class GeographyLookupController extends Controller
{
    public function countries(Request $request)
    {
        $q = Country::query()->active()->orderBy('name');
        if ($request->filled('q')) {
            $term = '%'.$request->get('q').'%';
            $q->where(function ($w) use ($term) {
                $w->where('name', 'like', $term)->orWhere('iso_code', 'like', $term);
            });
        }

        return response()->json([
            'results' => $q->get(['id', 'name', 'iso_code'])->map(fn ($c) => [
                'id' => $c->id,
                'text' => $c->name.' ('.$c->iso_code.')',
            ]),
        ]);
    }

    public function cities(Request $request)
    {
        $request->validate([
            'country_id' => ['required', 'integer', 'exists:countries,id'],
        ]);

        $selectedId = $request->integer('selected_id');

        $q = City::query()
            ->where('country_id', $request->integer('country_id'))
            ->where(function ($w) use ($selectedId) {
                $w->where('is_active', true);
                if ($selectedId) {
                    $w->orWhere('id', $selectedId);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($request->filled('q')) {
            $q->where('name', 'like', '%'.$request->get('q').'%');
        }

        return response()->json([
            'results' => $q->get(['id', 'name'])->map(fn ($c) => [
                'id' => $c->id,
                'text' => $c->name,
            ]),
        ]);
    }

    public function areas(Request $request)
    {
        $request->validate([
            'city_id' => ['required', 'integer', 'exists:cities,id'],
        ]);

        $selectedId = $request->integer('selected_id');

        $q = Area::query()
            ->where('city_id', $request->integer('city_id'))
            ->where(function ($w) use ($selectedId) {
                $w->where('is_active', true);
                if ($selectedId) {
                    $w->orWhere('id', $selectedId);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($request->filled('q')) {
            $q->where('name', 'like', '%'.$request->get('q').'%');
        }

        return response()->json([
            'results' => $q->get(['id', 'name'])->map(fn ($a) => [
                'id' => $a->id,
                'text' => $a->name,
            ]),
        ]);
    }
}
