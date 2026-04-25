<?php

namespace App\Livewire\Auth;

use App\Mail\VerifyRegistrationOtp;
use App\Models\Institute;
use App\Models\TemporaryUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

#[Layout('layouts.student-auth')]
class StudentRegister extends Component
{
    public int $step = 1;

    /**
     * Deduplicated institute rows for the picker (loaded in mount, not the OTP/registration source of truth).
     *
     * @var list<array{id: int, name: string, email_code: string}>
     */
    public array $institutes = [];

    /** Picker open state */
    public bool $institutePickerOpen = false;

    public string $instituteSearch = '';

    #[Validate('nullable|integer|exists:institutes,id')]
    public ?int $institute_id = null;

    public string $institute_domain = '';

    public string $email_prefix = '';

    public string $first_name = '';

    public string $last_name = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $enteredOtp = '';

    public string $otp0 = '';

    public string $otp1 = '';

    public string $otp2 = '';

    public string $otp3 = '';

    public string $otp4 = '';

    public string $otp5 = '';

    public function mount(): void
    {
        $this->institutes = $this->loadDeduplicatedInstitutes();
    }

    /**
     * @return list<array{id: int, name: string, email_code: string}>
     */
    #[Computed]
    public function filteredInstituteOptions(): array
    {
        $needle = mb_strtolower(trim($this->instituteSearch));
        $rows = $this->institutes;
        if ($needle === '') {
            return $rows;
        }

        return array_values(array_filter(
            $rows,
            static fn (array $row): bool => str_contains(mb_strtolower($row['name']), $needle)
        ));
    }

    public function selectInstitute(int $id): void
    {
        $this->institute_id = $id;
        $this->updatedInstituteId($id);
        $this->institutePickerOpen = false;
        $this->instituteSearch = '';
    }

    public function toggleInstitutePicker(): void
    {
        $this->institutePickerOpen = ! $this->institutePickerOpen;
    }

    /**
     * @return list<array{id: int, name: string, email_code: string}>
     */
    private function loadDeduplicatedInstitutes(): array
    {
        $rows = Institute::query()
            ->orderBy('id')
            ->get(['id', 'name', 'email_code'])
            ->unique(fn (Institute $row): string => $this->normalizeInstituteNameKey((string) $row->name))
            ->values()
            ->map(function (Institute $row) {
                return [
                    'id' => (int) $row->id,
                    'name' => (string) Str::of($row->name)->trim()->squish(),
                    'email_code' => (string) $row->email_code,
                ];
            })
            ->sortBy(fn (array $row) => mb_strtolower($row['name']))
            ->values()
            // Prefer lowest id for each name key (also catches duplicate ids from bad data)
            ->groupBy(fn (array $row): string => $this->normalizeInstituteNameKey($row['name']))
            ->map(fn ($group) => $group->sortBy('id')->first())
            ->values()
            ->sortBy(fn (array $row) => mb_strtolower($row['name']))
            ->values()
            ->all();

        // Defensive: collapse any duplicate id or normalized name that slipped through.
        $byId = [];
        foreach ($rows as $row) {
            if (! isset($byId[$row['id']])) {
                $byId[$row['id']] = $row;
            }
        }
        $rows = array_values($byId);
        $byNameKey = [];
        foreach ($rows as $row) {
            $k = $this->normalizeInstituteNameKey($row['name']);
            if (! isset($byNameKey[$k])) {
                $byNameKey[$k] = $row;
            }
        }

        return array_values($byNameKey);
    }

    /**
     * Stable key for deduplication: SQL "GROUP BY name" misses rows that look the same in the UI
     * but differ by case, padding, or unicode whitespace.
     */
    private function normalizeInstituteNameKey(string $name): string
    {
        $s = (string) Str::of($name)->trim()->squish();
        if (class_exists(\Normalizer::class)) {
            $n = \Normalizer::normalize($s, \Normalizer::FORM_C);
            if (is_string($n) && $n !== '') {
                $s = $n;
            }
        }
        // Fold Latin lookalikes for deduplication (display `name` stays unchanged in DB)
        $folded = (string) Str::of($s)->lower()->ascii();
        $s = $folded !== '' ? $folded : mb_strtolower($s, 'UTF-8');
        $s = preg_replace('/\s+/u', ' ', $s) ?? $s;
        $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $s) ?? $s;

        return trim($s);
    }

    public function updatedInstituteId(mixed $id): void
    {
        if (empty($id)) {
            $this->institute_domain = '';
            $this->institute_id = null;

            return;
        }
        $institute = Institute::query()->find((int) $id);
        if ($institute) {
            $this->institute_id = (int) $id;
            $this->institute_domain = ltrim((string) $institute->email_code, '@');
        } else {
            $this->institute_domain = '';
        }
    }

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
        if ($this->institute_id === null) {
            $this->addError('institute_id', __('Please select an institute.'));

            return;
        }
        if ($this->institute_domain === '') {
            $this->addError('institute_id', __('The selected institute has no email domain.'));

            return;
        }

        $fullEmail = $this->buildEmail();
        if (User::query()->where('email', $fullEmail)->exists()) {
            $this->addError('email_prefix', __('This email is already registered.'));

            return;
        }

        $otp = rand(100_000, 999_999);

        TemporaryUser::query()->updateOrCreate(
            ['email' => $fullEmail],
            [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'password' => Hash::make($this->password),
                'role' => User::ROLE_STUDENT,
                'institute_id' => $this->institute_id,
                'otp' => (string) $otp,
                'expires_at' => now()->addMinutes(15),
            ],
        );

        Mail::to($fullEmail)->send(new VerifyRegistrationOtp((string) $otp));
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
            'password' => ['required', 'string', 'min:8', 'same:password_confirmation'],
            'password_confirmation' => ['required', 'string', 'min:8'],
            'email_prefix' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9._-]+$/'],
            'institute_id' => ['required', 'integer', 'exists:institutes,id'],
        ];
    }

    public function buildEmail(): string
    {
        return $this->email_prefix.'@'.$this->institute_domain;
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

    public function verifyAndRegister(): void
    {
        $this->syncEnteredOtp();

        $this->validate([
            'enteredOtp' => ['required', 'string', 'size:6', 'regex:/^\d+$/'],
        ]);

        // Must match the email stored on TemporaryUser in sendOtp() (same as buildEmail()).
        $fullEmail = $this->email_prefix.'@'.$this->institute_domain;
        if ($this->email_prefix === '' || $this->institute_domain === '' || $this->institute_id === null) {
            $this->addError('enteredOtp', __('Please return to step 1 and resend a verification code.'));

            return;
        }

        $temporary = TemporaryUser::query()
            ->where('email', $fullEmail)
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

        $user = DB::transaction(function () use ($temporary) {
            // University inbox OTP proves control of the official email; no separate institute rep approval.
            $u = User::query()->create([
                'userid' => User::generateUniqueUseridRaw(),
                'first_name' => $temporary->first_name,
                'last_name' => $temporary->last_name,
                'email' => $temporary->email,
                'password' => $temporary->password,
                'account_status' => User::ACCOUNT_STATUS_ACTIVE,
                'role' => User::ROLE_STUDENT,
                'institution_id' => (int) $temporary->institute_id,
                'is_profile_complete' => false,
            ]);
            $u->email_verified_at = now();
            $u->save();

            app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
            $u->assignRole(Role::findByName('Student', 'student'));

            $temporary->delete();

            return $u;
        });

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        // Full navigation (not Livewire SPA) so the new session is applied before `auth` middleware runs.
        $this->redirect(route('client.student.settings'), navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.student-register');
    }
}
