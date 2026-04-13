<?php

namespace App\Http\Controllers\Administration\UserManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\UserManagement\StoreStudentRequest;
use App\Http\Requests\Administration\UserManagement\UpdateStudentRequest;
use App\Mail\WelcomeUserMail;
use App\Models\Institute;
use App\Support\StudentCountryList;
use App\Models\InstituteLocation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class StudentController extends Controller
{
    public function create(): View
    {
        $countries = StudentCountryList::all();
        $universities = Institute::query()->orderBy('name')->get();

        $branchOptions = collect();
        $oldUniversityId = old('university_id');
        if ($oldUniversityId !== null && $oldUniversityId !== '') {
            $branchOptions = InstituteLocation::query()
                ->where('institute_id', (int) $oldUniversityId)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        $initialEmailSuffix = '@—';
        if ($oldUniversityId !== null && $oldUniversityId !== '') {
            $match = $universities->firstWhere('id', (int) $oldUniversityId);
            if ($match) {
                $initialEmailSuffix = $match->email_code;
            }
        }

        return view('administration.user-management.student.create', [
            'countries' => $countries,
            'universities' => $universities,
            'branchOptions' => $branchOptions,
            'initialEmailSuffix' => $initialEmailSuffix,
        ]);
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);
        $this->assertStudent($user);

        $user->load(['institution', 'instituteLocation']);

        return view('administration.user-management.student.profile', compact('user'));
    }

    public function showApplications(User $user): View
    {
        $this->authorize('view', $user);
        $this->assertStudent($user);

        $user->load(['institution', 'instituteLocation']);

        return view('administration.user-management.student.applications', compact('user'));
    }

    public function showFavorites(User $user): View
    {
        $this->authorize('view', $user);
        $this->assertStudent($user);

        $user->load(['institution', 'instituteLocation']);

        return view('administration.user-management.student.favorites', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);
        $this->assertStudent($user);

        $countries = StudentCountryList::all();
        $universities = Institute::query()->orderBy('name')->get();

        $effectiveUniversityId = old('university_id', $user->institution_id);
        $branchOptions = InstituteLocation::query()
            ->where('institute_id', (int) $effectiveUniversityId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $initialEmailSuffix = '@—';
        $uniId = old('university_id', $user->institution_id);
        if ($uniId) {
            $match = $universities->firstWhere('id', (int) $uniId);
            if ($match) {
                $initialEmailSuffix = $match->email_code;
            }
        }

        $emailPrefix = old('email_prefix', $this->emailPrefixForUser($user));

        return view('administration.user-management.student.edit', [
            'user' => $user,
            'countries' => $countries,
            'universities' => $universities,
            'branchOptions' => $branchOptions,
            'initialEmailSuffix' => $initialEmailSuffix,
            'emailPrefix' => $emailPrefix,
        ]);
    }

    public function getBranches(Request $request): JsonResponse
    {
        $auth = $request->user();
        if (! $auth?->can('User Create') && ! $auth?->can('User Update')) {
            abort(403);
        }

        $request->validate([
            'university_id' => ['required', 'integer', 'exists:institutes,id'],
        ]);

        $locations = InstituteLocation::query()
            ->where('institute_id', $request->integer('university_id'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'data' => $locations->map(fn (InstituteLocation $loc) => [
                'id' => $loc->id,
                'name' => $loc->name,
            ])->values(),
        ]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $institute = Institute::query()->findOrFail((int) $validated['university_id']);

        $parts = preg_split('/\s+/', trim($validated['student_name']), 2, PREG_SPLIT_NO_EMPTY);
        $firstName = $parts[0] ?? '';
        $lastName = $parts[1] ?? $firstName;

        $plainPassword = $validated['password'];

        $user = DB::transaction(function () use ($validated, $firstName, $lastName, $institute) {
            $user = User::create([
                'userid' => $this->generateUniqueUserid(),
                'first_name' => $firstName,
                'middle_name' => null,
                'last_name' => $lastName,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'whatsapp' => ! empty($validated['whatsapp']) ? $validated['whatsapp'] : null,
                'role' => User::ROLE_STUDENT,
                'account_status' => User::ACCOUNT_STATUS_UNVERIFIED,
                'institution_id' => $institute->id,
                'country_code' => strtoupper($validated['country_code']),
                'institute_location_id' => (int) $validated['institute_location_id'],
                'student_id_number' => null,
                'course_level' => null,
                'graduation_year' => null,
                'company_name' => null,
                'billing_address' => null,
                'agency_name' => null,
                'license_number' => null,
                'office_address' => null,
                'job_title' => null,
                'dob' => null,
            ]);

            $role = Role::findByName('Student', 'student');
            $user->assignRole($role);

            return $user;
        });

        Mail::to($user->email)->queue(new WelcomeUserMail($user, $plainPassword));

        return redirect()
            ->route('administration.students.index')
            ->with('success', __('Student account created. A welcome email with sign-in details has been queued.'));
    }

    public function update(UpdateStudentRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);
        $this->assertStudent($user);

        $validated = $request->validated();
        $institute = Institute::query()->findOrFail((int) $validated['university_id']);

        $parts = preg_split('/\s+/', trim($validated['student_name']), 2, PREG_SPLIT_NO_EMPTY);
        $firstName = $parts[0] ?? '';
        $lastName = $parts[1] ?? $firstName;

        $user->fill([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'whatsapp' => ! empty($validated['whatsapp']) ? $validated['whatsapp'] : null,
            'institution_id' => $institute->id,
            'country_code' => strtoupper($validated['country_code']),
            'institute_location_id' => (int) $validated['institute_location_id'],
            'account_status' => $validated['account_status'],
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('administration.students.index')
            ->with('success', __('Student account updated.'));
    }

    protected function assertStudent(User $user): void
    {
        if (! $user->hasRole('Student')) {
            abort(404);
        }
    }

    protected function emailPrefixForUser(User $user): string
    {
        if (! $user->institution_id || ! $user->email) {
            return '';
        }

        $institute = Institute::query()->find($user->institution_id);
        if (! $institute) {
            return '';
        }

        $suffix = Institute::normalizeEmailCode($institute->email_code);
        if ($suffix === '') {
            return '';
        }

        $email = $user->email;
        $len = strlen($suffix);
        if (strlen($email) < $len) {
            return '';
        }

        if (strtolower(substr($email, -$len)) !== strtolower($suffix)) {
            return '';
        }

        return substr($email, 0, strlen($email) - $len);
    }

    protected function generateUniqueUserid(): string
    {
        do {
            $raw = (string) random_int(100000, 999999);
        } while (User::withoutGlobalScopes()->where('userid', 'UID'.$raw)->exists());

        return $raw;
    }
}
