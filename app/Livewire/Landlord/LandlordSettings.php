<?php

namespace App\Livewire\Landlord;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Component;
use Livewire\WithFileUploads;

class LandlordSettings extends Component
{
    use WithFileUploads;

    public string $first_name = '';

    public string $last_name = '';

    public string $phone = '';

    public string $company_name = '';

    /** @var mixed */
    public $newAvatar;

    public string $current_password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    public bool $notify_new_application = true;

    public bool $notify_student_message = true;

    public bool $notify_platform_updates = true;

    public function mount(): void
    {
        $user = $this->landlord();
        $this->first_name = $user->first_name ?? '';
        $this->last_name = $user->last_name ?? '';
        $this->phone = $user->phone ?? '';
        $this->company_name = $user->company_name ?? '';

        $prefs = $user->preferences ?? [];
        $this->notify_new_application = (bool) ($prefs['email_landlord_new_application'] ?? true);
        $this->notify_student_message = (bool) ($prefs['email_landlord_student_message'] ?? true);
        $this->notify_platform_updates = (bool) ($prefs['email_landlord_platform_updates'] ?? true);
    }

    protected function landlord(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Landlord'), 403);

        return $user;
    }

    public function updatedNewAvatar(): void
    {
        $this->validateOnly('newAvatar', [
            'newAvatar' => ['nullable', 'image', 'max:5120'],
        ]);
    }

    public function updateProfile(): void
    {
        $user = $this->landlord();

        $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'newAvatar' => ['nullable', 'image', 'max:5120'],
        ]);

        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->phone = $this->phone !== '' ? $this->phone : null;
        $user->company_name = $this->company_name !== '' ? $this->company_name : null;
        $user->save();

        if ($this->newAvatar) {
            $user->clearMediaCollection('avatar');
            $user->addMedia($this->newAvatar->getRealPath())
                ->usingFileName($this->newAvatar->getClientOriginalName())
                ->toMediaCollection('avatar');
            $this->newAvatar = null;
        }

        $this->dispatch('notify', message: __('Profile updated successfully.'), type: 'success');
    }

    public function updatePassword(): void
    {
        $user = $this->landlord();

        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'confirmed', PasswordRule::min(8)],
        ]);

        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->dispatch('notify', message: __('Password updated successfully.'), type: 'success');
    }

    public function savePreferences(): void
    {
        $user = $this->landlord();

        $this->validate([
            'notify_new_application' => ['boolean'],
            'notify_student_message' => ['boolean'],
            'notify_platform_updates' => ['boolean'],
        ]);

        $prefs = $user->preferences ?? [];
        $prefs['email_landlord_new_application'] = $this->notify_new_application;
        $prefs['email_landlord_student_message'] = $this->notify_student_message;
        $prefs['email_landlord_platform_updates'] = $this->notify_platform_updates;
        $user->preferences = $prefs;
        $user->save();

        $this->dispatch('notify', message: __('Preferences saved.'), type: 'success');
    }

    public function render(): View
    {
        return view('livewire.landlord.landlord-settings', [
            'user' => $this->landlord()->fresh('media'),
        ])->layout('layouts.landlord', [
            'title' => __('Account Settings'),
            'pageTitle' => __('Account Settings'),
        ]);
    }
}
