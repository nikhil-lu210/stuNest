<?php

namespace App\Livewire\Student;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentSavedProperties extends Component
{
    public function unsaveProperty(int $propertyId): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasStudentRole(), 403);

        $user->savedProperties()->detach($propertyId);
    }

    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasStudentRole(), 403);

        $savedProperties = $user->savedProperties()
            ->with('media')
            ->orderByPivot('created_at', 'desc')
            ->get();

        return view('livewire.student.student-saved-properties', [
            'savedProperties' => $savedProperties,
        ]);
    }
}
