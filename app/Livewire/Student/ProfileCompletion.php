<?php

namespace App\Livewire\Student;

use App\Models\User;
use App\Support\StudentCountryList;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProfileCompletion extends Component
{
    public string $student_id = '';

    public string $country_of_citizen = '';

    public function mount(): void
    {
        $user = $this->student();
        $this->student_id = (string) ($user->student_id ?? '');
        $this->country_of_citizen = (string) ($user->country_of_citizen ?? '');
    }

    public function saveProfile(): void
    {
        $codes = array_column(StudentCountryList::all(), 'code');
        $this->validate([
            'student_id' => ['required', 'string', 'max:100'],
            'country_of_citizen' => ['required', 'string', 'size:2', Rule::in($codes)],
        ]);

        $user = $this->student();
        $user->forceFill([
            'student_id' => $this->student_id,
            'country_of_citizen' => strtoupper($this->country_of_citizen),
            'is_profile_complete' => true,
        ])->save();

        $this->redirect(route('client.student.dashboard'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.student.profile-completion', [
            'countries' => StudentCountryList::all(),
        ]);
    }

    private function student(): User
    {
        $user = auth()->user();
        abort_unless($user instanceof User && $user->hasStudentRole(), 403);

        return $user;
    }
}
