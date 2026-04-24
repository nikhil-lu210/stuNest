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

class InstituteApplicationDetails extends Component
{
    public Application $application;

    public bool $isOwner = false;

    public function mount(int $applicationId): void
    {
        $authUser = Auth::user();
        abort_unless($authUser instanceof User && $authUser->hasRole('Institute Representative'), 403);

        $application = Application::query()
            ->whereKey($applicationId)
            ->with([
                'property.owner',
                'property.media',
                'property.area',
                'property.city',
                'user',
            ])
            ->firstOrFail();

        $property = $application->property;
        abort_if($property === null, 404);

        $institute = $this->resolveInstitute();

        $applicant = $application->user;
        abort_if($applicant === null, 404);

        $ownsProperty = (int) $property->user_id === (int) $authUser->id;
        $studentBelongsToInstitute = $this->studentBelongsToInstitute($applicant, $institute);

        abort_unless($ownsProperty || $studentBelongsToInstitute, 403);

        $this->isOwner = $ownsProperty;
        $this->application = $application;
    }

    public function acceptApplication(): void
    {
        if (! $this->isOwner) {
            abort(403);
        }

        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $application = Application::query()
            ->whereKey($this->application->id)
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

        $this->application->refresh();
        $this->dispatch('notify', message: __('Application accepted.'), type: 'success');
    }

    public function rejectApplication(): void
    {
        if (! $this->isOwner) {
            abort(403);
        }

        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $application = Application::query()
            ->whereKey($this->application->id)
            ->whereHas('property', fn ($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        abort_unless($application->status === Application::STATUS_PENDING, 403);

        $application->update([
            'status' => Application::STATUS_REJECTED,
        ]);

        $this->application->refresh();
        $this->dispatch('notify', message: __('Application declined.'), type: 'warning');
    }

    public function render(): View
    {
        $institute = $this->resolveInstitute();

        return view('livewire.institute.institute-application-details', [
            'institute' => $institute,
        ])->layout('layouts.institute', [
            'title' => __('Application Details'),
            'pageTitle' => __('Application Details'),
            'pageSubtitle' => $institute->name,
            'instituteOrgName' => $institute->name,
        ]);
    }

    private function resolveInstitute(): Institute
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $representation = InstituteRepresentative::query()
            ->where('user_id', $user->id)
            ->with('institute')
            ->first();

        abort_if($representation === null || ! $representation->institute instanceof Institute, 403);

        return $representation->institute;
    }

    /**
     * Students are tied to an institute via institution_id or institute_location_id (not a user.institute_id column).
     */
    private function studentBelongsToInstitute(User $student, Institute $institute): bool
    {
        if ((int) $student->institution_id === (int) $institute->id) {
            return true;
        }

        $student->loadMissing('instituteLocation');

        return $student->instituteLocation !== null
            && (int) $student->instituteLocation->institute_id === (int) $institute->id;
    }
}
