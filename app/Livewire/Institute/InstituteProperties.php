<?php

namespace App\Livewire\Institute;

use App\Models\Institute;
use App\Models\InstituteRepresentative;
use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InstituteProperties extends Component
{
    public function toggleStatus(int $propertyId): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Institute Representative'), 403);

        $property = Property::query()
            ->where('user_id', $user->id)
            ->whereKey($propertyId)
            ->firstOrFail();

        abort_unless($user->can('update', $property), 403);

        if (in_array($property->status, [Property::STATUS_LET_AGREED, 'rented'], true)) {
            return;
        }

        if ($property->status === Property::STATUS_PUBLISHED) {
            $property->update(['status' => Property::STATUS_DRAFT]);
            $this->dispatch('notify', message: __('Listing paused. It is no longer visible to students.'), type: 'success');

            return;
        }

        if (in_array($property->status, [Property::STATUS_DRAFT, Property::STATUS_ARCHIVED], true)) {
            $property->update(['status' => Property::STATUS_PUBLISHED]);
            $this->dispatch('notify', message: __('Listing is now live and visible to students.'), type: 'success');
        }
    }

    public function deleteProperty(int $propertyId): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Institute Representative'), 403);

        $property = Property::query()
            ->where('user_id', $user->id)
            ->whereKey($propertyId)
            ->firstOrFail();

        abort_unless($user->can('delete', $property), 403);

        $property->delete();
        $this->dispatch('notify', message: __('Listing removed.'), type: 'success');
    }

    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Institute Representative'), 403);

        $representation = InstituteRepresentative::query()
            ->where('user_id', $user->id)
            ->with('institute')
            ->first();

        abort_if($representation === null || ! $representation->institute instanceof Institute, 403);

        $institute = $representation->institute;

        $properties = Property::query()
            ->where('user_id', $user->id)
            ->with(['media', 'area', 'city', 'applications'])
            ->latest()
            ->get();

        return view('livewire.institute.institute-properties', [
            'properties' => $properties,
        ])->layout('layouts.institute', [
            'title' => __('All Properties'),
            'pageTitle' => __('All Properties'),
            'pageSubtitle' => $institute->name,
            'instituteOrgName' => $institute->name,
        ]);
    }
}
