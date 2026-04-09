<?php

namespace App\Http\Controllers\Administration\Settings\Institute;

use Hash;
use Exception;
use App\Models\User;
use App\Models\Institute;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\InstituteRepresentative;
use App\Http\Requests\Administration\Settings\Institute\InstituteRepresentativeStoreRequest;

class InstituteRepresentativeController extends Controller
{
    public function create(Institute $institute)
    {
        $institute->load([
            'locations' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
        ]);

        return view('administration.settings.institute.representatives.create', compact('institute'));
    }

    public function store(InstituteRepresentativeStoreRequest $request, Institute $institute)
    {
        $user = null;

        try {
            DB::transaction(function () use ($request, $institute, &$user) {
                $fullName = $request->first_name.' '.$request->middle_name.' '.$request->last_name;
                $fullName = preg_replace('/\s+/', ' ', trim($fullName));

                $user = User::create([
                    'userid' => $request->userid,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'name' => $fullName,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                if ($request->hasFile('avatar')) {
                    $user->addMedia($request->avatar)->toMediaCollection('avatar');
                }

                $role = Role::findByName('Institute Representative');
                $user->assignRole($role);

                InstituteRepresentative::create([
                    'institute_id' => $institute->id,
                    'institute_location_id' => $request->institute_location_id,
                    'user_id' => $user->id,
                ]);
            }, 5);

            toast('Institute representative has been created.', 'success');

            return redirect()->route('administration.settings.institute.show', $institute);
        } catch (Exception $e) {
            alert('Error.', $e->getMessage(), 'error');

            return redirect()->back()->withInput();
        }
    }

    public function destroy(Institute $institute, InstituteRepresentative $representative)
    {
        if ($representative->institute_id !== $institute->id) {
            abort(404);
        }

        try {
            DB::transaction(function () use ($representative) {
                $user = $representative->user;
                $representative->delete();
                if ($user) {
                    $user->removeRole('Institute Representative');
                }
            }, 5);

            toast('Representative assignment has been removed.', 'success');
        } catch (Exception $e) {
            alert('Error.', $e->getMessage(), 'error');
        }

        return redirect()->route('administration.settings.institute.show', $institute);
    }
}
