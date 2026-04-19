<?php

namespace App\Livewire\Student;

use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentListings extends Component
{
    public function toggleStatus(int $propertyId): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasStudentRole(), 403);

        $property = Property::query()
            ->where('user_id', $user->id)
            ->whereKey($propertyId)
            ->firstOrFail();

        abort_unless($user->can('update', $property), 403);

        if ($property->status === Property::STATUS_PUBLISHED) {
            $property->update(['status' => Property::STATUS_DRAFT]);

            return;
        }

        if (in_array($property->status, [Property::STATUS_DRAFT, Property::STATUS_ARCHIVED], true)) {
            $property->update(['status' => Property::STATUS_PUBLISHED]);
        }
    }

    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasStudentRole(), 403);

        $properties = Property::query()
            ->where('user_id', $user->id)
            ->with('media')
            ->latest()
            ->get();

        return view('livewire.student.student-listings', [
            'properties' => $properties,
        ]);
    }
}
