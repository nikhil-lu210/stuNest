<?php

namespace App\Livewire\Landlord;

use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LandlordProperties extends Component
{
    public string $search = '';

    public function toggleStatus(int $propertyId): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Landlord'), 403);

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
        abort_unless($user instanceof User && $user->hasRole('Landlord'), 403);

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
        abort_unless($user instanceof User && $user->hasRole('Landlord'), 403);

        $term = trim($this->search);
        $like = $term !== '' ? '%'.addcslashes($term, '%_\\').'%' : null;

        $properties = Property::query()
            ->where('user_id', $user->id)
            ->with(['media', 'area', 'city', 'applications'])
            ->when($like !== null, function ($query) use ($like) {
                $query->where(function ($q2) use ($like) {
                    $q2->where('listing_category', 'like', $like)
                        ->orWhere('property_type', 'like', $like)
                        ->orWhereHas('area', fn ($a) => $a->where('name', 'like', $like))
                        ->orWhereHas('city', fn ($c) => $c->where('name', 'like', $like));
                });
            })
            ->latest()
            ->get();

        return view('livewire.landlord.landlord-properties', [
            'properties' => $properties,
        ])->layout('layouts.landlord', [
            'title' => __('My Properties'),
            'pageTitle' => __('My Properties'),
        ]);
    }
}
