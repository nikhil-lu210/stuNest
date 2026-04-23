<?php

namespace App\Livewire\Institute;

use App\Mail\WelcomeUserMail;
use App\Models\Institute;
use App\Models\InstituteLocation;
use App\Models\InstituteRepresentative;
use App\Models\User;
use App\Support\StudentCountryList;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class InstituteCreateStudent extends Component
{
    public string $student_name = '';

    public string $country_code = '';

    public ?int $institute_location_id = null;

    public string $email_prefix = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $phone = '';

    public string $whatsapp = '';

    public function save(): void
    {
        $authUser = Auth::user();
        abort_unless($authUser instanceof User && $authUser->hasRole('Institute Representative'), 403);

        $institute = $this->resolveInstitute();

        $validated = $this->validate([
            'student_name' => ['required', 'string', 'max:255', 'regex:/^\S+(?:\s+\S+)+$/'],
            'country_code' => ['required', 'string', 'size:2', Rule::in(StudentCountryList::codes())],
            'institute_location_id' => [
                'required',
                'integer',
                Rule::exists('institute_locations', 'id')->where('institute_id', $institute->id),
            ],
            'email_prefix' => ['required', 'string', 'max:120', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
        ], [
            'student_name.regex' => __('Enter the student’s first and last name (at least two words).'),
        ]);

        $email = strtolower(trim($validated['email_prefix'])).Institute::normalizeEmailCode($institute->email_code);

        Validator::make(
            ['email' => $email],
            ['email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')]],
        )->validate();

        $parts = preg_split('/\s+/', trim($validated['student_name']), 2, PREG_SPLIT_NO_EMPTY);
        $firstName = $parts[0] ?? '';
        $lastName = $parts[1] ?? $firstName;

        $plainPassword = $validated['password'];

        $user = DB::transaction(function () use ($validated, $firstName, $lastName, $institute, $email) {
            $user = User::create([
                'userid' => User::generateUniqueUseridRaw(),
                'first_name' => $firstName,
                'middle_name' => null,
                'last_name' => $lastName,
                'email' => $email,
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

        $this->dispatch('notify', message: __('Student account created. A welcome email with sign-in details has been queued.'), type: 'success');

        $this->redirect(route('client.institute.students.unverified'), navigate: true);
    }

    public function render(): View
    {
        $authUser = Auth::user();
        abort_unless($authUser instanceof User && $authUser->hasRole('Institute Representative'), 403);

        $institute = $this->resolveInstitute();

        $locations = InstituteLocation::query()
            ->where('institute_id', $institute->id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $countries = StudentCountryList::all();

        $emailSuffix = Institute::normalizeEmailCode($institute->email_code);

        return view('livewire.institute.institute-create-student', [
            'institute' => $institute,
            'locations' => $locations,
            'countries' => $countries,
            'emailSuffix' => $emailSuffix,
        ])->layout('layouts.institute', [
            'title' => __('Create Account'),
            'pageTitle' => __('Create Account'),
            'pageSubtitle' => $institute->name,
            'instituteOrgName' => $institute->name,
        ]);
    }

    private function resolveInstitute(): Institute
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $representation = InstituteRepresentative::query()
            ->where('user_id', $user->id)
            ->with('institute')
            ->first();

        abort_if($representation === null || ! $representation->institute instanceof Institute, 403);

        return $representation->institute;
    }
}
