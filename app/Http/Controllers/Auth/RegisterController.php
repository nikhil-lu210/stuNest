<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use App\Models\InstituteLocation;
use App\Models\InstituteRepresentative;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm(): View
    {
        return view('auth.register', [
            'institutes' => Institute::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate(
            $this->rulesForRole($request),
            [
                'role.in' => __('The selected account type is invalid.'),
            ]
        );

        $user = DB::transaction(function () use ($data) {
            $user = $this->createUser($data);
            $this->assignClientRole($user, $data['role']);
            if ($data['role'] === 'institute') {
                $this->provisionInstituteRegistration($user, $data);
            }
            $user = $user->fresh();

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->to($user->clientPortalHomeUrl());
    }

    /**
     * @return array<string, array<int, Password|array<int, mixed|string>|string>>
     */
    private function rulesForRole(Request $request): array
    {
        $base = [
            'role' => ['required', Rule::in(['student', 'landlord', 'institute'])],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];

        $role = $request->input('role');

        if ($role === 'student') {
            $base['student_id_number'] = ['required', 'string', 'max:255'];
            $base['institution_id'] = ['required', 'integer', 'exists:institutes,id'];
        } elseif ($role === 'landlord') {
            $base['phone'] = ['required', 'string', 'max:50'];
            $base['company_name'] = ['nullable', 'string', 'max:255'];
        } elseif ($role === 'institute') {
            $base['phone'] = ['required', 'string', 'max:50'];
            $base['institute_name'] = ['required', 'string', 'max:255'];
            $base['department'] = ['required', 'string', 'max:255'];
        }

        return $base;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createUser(array $data): User
    {
        $overrides = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'userid' => User::generateUniqueUseridRaw(),
            'account_status' => User::ACCOUNT_STATUS_UNVERIFIED,
        ];

        if ($data['role'] === 'student') {
            $overrides['role'] = User::ROLE_STUDENT;
            $overrides['student_id_number'] = $data['student_id_number'];
            $overrides['institution_id'] = (int) $data['institution_id'];
        } elseif ($data['role'] === 'landlord') {
            $overrides['role'] = User::ROLE_LANDLORD;
            $overrides['phone'] = $data['phone'];
            if (! empty($data['company_name'])) {
                $overrides['company_name'] = $data['company_name'];
            }
        } elseif ($data['role'] === 'institute') {
            $overrides['phone'] = $data['phone'];
            $overrides['role'] = null;
        }

        return User::query()->create($overrides);
    }

    private function assignClientRole(User $user, string $roleKey): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $clientRole = match ($roleKey) {
            'student' => Role::findByName('Student', 'student'),
            'landlord' => Role::findByName('Landlord', 'landlord'),
            'institute' => Role::findByName('Institute Representative', 'institute'),
        };

        $user->assignRole($clientRole);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function provisionInstituteRegistration(User $user, array $data): void
    {
        do {
            $emailCode = 'pending-'.strtolower(uniqid('', true));
        } while (Institute::withTrashed()->where('email_code', Institute::normalizeEmailCode($emailCode))->exists());

        $institute = Institute::query()->create([
            'name' => $data['institute_name'],
            'email_code' => $emailCode,
        ]);

        $location = InstituteLocation::query()->create([
            'institute_id' => $institute->id,
            'name' => 'Main',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        InstituteRepresentative::query()->create([
            'institute_id' => $institute->id,
            'institute_location_id' => $location->id,
            'user_id' => $user->id,
        ]);

        $prefs = is_array($user->preferences) ? $user->preferences : (array) ($user->preferences ?? []);
        $prefs['institute_representative_department'] = $data['department'];

        $user->forceFill([
            'institution_id' => $institute->id,
            'institute_location_id' => $location->id,
            'preferences' => $prefs,
        ])->save();
    }
}
