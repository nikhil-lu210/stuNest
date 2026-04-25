<?php

namespace App\Livewire\Auth;

use App\Mail\VerifyRegistrationOtp;
use App\Models\Institute;
use App\Models\InstituteLocation;
use App\Models\InstituteRepresentative;
use App\Models\TemporaryUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Institute representative self-registration: creates {@see Institute} + default location + {@see InstituteRepresentative} after email OTP.
 */
#[Layout('layouts.institute-auth')]
class InstituteRegister extends Component
{
    private const TEMP_ROLE = 'institute';

    public int $step = 1;

    public string $institute_name = '';

    public string $institute_email_code = '';

    public string $department = '';

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $phone = '';

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

        $nameNorm = (string) Str::of($this->institute_name)->trim()->squish();
        $code = Institute::normalizeEmailCode(trim($this->institute_email_code));
        $domain = ltrim($code, '@');

        if ($domain === '') {
            $this->addError('institute_email_code', __('Please enter a valid email domain.'));

            return;
        }

        if (! $this->emailDomainMatches($email, $domain)) {
            $this->addError('email', __('Your work email must use the official institution domain (e.g. :example).', [
                'example' => 'name@'.strtolower($domain),
            ]));

            return;
        }

        if (Institute::query()->where('name', $nameNorm)->exists()) {
            $this->addError('institute_name', __('An institution with this name is already registered.'));

            return;
        }

        if (Institute::query()->where('email_code', $code)->exists()) {
            $this->addError('institute_email_code', __('This email domain is already used by an institution.'));

            return;
        }

        $otp = random_int(100_000, 999_999);

        TemporaryUser::query()->updateOrCreate(
            ['email' => $email],
            [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone' => $this->phone,
                'company_name' => null,
                'institute_name' => $nameNorm,
                'institute_email_code' => $code,
                'department' => (string) Str::of($this->department)->trim()->squish(),
                'password' => Hash::make($this->password),
                'role' => self::TEMP_ROLE,
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
            'institute_name' => ['required', 'string', 'max:255'],
            'institute_email_code' => ['required', 'string', 'max:125'],
            'department' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'phone' => ['required', 'string', 'min:8', 'max:32'],
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

        if ($temporary->role !== self::TEMP_ROLE) {
            $this->addError('enteredOtp', __('This code is not valid for institute registration.'));

            return;
        }

        if (blank($temporary->institute_name) || blank($temporary->institute_email_code) || blank($temporary->department)) {
            $this->addError('enteredOtp', __('Your registration data is incomplete. Please start again from step 1.'));

            return;
        }

        if (Institute::query()->where('name', $temporary->institute_name)->exists()) {
            $this->addError('enteredOtp', __('This institution is already registered.'));

            return;
        }

        if (Institute::query()->where('email_code', $temporary->institute_email_code)->exists()) {
            $this->addError('enteredOtp', __('This email domain is already in use.'));

            return;
        }

        $user = DB::transaction(function () use ($temporary) {
            $institute = Institute::query()->create([
                'name' => $temporary->institute_name,
                'email_code' => $temporary->institute_email_code,
            ]);

            $location = InstituteLocation::query()->create([
                'institute_id' => $institute->id,
                'name' => 'Main',
                'is_primary' => true,
                'sort_order' => 0,
            ]);

            $prefs = [
                'institute_representative_department' => $temporary->department,
            ];

            $u = User::query()->create([
                'userid' => User::generateUniqueUseridRaw(),
                'first_name' => $temporary->first_name,
                'last_name' => $temporary->last_name,
                'email' => $temporary->email,
                'phone' => $temporary->phone,
                'password' => $temporary->password,
                'account_status' => User::ACCOUNT_STATUS_PENDING,
                'role' => null,
                'institution_id' => $institute->id,
                'institute_location_id' => $location->id,
                'preferences' => $prefs,
                'is_profile_complete' => false,
            ]);
            $u->email_verified_at = now();
            $u->save();

            InstituteRepresentative::query()->create([
                'institute_id' => $institute->id,
                'institute_location_id' => $location->id,
                'user_id' => $u->id,
            ]);

            app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
            $u->assignRole(Role::findByName('Institute Representative', 'institute'));

            $temporary->delete();

            return $u;
        });

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        $this->redirect(route('client.institute.settings'), navigate: false);
    }

    private function emailDomainMatches(string $emailLower, string $domain): bool
    {
        $domainLower = strtolower($domain);
        $parts = explode('@', $emailLower, 2);

        return count($parts) === 2 && $parts[1] === $domainLower;
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
        return view('livewire.auth.institute-register');
    }
}
