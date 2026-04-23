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

class InstituteOverview extends Component
{
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

        $instituteStudents = $this->instituteStudentsBaseQuery($institute);

        $totalStudents = (clone $instituteStudents)->count();

        $activeApplications = Application::query()
            ->whereIn('status', [Application::STATUS_PENDING, Application::STATUS_ACCEPTED])
            ->whereHas('student', function (Builder $q) use ($institute) {
                $this->applyInstituteStudentConstraints($q, $institute);
            })
            ->count();

        $pendingVerification = (clone $instituteStudents)
            ->whereIn('account_status', [User::ACCOUNT_STATUS_PENDING, User::ACCOUNT_STATUS_UNVERIFIED])
            ->count();

        $recentSignups = (clone $instituteStudents)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get([
                'id',
                'first_name',
                'last_name',
                'userid',
                'student_id_number',
                'created_at',
                'account_status',
            ]);

        return view('livewire.institute.institute-overview', [
            'institute' => $institute,
            'totalStudents' => $totalStudents,
            'activeApplications' => $activeApplications,
            'pendingVerification' => $pendingVerification,
            'recentSignups' => $recentSignups,
        ])->layout('layouts.institute', [
            'title' => __('University Overview'),
            'pageTitle' => __('University Overview'),
            'pageSubtitle' => $institute->name,
            'instituteOrgName' => $institute->name,
        ]);
    }

    private function instituteStudentsBaseQuery(Institute $institute): Builder
    {
        return User::query()
            ->whereRoleName('Student')
            ->where(function (Builder $q) use ($institute) {
                $q->where('institution_id', $institute->id)
                    ->orWhereHas('instituteLocation', function (Builder $lq) use ($institute) {
                        $lq->where('institute_id', $institute->id);
                    });
            });
    }

    private function applyInstituteStudentConstraints(Builder $query, Institute $institute): void
    {
        $query->whereRoleName('Student')
            ->where(function (Builder $q) use ($institute) {
                $q->where('institution_id', $institute->id)
                    ->orWhereHas('instituteLocation', function (Builder $lq) use ($institute) {
                        $lq->where('institute_id', $institute->id);
                    });
            });
    }
}
