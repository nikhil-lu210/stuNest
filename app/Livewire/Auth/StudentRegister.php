<?php

namespace App\Livewire\Auth;

use App\Mail\VerifyRegistrationOtp;
use App\Models\Institute;
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

    /** Picker open state (institute list is not stored in public props — see computed properties). */
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

    public string $generatedOtp = '';

    public string $enteredOtp = '';

    /**
     * Unique institutes for the UI — rebuilt each request, never sent to the browser as serialized state.
     *
     * @return list<array{id: int, name: string, email_code: string}>
     */
    #[Computed]
    public function instituteOptions(): array
    {
        return $this->loadDeduplicatedInstitutes();
    }

    /**
     * @return list<array{id: int, name: string, email_code: string}>
     */
    #[Computed]
    public function filteredInstituteOptions(): array
    {
        $needle = mb_strtolower(trim($this->instituteSearch));
        $rows = $this->instituteOptions;
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

        $email = $this->buildEmail();
        if (User::query()->where('email', $email)->exists()) {
            $this->addError('email_prefix', __('This email is already registered.'));

            return;
        }

        $this->generatedOtp = (string) random_int(100000, 999999);
        Mail::to($email)->send(new VerifyRegistrationOtp($this->generatedOtp));
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

    public function verifyAndRegister(): void
    {
        $this->validate([
            'enteredOtp' => ['required', 'string', 'size:6', 'regex:/^\d+$/'],
        ]);

        if ($this->enteredOtp !== $this->generatedOtp) {
            $this->addError('enteredOtp', __('The code is incorrect. Please try again.'));

            return;
        }

        $email = $this->buildEmail();

        $user = DB::transaction(function () use ($email) {
            $u = User::query()->create([
                'userid' => User::generateUniqueUseridRaw(),
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $email,
                'password' => Hash::make($this->password),
                'email_verified_at' => now(),
                'account_status' => User::ACCOUNT_STATUS_UNVERIFIED,
                'role' => User::ROLE_STUDENT,
                'institution_id' => $this->institute_id,
                'is_profile_complete' => false,
            ]);

            app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
            $u->assignRole(Role::findByName('Student', 'student'));

            return $u;
        });

        Auth::login($user, remember: true);

        $this->redirect(route('student.profile.edit'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.student-register');
    }
}
