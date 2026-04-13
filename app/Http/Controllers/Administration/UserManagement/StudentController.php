<?php

namespace App\Http\Controllers\Administration\UserManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\UserManagement\StoreStudentRequest;
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

    public function getBranches(Request $request): JsonResponse
    {
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

    protected function generateUniqueUserid(): string
    {
        do {
            $raw = (string) random_int(100000, 999999);
        } while (User::withoutGlobalScopes()->where('userid', 'UID'.$raw)->exists());

        return $raw;
    }
}
