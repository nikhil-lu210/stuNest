<?php

namespace App\Livewire\Institute;

use App\Models\Application;
use App\Models\Institute;
use App\Models\InstituteRepresentative;
use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InstituteOurApplications extends Component
{
    public function acceptApplication(int $id): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Institute Representative'), 403);

        $application = Application::query()
            ->whereKey($id)
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

    public function rejectApplication(int $id): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Institute Representative'), 403);

        $application = Application::query()
            ->whereKey($id)
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
        abort_unless($user instanceof User && $user->hasRole('Institute Representative'), 403);

        $representation = InstituteRepresentative::query()
            ->where('user_id', $user->id)
            ->with('institute')
            ->first();

        abort_if($representation === null || ! $representation->institute instanceof Institute, 403);

        $institute = $representation->institute;

        $applications = Application::query()
            ->whereHas('property', fn ($q) => $q->where('user_id', $user->id))
            ->with(['property.area', 'property.city', 'student', 'latestMessage'])
            ->latest()
            ->get();

        return view('livewire.institute.institute-our-applications', [
            'applications' => $applications,
            'institute' => $institute,
        ])->layout('layouts.institute', [
            'title' => __('Applications for Our Properties'),
            'pageTitle' => __('Applications for Our Properties'),
            'pageSubtitle' => $institute->name,
            'instituteOrgName' => $institute->name,
        ]);
    }
}
