<?php

namespace App\Livewire\Student;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Component;
use Livewire\WithFileUploads;

class StudentSettings extends Component
{
    use WithFileUploads;

    public string $first_name = '';

    public string $last_name = '';

    public string $phone = '';

    /** @var mixed */
    public $avatar;

    public string $current_password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    public bool $notify_application_status = true;

    public bool $notify_landlord_message = true;

    public function mount(): void
    {
        $user = $this->student();
        $this->first_name = $user->first_name ?? '';
        $this->last_name = $user->last_name ?? '';
        $this->phone = $user->phone ?? '';
        $prefs = $user->preferences ?? [];
        $this->notify_application_status = (bool) ($prefs['email_application_status'] ?? true);
        $this->notify_landlord_message = (bool) ($prefs['email_landlord_message'] ?? true);
    }

    protected function student(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Student'), 403);

        return $user;
    }

    protected function toastSuccess(string $message): void
    {
        $options = json_encode([
            'toast' => true,
            'position' => 'top-end',
            'icon' => 'success',
            'title' => $message,
            'showConfirmButton' => false,
            'timer' => 4500,
            'timerProgressBar' => true,
            'heightAuto' => false,
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $this->js(sprintf(
            'if (typeof Swal !== "undefined") { Swal.fire(%s); }',
            $options
        ));
    }

    public function updatedAvatar(): void
    {
        $this->validateOnly('avatar', [
            'avatar' => ['nullable', 'image', 'max:5120'],
        ]);
    }

    public function updateProfile(): void
    {
        $user = $this->student();

        $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'image', 'max:5120'],
        ]);

        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->phone = $this->phone !== '' ? $this->phone : null;
        $user->save();

        if ($this->avatar) {
            $user->clearMediaCollection('avatar');
            $user->addMedia($this->avatar->getRealPath())
                ->usingFileName($this->avatar->getClientOriginalName())
                ->toMediaCollection('avatar');
            $this->avatar = null;
        }

        $this->toastSuccess(__('Profile updated successfully.'));
    }

    public function updatePassword(): void
    {
        $user = $this->student();

        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'confirmed', PasswordRule::min(8)],
        ]);

        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->toastSuccess(__('Password updated successfully.'));
    }

    public function savePreferences(): void
    {
        $user = $this->student();

        $this->validate([
            'notify_application_status' => ['boolean'],
            'notify_landlord_message' => ['boolean'],
        ]);

        $prefs = $user->preferences ?? [];
        $prefs['email_application_status'] = $this->notify_application_status;
        $prefs['email_landlord_message'] = $this->notify_landlord_message;
        $user->preferences = $prefs;
        $user->save();

        $this->toastSuccess(__('Preferences saved.'));
    }

    public function render(): View
    {
        return view('livewire.student.student-settings', [
            'user' => Auth::user()->fresh(['institution', 'media']),
        ]);
    }
}
