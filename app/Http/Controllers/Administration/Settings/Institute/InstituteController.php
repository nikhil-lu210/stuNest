<?php

namespace App\Http\Controllers\Administration\Settings\Institute;

use Exception;
use App\Models\Institute;
use App\Models\InstituteLocation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\Settings\Institute\InstituteStoreRequest;
use App\Http\Requests\Administration\Settings\Institute\InstituteUpdateRequest;

class InstituteController extends Controller
{
    public function index()
    {
        $institutes = Institute::query()
            ->withCount(['locations', 'representatives'])
            ->latest()
            ->get();

        return view('administration.settings.institute.index', compact('institutes'));
    }

    public function create()
    {
        return view('administration.settings.institute.create');
    }

    public function store(InstituteStoreRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $institute = Institute::create([
                    'name' => $request->name,
                    'email_code' => Institute::normalizeEmailCode($request->email_code),
                ]);

                foreach ($request->locations as $index => $row) {
                    $institute->locations()->create([
                        'name' => $row['name'],
                        'address_line_1' => $row['address_line_1'] ?? null,
                        'city' => $row['city'] ?? null,
                        'postcode' => $row['postcode'] ?? null,
                        'country' => ! empty($row['country']) ? strtoupper($row['country']) : 'GB',
                        'is_primary' => ! empty($row['is_primary']),
                        'sort_order' => $index,
                    ]);
                }
            }, 5);

            toast('Institute has been registered.', 'success');

            return redirect()->route('administration.settings.institute.index');
        } catch (Exception $e) {
            alert('Error.', $e->getMessage(), 'error');

            return redirect()->back()->withInput();
        }
    }

    public function show(Institute $institute)
    {
        $institute->load([
            'locations' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
            'representatives.user',
            'representatives.location',
        ]);

        return view('administration.settings.institute.show', compact('institute'));
    }

    public function edit(Institute $institute)
    {
        $institute->load([
            'locations' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
        ]);

        return view('administration.settings.institute.edit', compact('institute'));
    }

    public function update(InstituteUpdateRequest $request, Institute $institute)
    {
        try {
            DB::transaction(function () use ($request, $institute) {
                $institute->update([
                    'name' => $request->name,
                    'email_code' => Institute::normalizeEmailCode($request->email_code),
                ]);

                $incomingIds = [];
                foreach ($request->locations as $index => $row) {
                    if (! empty($row['id'])) {
                        $location = InstituteLocation::where('institute_id', $institute->id)
                            ->whereKey($row['id'])
                            ->firstOrFail();
                        $location->update([
                            'name' => $row['name'],
                            'address_line_1' => $row['address_line_1'] ?? null,
                            'city' => $row['city'] ?? null,
                            'postcode' => $row['postcode'] ?? null,
                            'country' => ! empty($row['country']) ? strtoupper($row['country']) : 'GB',
                            'is_primary' => ! empty($row['is_primary']),
                            'sort_order' => $index,
                        ]);
                        $incomingIds[] = $location->id;
                    } else {
                        $created = $institute->locations()->create([
                            'name' => $row['name'],
                            'address_line_1' => $row['address_line_1'] ?? null,
                            'city' => $row['city'] ?? null,
                            'postcode' => $row['postcode'] ?? null,
                            'country' => ! empty($row['country']) ? strtoupper($row['country']) : 'GB',
                            'is_primary' => ! empty($row['is_primary']),
                            'sort_order' => $index,
                        ]);
                        $incomingIds[] = $created->id;
                    }
                }

                $toRemove = $institute->locations()->whereNotIn('id', $incomingIds)->get();
                foreach ($toRemove as $location) {
                    if ($location->representatives()->exists()) {
                        throw new Exception('Cannot remove branch "'.$location->name.'" because it still has representatives assigned.');
                    }
                    $location->delete();
                }
            }, 5);

            toast('Institute has been updated.', 'success');

            return redirect()->route('administration.settings.institute.show', $institute);
        } catch (Exception $e) {
            alert('Error.', $e->getMessage(), 'error');

            return redirect()->back()->withInput();
        }
    }
}
