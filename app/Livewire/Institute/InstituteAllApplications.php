<?php

namespace App\Livewire\Institute;

use App\Models\Application;
use App\Models\Institute;
use App\Models\InstituteRepresentative;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InstituteAllApplications extends Component
{
    public function render(): View
    {
        $authUser = Auth::user();
        abort_unless($authUser instanceof User && $authUser->hasRole('Institute Representative'), 403);

        $institute = $this->resolveInstitute();

        $applications = Application::query()
            ->whereHas('student', function (Builder $q) use ($institute) {
                $q->whereRoleName('Student')
                    ->where(function (Builder $sq) use ($institute) {
                        $sq->where('institution_id', $institute->id)
                            ->orWhereHas('instituteLocation', function (Builder $lq) use ($institute) {
                                $lq->where('institute_id', $institute->id);
                            });
                    });
            })
            ->with(['property.creator', 'student'])
            ->latest()
            ->get();

        return view('livewire.institute.institute-all-applications', [
            'applications' => $applications,
            'institute' => $institute,
        ])->layout('layouts.institute', [
            'title' => __('Student Applications Overview'),
            'pageTitle' => __('Student Applications Overview'),
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
}
