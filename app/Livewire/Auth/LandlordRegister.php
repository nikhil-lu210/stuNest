<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

#[Layout('layouts.landlord-auth')]
class LandlordRegister extends Component
{
    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $phone = '';

    public string $company_name = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'min:8', 'max:32'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $u = User::query()->create([
                'userid' => User::generateUniqueUseridRaw(),
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'company_name' => filled(trim($validated['company_name'] ?? '')) ? trim($validated['company_name']) : null,
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_LANDLORD,
                'account_status' => User::ACCOUNT_STATUS_ACTIVE,
                'is_profile_complete' => true,
            ]);

            app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
            $u->assignRole(Role::findByName('Landlord', 'landlord'));

            return $u;
        });

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        $this->redirect(route('client.landlord.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.landlord-register');
    }
}
