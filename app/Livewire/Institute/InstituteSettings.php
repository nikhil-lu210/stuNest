<?php

namespace App\Livewire\Institute;

use App\Models\Institute;
use App\Models\InstituteRepresentative;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Component;
use Livewire\WithFileUploads;

class InstituteSettings extends Component
{
    use WithFileUploads;

    public string $first_name = '';

    public string $last_name = '';

    public string $phone = '';

    public string $institute_name = '';

    public string $department = '';

    /** @var mixed */
    public $newAvatar;

    public string $current_password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    public bool $notify_student_pending_verification = true;

    public bool $notify_student_applied_our_properties = true;

    public bool $notify_support_message = true;

    public function mount(): void
    {
        $user = $this->representative();
        $institute = $this->resolveInstitute();

        $this->first_name = $user->first_name ?? '';
        $this->last_name = $user->last_name ?? '';
        $this->phone = $user->phone ?? '';
        $this->institute_name = $institute->name ?? '';
        $prefs = $user->preferences ?? [];
        $this->department = (string) ($prefs['institute_representative_department'] ?? '');
        $this->notify_student_pending_verification = (bool) ($prefs['email_institute_student_pending_verification'] ?? true);
        $this->notify_student_applied_our_properties = (bool) ($prefs['email_institute_student_applied_our_properties'] ?? true);
        $this->notify_support_message = (bool) ($prefs['email_institute_support_message'] ?? true);
    }

    public function updatedNewAvatar(): void
    {
        $this->validateOnly('newAvatar', [
            'newAvatar' => ['nullable', 'image', 'max:5120'],
        ]);
    }

    public function updateProfile(): void
    {
        $user = $this->representative();
        $institute = $this->resolveInstitute();

        $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'institute_name' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'newAvatar' => ['nullable', 'image', 'max:5120'],
        ]);

        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->phone = $this->phone !== '' ? $this->phone : null;

        $prefs = $user->preferences ?? [];
        $prefs['institute_representative_department'] = $this->department !== '' ? $this->department : null;
        $user->preferences = $prefs;
        $user->save();

        $institute->name = $this->institute_name;
        $institute->save();

        if ($this->newAvatar) {
            $user->clearMediaCollection('avatar');
            $user->addMedia($this->newAvatar->getRealPath())
                ->usingFileName($this->newAvatar->getClientOriginalName())
                ->toMediaCollection('avatar');
            $this->newAvatar = null;
        }

        $this->dispatch('notify', message: __('Profile updated successfully'), type: 'success');
    }

    public function updatePassword(): void
    {
        $user = $this->representative();

        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'confirmed', PasswordRule::min(8)],
        ]);

        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->dispatch('notify', message: __('Password updated successfully'), type: 'success');
    }

    public function savePreferences(): void
    {
        $user = $this->representative();

        $this->validate([
            'notify_student_pending_verification' => ['boolean'],
            'notify_student_applied_our_properties' => ['boolean'],
            'notify_support_message' => ['boolean'],
        ]);

        $prefs = $user->preferences ?? [];
        $prefs['email_institute_student_pending_verification'] = $this->notify_student_pending_verification;
        $prefs['email_institute_student_applied_our_properties'] = $this->notify_student_applied_our_properties;
        $prefs['email_institute_support_message'] = $this->notify_support_message;
        $user->preferences = $prefs;
        $user->save();

        $this->dispatch('notify', message: __('Preferences saved'), type: 'success');
    }

    public function render(): View
    {
        $user = $this->representative()->fresh('media');
        $institute = $this->resolveInstitute();

        return view('livewire.institute.institute-settings', [
            'user' => $user,
            'institute' => $institute,
        ])->layout('layouts.institute', [
            'title' => __('Institution Settings'),
            'pageTitle' => __('Institution Settings'),
            'pageSubtitle' => $institute->name,
            'instituteOrgName' => $institute->name,
        ]);
    }

    private function representative(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Institute Representative'), 403);

        return $user;
    }

    private function resolveInstitute(): Institute
    {
        $user = $this->representative();

        $representation = InstituteRepresentative::query()
            ->where('user_id', $user->id)
            ->with('institute')
            ->first();

        abort_if($representation === null || ! $representation->institute instanceof Institute, 403);

        return $representation->institute;
    }
}
