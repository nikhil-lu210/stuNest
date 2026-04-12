<?php

namespace App\Livewire\Administration\User;

use App\Mail\WelcomeUserMail;
use App\Models\User;
use App\Support\SystemRoles;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

/**
 * Create Vuexy / administration staff users only (Spatie roles on the "web" guard).
 * Client users (student, landlord, agent, institute rep) use separate flows and portal guards.
 */
class CreateUser extends Component
{
    public string $first_name = '';

    public string $middle_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $phone = '';

    /** Spatie role id (web guard — Developer, Super Admin, …). */
    public $selected_admin_role = null;

    /** @var array<int, array{id: int, name: string}> */
    public array $available_roles = [];

    public function mount(): void
    {
        $this->loadAdministrationRoles();
    }

    protected function loadAdministrationRoles(): void
    {
        $this->available_roles = SystemRoles::administrationRolesQuery(auth()->user())
            ->get()
            ->map(fn (Role $role) => ['id' => $role->id, 'name' => $role->name])
            ->values()
            ->all();
    }

    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'selected_admin_role' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(function ($query) {
                    $query->where('guard_name', SystemRoles::WEB_GUARD);
                }),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $role = Role::query()->find($value);
                    if ($role && SystemRoles::isDeveloperRole($role) && ! SystemRoles::viewerIsDeveloper(auth()->user())) {
                        $fail(__('Invalid role.'));
                    }
                },
            ],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $plainPassword = Str::random(12);

        $user = DB::transaction(function () use ($plainPassword) {
            $user = User::create([
                'userid' => $this->generateUniqueUserid(),
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name !== '' ? $this->middle_name : null,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone !== '' ? $this->phone : null,
                'role' => User::ROLE_ADMIN,
                'account_status' => User::ACCOUNT_STATUS_UNVERIFIED,
                'password' => Hash::make($plainPassword),
                'dob' => null,
                'institution_id' => null,
                'student_id_number' => null,
                'course_level' => null,
                'graduation_year' => null,
                'company_name' => null,
                'billing_address' => null,
                'agency_name' => null,
                'license_number' => null,
                'office_address' => null,
                'job_title' => null,
            ]);

            $user->assignRole((int) $this->selected_admin_role);

            return $user;
        });

        Mail::to($user->email)->queue(new WelcomeUserMail($user, $plainPassword));

        session()->flash('success', __('Administration user created. A welcome email with a temporary password has been queued.'));

        $this->redirect(route('administration.users.create'));
    }

    protected function generateUniqueUserid(): string
    {
        do {
            $raw = (string) random_int(100000, 999999);
        } while (User::withoutGlobalScopes()->where('userid', 'UID'.$raw)->exists());

        return $raw;
    }

    public function render(): View
    {
        return view('livewire.administration.user.create-user');
    }
}
