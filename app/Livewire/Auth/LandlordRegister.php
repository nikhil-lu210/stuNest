<?php

namespace App\Livewire\Auth;

use App\Mail\VerifyRegistrationOtp;
use App\Models\TemporaryUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

#[Layout('layouts.landlord-auth')]
class LandlordRegister extends Component
{
    public int $step = 1;

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $phone = '';

    public string $company_name = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $enteredOtp = '';

    public string $otp0 = '';

    public string $otp1 = '';

    public string $otp2 = '';

    public string $otp3 = '';

    public string $otp4 = '';

    public string $otp5 = '';

    public function updated($name, $value): void
    {
        if (! in_array($name, ['otp0', 'otp1', 'otp2', 'otp3', 'otp4', 'otp5'], true)) {
            return;
        }

        $all = preg_replace('/\D/', '', (string) $value);
        if (strlen($all) > 1) {
            for ($i = 0; $i < 6; $i++) {
                $p = "otp{$i}";
                $this->{$p} = $all[$i] ?? '';
            }
        } else {
            $i = (int) substr($name, 3);
            $p = "otp{$i}";
            $this->{$p} = $all[0] ?? '';
        }

        $this->syncEnteredOtp();
    }

    public function sendOtp(): void
    {
        $this->validate($this->stepOneRules());

        $email = $this->normalizedEmail();
        if (User::query()->where('email', $email)->exists()) {
            $this->addError('email', __('This email is already registered.'));

            return;
        }

        $otp = random_int(100_000, 999_999);

        TemporaryUser::query()->updateOrCreate(
            ['email' => $email],
            [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone' => $this->phone,
                'company_name' => filled(trim($this->company_name)) ? trim($this->company_name) : null,
                'password' => Hash::make($this->password),
                'role' => User::ROLE_LANDLORD,
                'institute_id' => null,
                'otp' => (string) $otp,
                'expires_at' => now()->addMinutes(15),
            ],
        );

        Mail::to($email)->send(new VerifyRegistrationOtp((string) $otp));
        $this->resetOtpFields();
        $this->step = 2;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function stepOneRules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'phone' => ['required', 'string', 'min:8', 'max:32'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'same:password_confirmation'],
            'password_confirmation' => ['required', 'string', 'min:8'],
        ];
    }

    public function verifyAndRegister(): void
    {
        $this->syncEnteredOtp();

        $this->validate([
            'enteredOtp' => ['required', 'string', 'size:6', 'regex:/^\d+$/'],
        ]);

        $email = $this->normalizedEmail();

        $temporary = TemporaryUser::query()
            ->where('email', $email)
            ->where('otp', $this->enteredOtp)
            ->first();

        if (! $temporary) {
            $this->addError('enteredOtp', __('Invalid verification code.'));

            return;
        }

        if ($temporary->expires_at->isPast()) {
            $temporary->delete();
            $this->addError('enteredOtp', __('Code has expired. Please request a new one.'));

            return;
        }

        if ($temporary->role !== User::ROLE_LANDLORD) {
            $this->addError('enteredOtp', __('This code is not valid for landlord registration.'));

            return;
        }

        $user = DB::transaction(function () use ($temporary) {
            $u = User::query()->create([
                'userid' => User::generateUniqueUseridRaw(),
                'first_name' => $temporary->first_name,
                'last_name' => $temporary->last_name,
                'email' => $temporary->email,
                'phone' => $temporary->phone,
                'company_name' => $temporary->company_name,
                'password' => $temporary->password,
                'role' => User::ROLE_LANDLORD,
                'account_status' => User::ACCOUNT_STATUS_PENDING,
                'is_profile_complete' => false,
            ]);
            $u->email_verified_at = now();
            $u->save();

            app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
            $u->assignRole(Role::findByName('Landlord', 'landlord'));

            $temporary->delete();

            return $u;
        });

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        $this->redirect(route('client.landlord.settings.index'), navigate: false);
    }

    private function normalizedEmail(): string
    {
        return strtolower(trim($this->email));
    }

    private function resetOtpFields(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $p = "otp{$i}";
            $this->{$p} = '';
        }
        $this->enteredOtp = '';
    }

    private function syncEnteredOtp(): void
    {
        $this->enteredOtp = $this->otp0.$this->otp1.$this->otp2.$this->otp3.$this->otp4.$this->otp5;
    }

    public function render()
    {
        return view('livewire.auth.landlord-register');
    }
}
