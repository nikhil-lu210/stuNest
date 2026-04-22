<?php

namespace App\Livewire\Landlord;

use App\Models\Application;
use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LandlordOverview extends Component
{
    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Landlord'), 403);

        $userId = $user->id;

        $totalProperties = Property::query()
            ->where('user_id', $userId)
            ->count();

        // Live on the marketplace (`published`). No separate `available` status exists on Property.
        $activeListings = Property::query()
            ->where('user_id', $userId)
            ->where('status', Property::STATUS_PUBLISHED)
            ->count();

        $pendingApplications = Application::query()
            ->whereHas('property', fn ($q) => $q->where('user_id', $userId))
            ->where('status', Application::STATUS_PENDING)
            ->count();

        $recentApplications = Application::query()
            ->whereHas('property', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->where('status', Application::STATUS_PENDING)
            ->latest()
            ->take(5)
            ->with([
                'property' => fn ($q) => $q->with(['area', 'city']),
                'student',
            ])
            ->get();

        return view('livewire.landlord.landlord-overview', [
            'totalProperties' => $totalProperties,
            'activeListings' => $activeListings,
            'pendingApplications' => $pendingApplications,
            'recentApplications' => $recentApplications,
        ])->layout('layouts.landlord', [
            'title' => __('Dashboard Overview'),
            'pageTitle' => __('Dashboard Overview'),
        ]);
    }
}
