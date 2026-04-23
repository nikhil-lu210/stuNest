<?php

namespace App\Livewire\Institute;

use App\Models\Institute;
use App\Models\InstituteRepresentative;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class InstituteStudents extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public function mount(): void
    {
        if (request()->routeIs('client.institute.students.unverified')) {
            $this->statusFilter = 'pending';
        } elseif (request()->routeIs('client.institute.students.index')) {
            $this->statusFilter = 'all';
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'statusFilter'], true)) {
            $this->resetPage();
        }
    }

    public function verifyStudent(int $userId): void
    {
        $institute = $this->resolveInstitute();
        $student = User::query()->whereKey($userId)->whereRoleName('Student')->first();

        abort_unless($student !== null && $this->studentBelongsToInstitute($student, $institute), 403);
        abort_unless(in_array($student->account_status, [User::ACCOUNT_STATUS_PENDING, User::ACCOUNT_STATUS_UNVERIFIED], true), 403);

        $student->update(['account_status' => User::ACCOUNT_STATUS_ACTIVE]);

        $this->dispatch('notify', message: __('Student Verified Successfully'), type: 'success');
    }

    public function rejectStudent(int $userId): void
    {
        $institute = $this->resolveInstitute();
        $student = User::query()->whereKey($userId)->whereRoleName('Student')->first();

        abort_unless($student !== null && $this->studentBelongsToInstitute($student, $institute), 403);
        abort_unless(in_array($student->account_status, [User::ACCOUNT_STATUS_PENDING, User::ACCOUNT_STATUS_UNVERIFIED], true), 403);

        $student->update(['account_status' => User::ACCOUNT_STATUS_REJECTED]);

        $this->dispatch('notify', message: __('Student Application Rejected'), type: 'warning');
    }

    public function render(): View
    {
        $authUser = Auth::user();
        abort_unless($authUser instanceof User && $authUser->hasRole('Institute Representative'), 403);

        $institute = $this->resolveInstitute();

        $query = $this->instituteStudentsBaseQuery($institute);

        if ($this->statusFilter === 'pending') {
            $query->whereIn('account_status', [User::ACCOUNT_STATUS_PENDING, User::ACCOUNT_STATUS_UNVERIFIED]);
        } elseif ($this->statusFilter === 'verified') {
            $query->where('account_status', User::ACCOUNT_STATUS_ACTIVE);
        }

        $search = trim($this->search);
        if ($search !== '') {
            $term = '%'.$search.'%';
            $query->where(function (Builder $q) use ($term) {
                $q->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('student_id_number', 'like', $term)
                    ->orWhere('userid', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        $students = $query
            ->orderByDesc('created_at')
            ->paginate(15);

        $pageTitle = request()->routeIs('client.institute.students.unverified')
            ? __('Unverified Students')
            : __('All Students');

        return view('livewire.institute.institute-students', [
            'institute' => $institute,
            'students' => $students,
        ])->layout('layouts.institute', [
            'title' => $pageTitle,
            'pageTitle' => $pageTitle,
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
