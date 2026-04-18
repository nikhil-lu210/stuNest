<?php

namespace App\Http\Controllers\Client\Explore;

use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index(Request $request): View
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

        $properties = $query->latest()->paginate(12)->withQueryString();

        $filters = [
            'q' => $q,
            'move_in' => $request->input('move_in'),
            'guests' => $guests,
        ];

        return view('client.explore.index', compact('properties', 'filters'));
    }
}
