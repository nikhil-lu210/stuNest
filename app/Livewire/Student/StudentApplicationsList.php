<?php

namespace App\Livewire\Student;

use App\Models\Application;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentApplicationsList extends Component
{
    public function withdrawApplication(int $applicationId): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasStudentRole(), 403);

        $application = Application::query()
            ->whereKey($applicationId)
            ->where('user_id', $user->id)
            ->first();

        abort_unless($application, 403);

        if ($application->status !== Application::STATUS_PENDING) {
            return;
        }

        $application->update(['status' => Application::STATUS_WITHDRAWN]);
    }

    /**
     * Human-readable proposed tenancy from stored weeks and listing rent unit.
     */
    public function proposedTenancyLabel(Application $application): string
    {
        $weeks = max(1, (int) $application->proposed_duration_weeks);
        $property = $application->property;

        if (! $property) {
            return trans_choice(':count week|:count weeks', $weeks, ['count' => $weeks]);
        }

        return match ($property->rent_duration ?? 'week') {
            'day' => __('~:count days', ['count' => max(1, (int) round($weeks * 7))]),
            'month' => __('~:count months', ['count' => max(1, (int) round($weeks / 52 * 12))]),
            default => __(':count weeks', ['count' => $weeks]),
        };
    }

    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasStudentRole(), 403);

        $applications = $user->applications()
            ->with(['property.media', 'property.area', 'property.city'])
            ->latest()
            ->get();

        return view('livewire.student.student-applications-list', [
            'applications' => $applications,
        ]);
    }
}
