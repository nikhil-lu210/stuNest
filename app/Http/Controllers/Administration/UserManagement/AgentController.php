<?php

namespace App\Http\Controllers\Administration\UserManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\UserManagement\StoreAgentRequest;
use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class AgentController extends Controller
{
    public function create(): View
    {
        return view('administration.user-management.agent.create');
    }

    public function store(StoreAgentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $plainPassword = $validated['password'];

        $user = DB::transaction(function () use ($validated, $plainPassword) {
            $user = User::create([
                'userid' => $this->generateUniqueUserid(),
                'first_name' => $validated['first_name'],
                'middle_name' => null,
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($plainPassword),
                'phone' => $validated['phone'],
                'whatsapp' => ! empty($validated['whatsapp']) ? $validated['whatsapp'] : null,
                'role' => User::ROLE_AGENT,
                'account_status' => $validated['account_status'],
                'agency_name' => $validated['agency_name'],
                'license_number' => ! empty($validated['license_number']) ? $validated['license_number'] : null,
                'office_address' => ! empty($validated['office_address']) ? $validated['office_address'] : null,
                'institution_id' => null,
                'country_code' => null,
                'institute_location_id' => null,
                'student_id_number' => null,
                'course_level' => null,
                'graduation_year' => null,
                'company_name' => null,
                'billing_address' => null,
                'job_title' => null,
                'dob' => null,
            ]);

            $role = Role::findByName('Agent', 'agent');
            $user->assignRole($role);

            return $user;
        });

        Mail::to($user->email)->queue(new WelcomeUserMail($user, $plainPassword));

        return redirect()
            ->route('administration.agents.index')
            ->with('success', __('Agent account created. A welcome email with sign-in details has been queued.'));
    }

    protected function generateUniqueUserid(): string
    {
        do {
            $raw = (string) random_int(100000, 999999);
        } while (User::withoutGlobalScopes()->where('userid', 'UID'.$raw)->exists());

        return $raw;
    }
}
