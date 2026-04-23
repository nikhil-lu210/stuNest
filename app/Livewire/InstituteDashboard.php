<?php

namespace App\Livewire;

use App\Models\Application;
use App\Models\Institute;
use App\Models\InstituteRepresentative;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InstituteDashboard extends Component
{
    public string $dateRange = 'this_year';

    public function exportReport(): void
    {
        $this->dispatch('notify', message: __('Report exported successfully'), type: 'success');
    }

    public function render(): View
    {
        $authUser = Auth::user();
        abort_unless($authUser instanceof User && $authUser->hasRole('Institute Representative'), 403);

        $institute = $this->resolveInstitute();
        [$start, $end] = $this->dateRangeBounds();

        $userBase = $this->instituteStudentsBaseQuery($institute);
        $userInRange = (clone $userBase);
        $this->applyCreatedAtRange($userInRange, $start, $end);

        $totalStudents = (clone $userInRange)->count();
        $verifiedStudents = (clone $userInRange)->where('account_status', User::ACCOUNT_STATUS_ACTIVE)->count();
        $pendingStudents = (clone $userInRange)->whereIn('account_status', [
            User::ACCOUNT_STATUS_PENDING,
            User::ACCOUNT_STATUS_UNVERIFIED,
        ])->count();
        $rejectedStudents = (clone $userInRange)->where('account_status', User::ACCOUNT_STATUS_REJECTED)->count();

        $applicationQuery = Application::query()
            ->whereHas('student', function (Builder $q) use ($institute) {
                $this->applyInstituteStudentConstraints($q, $institute);
            });
        $this->applyCreatedAtRange($applicationQuery, $start, $end);

        $totalApplications = (clone $applicationQuery)->count();
        $acceptedApplications = (clone $applicationQuery)->where('status', Application::STATUS_ACCEPTED)->count();

        $successRatePercent = $totalApplications > 0
            ? round(($acceptedApplications / $totalApplications) * 100, 1)
            : null;

        $applicationByMonth = $this->applicationsGroupedByMonth($institute, $start, $end);

        $statusSegments = $this->statusDistributionSegments(
            $totalStudents,
            $verifiedStudents,
            $pendingStudents,
            $rejectedStudents
        );

        $recentAcceptedApplications = Application::query()
            ->where('status', Application::STATUS_ACCEPTED)
            ->whereNotNull('accepted_at')
            ->whereHas('student', function (Builder $q) use ($institute) {
                $this->applyInstituteStudentConstraints($q, $institute);
            })
            ->with([
                'student',
                'property' => fn ($q) => $q->with(['area', 'city']),
            ])
            ->when($start !== null, fn (Builder $q) => $q->where('accepted_at', '>=', $start))
            ->when($end !== null, fn (Builder $q) => $q->where('accepted_at', '<=', $end))
            ->orderByDesc('accepted_at')
            ->limit(10)
            ->get();

        return view('livewire.institute-dashboard', [
            'institute' => $institute,
            'totalStudents' => $totalStudents,
            'verifiedStudents' => $verifiedStudents,
            'pendingStudents' => $pendingStudents,
            'rejectedStudents' => $rejectedStudents,
            'totalApplications' => $totalApplications,
            'acceptedApplications' => $acceptedApplications,
            'successRatePercent' => $successRatePercent,
            'applicationByMonth' => $applicationByMonth,
            'statusSegments' => $statusSegments,
            'recentAcceptedApplications' => $recentAcceptedApplications,
        ])->layout('layouts.institute', [
            'title' => __('Dashboard'),
            'pageTitle' => __('Dashboard'),
            'pageSubtitle' => $institute->name,
            'instituteOrgName' => $institute->name,
        ]);
    }

    /**
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    private function dateRangeBounds(): array
    {
        return match ($this->dateRange) {
            'this_month' => [now()->copy()->startOfMonth(), now()->copy()->endOfDay()],
            'last_6_months' => [now()->copy()->subMonths(6)->startOfDay(), now()->copy()->endOfDay()],
            'this_year' => [now()->copy()->startOfYear(), now()->copy()->endOfDay()],
            'all_time' => [null, null],
            default => [now()->copy()->startOfYear(), now()->copy()->endOfDay()],
        };
    }

    /**
     * @return list<array{label: string, sub_label: string, count: int, height_percent: float}>
     */
    private function applicationsGroupedByMonth(Institute $institute, ?Carbon $start, ?Carbon $end): array
    {
        $query = Application::query()
            ->whereHas('student', function (Builder $q) use ($institute) {
                $this->applyInstituteStudentConstraints($q, $institute);
            });
        $this->applyCreatedAtRange($query, $start, $end);

        $counts = $query->get(['created_at'])
            ->groupBy(fn (Application $a) => $a->created_at->format('Y-m'))
            ->map->count();

        if ($start !== null && $end !== null) {
            $cursor = $start->copy()->startOfMonth();
            $last = $end->copy()->startOfMonth();
        } elseif ($this->dateRange === 'this_month') {
            $cursor = now()->copy()->startOfMonth();
            $last = $cursor->copy();
        } elseif ($this->dateRange === 'last_6_months') {
            $cursor = now()->copy()->subMonths(5)->startOfMonth();
            $last = now()->copy()->startOfMonth();
        } elseif ($this->dateRange === 'this_year') {
            $cursor = now()->copy()->startOfYear();
            $last = now()->copy()->startOfMonth();
        } else {
            $sortedKeys = $counts->keys()->sort()->values();
            if ($sortedKeys->isEmpty()) {
                $cursor = now()->copy()->subMonths(11)->startOfMonth();
                $last = now()->copy()->startOfMonth();
            } else {
                if ($sortedKeys->count() > 12) {
                    $sortedKeys = $sortedKeys->slice(-12)->values();
                }
                $cursor = Carbon::createFromFormat('Y-m', $sortedKeys->first())->startOfMonth();
                $last = Carbon::createFromFormat('Y-m', $sortedKeys->last())->startOfMonth();
            }
        }

        $max = max((int) $counts->max(), 1);
        $out = [];
        $c = $cursor->copy();
        while ($c->lte($last)) {
            $key = $c->format('Y-m');
            $count = (int) ($counts[$key] ?? 0);
            $out[] = [
                'label' => $c->format('M'),
                'sub_label' => $c->format('y'),
                'count' => $count,
                'height_percent' => round(($count / $max) * 100, 1),
            ];
            $c->addMonth();
        }

        return $out;
    }

    /**
     * @return list<array{key: string, label: string, percent: float, class: string}>
     */
    private function statusDistributionSegments(
        int $total,
        int $verified,
        int $pending,
        int $rejected
    ): array {
        if ($total === 0) {
            return [
                ['key' => 'verified', 'label' => __('Verified'), 'percent' => 0.0, 'class' => 'bg-green-500'],
                ['key' => 'pending', 'label' => __('Pending'), 'percent' => 0.0, 'class' => 'bg-amber-500'],
                ['key' => 'rejected', 'label' => __('Rejected'), 'percent' => 0.0, 'class' => 'bg-gray-400'],
            ];
        }

        return [
            [
                'key' => 'verified',
                'label' => __('Verified'),
                'percent' => round(($verified / $total) * 100, 1),
                'class' => 'bg-green-500',
            ],
            [
                'key' => 'pending',
                'label' => __('Pending'),
                'percent' => round(($pending / $total) * 100, 1),
                'class' => 'bg-amber-500',
            ],
            [
                'key' => 'rejected',
                'label' => __('Rejected'),
                'percent' => round(($rejected / $total) * 100, 1),
                'class' => 'bg-gray-400',
            ],
        ];
    }

    private function applyCreatedAtRange(Builder $query, ?Carbon $start, ?Carbon $end): void
    {
        if ($start !== null) {
            $query->where('created_at', '>=', $start);
        }
        if ($end !== null) {
            $query->where('created_at', '<=', $end);
        }
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
