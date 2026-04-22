<?php

namespace App\Livewire\Landlord;

use App\Models\Application;
use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LandlordApplications extends Component
{
    public function acceptApplication(int $applicationId): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Landlord'), 403);

        $application = Application::query()
            ->whereKey($applicationId)
            ->whereHas('property', fn ($q) => $q->where('user_id', $user->id))
            ->with('property')
            ->firstOrFail();

        abort_unless($application->status === Application::STATUS_PENDING, 403);

        DB::transaction(function () use ($application): void {
            $application->update([
                'status' => Application::STATUS_ACCEPTED,
                'accepted_at' => now(),
            ]);
            $application->property->update([
                'status' => Property::STATUS_LET_AGREED,
            ]);
        });

        $this->dispatch('notify', message: __('Application accepted.'), type: 'success');
    }

    public function rejectApplication(int $applicationId): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Landlord'), 403);

        $application = Application::query()
            ->whereKey($applicationId)
            ->whereHas('property', fn ($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        abort_unless($application->status === Application::STATUS_PENDING, 403);

        $application->update([
            'status' => Application::STATUS_REJECTED,
        ]);

        $this->dispatch('notify', message: __('Application declined.'), type: 'warning');
    }

    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Landlord'), 403);

        $applications = Application::query()
            ->whereHas('property', fn ($q) => $q->where('user_id', $user->id))
            ->with(['property.area', 'property.city', 'student', 'latestMessage'])
            ->latest()
            ->get();

        $pending = $applications->where('status', Application::STATUS_PENDING)->values();
        $processed = $applications->where('status', '!=', Application::STATUS_PENDING)->values();

        return view('livewire.landlord.landlord-applications', [
            'pendingApplications' => $pending,
            'processedApplications' => $processed,
        ])->layout('layouts.landlord', [
            'title' => __('Tenant Applications'),
            'pageTitle' => __('Tenant Applications'),
        ]);
    }
}
