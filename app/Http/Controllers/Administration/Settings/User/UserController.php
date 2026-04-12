<?php

namespace App\Http\Controllers\Administration\Settings\User;

use Hash;
use Exception;
use App\Models\User;
use App\Support\SystemRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\Settings\User\UserStoreRequest;
use App\Http\Requests\Administration\Settings\User\UserUpdateRequest;
use App\Notifications\Administration\NewUserRegistrationNotification;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query()->with('roles')->orderBy('first_name')->orderBy('last_name');

        if ($request->filled('filter_role')) {
            if ($request->filter_role === 'Admins') {
                $adminRoleNames = ['Super Admin', 'Admin'];
                if (SystemRoles::viewerIsDeveloper($request->user())) {
                    $adminRoleNames[] = 'Developer';
                }
                $query->whereHas('roles', function ($q) use ($adminRoleNames) {
                    $q->whereIn('name', $adminRoleNames);
                });
            } else {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('name', $request->filter_role);
                });
            }
        }

        $users = $query->get();

        return view('administration.user-management.directory-index', [
            'pageTitleMeta' => __('User Management'),
            'pageHeading' => __('All Users'),
            'breadcrumbParent' => __('User Management'),
            'breadcrumbCurrent' => __('All Users'),
            'cardTitle' => __('All Users'),
            'users' => $users,
            'createRoute' => route('administration.users.create'),
            'createLabel' => __('Create administration user'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('administration.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        // dd($request->all());
        $user = NULL;
        try {
            DB::transaction(function() use ($request, &$user) {
                $user = User::create([
                    'userid' => $request->userid,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                
                // Upload and associate the avatar with the user
                if ($request->hasFile('avatar')) {
                    $user->addMedia($request->avatar)->toMediaCollection('avatar');
                }

                $role = Role::findOrFail($request->role_id);
                $user->assignRole($role);

                if ($role->name === SystemRoles::DEVELOPER_NAME) {
                    $admins = User::query()
                        ->whereHas('roles', function ($query) {
                            $query->where('name', SystemRoles::DEVELOPER_NAME)
                                ->where('guard_name', SystemRoles::WEB_GUARD);
                        })
                        ->get();
                } else {
                    $admins = User::query()
                        ->where(function ($q) {
                            $q->whereHas('roles', function ($query) {
                                $query->where('name', SystemRoles::SUPER_ADMIN_NAME)
                                    ->where('guard_name', SystemRoles::WEB_GUARD);
                            })->orWhereHas('roles', function ($query) {
                                $query->where('name', 'Admin')
                                    ->where('guard_name', SystemRoles::WEB_GUARD);
                            })->orWhereHas('roles', function ($query) {
                                $query->where('name', SystemRoles::DEVELOPER_NAME)
                                    ->where('guard_name', SystemRoles::WEB_GUARD);
                            });
                        })
                        ->get();
                }

                foreach ($admins as $admin) {
                    $admin->notify(new NewUserRegistrationNotification($user));
                }
            }, 5);

            toast('A New User Has Been Created.','success');
            return redirect()->route('administration.settings.user.show.profile', ['user' => $user]);
        } catch (Exception $e) {
            dd($e);
            alert('Opps! Error.', $e->getMessage(), 'error');
            return redirect()->back()->withInput();
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function showProfile(User $user)
    {
        $this->authorize('view', $user);

        return view('administration.settings.user.includes.profile', compact(['user']));
    }

    /**
     * Display the specified resource.
     */
    public function showAttendance(User $user)
    {
        $this->authorize('view', $user);

        return view('administration.settings.user.includes.attendance', compact(['user']));
    }

    /**
     * Display the specified resource.
     */
    public function showBreak(User $user)
    {
        $this->authorize('view', $user);

        return view('administration.settings.user.includes.break', compact(['user']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = SystemRoles::administrationRolesQuery(auth()->user())->get();

        return view('administration.settings.user.edit', compact(['roles', 'user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $this->authorize('update', $user);

        try {
            DB::transaction(function() use ($request, $user) {
                $user->update([
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                ]);

                // Upload and associate the avatar with the user
                if ($request->hasFile('avatar')) {
                    $user->addMedia($request->avatar)->toMediaCollection('avatar');
                }

                // Sync the user's role
                $role = Role::findOrFail($request->role_id);
                $user->syncRoles([$role]);
            }, 5);

            toast('User information has been updated.', 'success');
            return redirect()->route('administration.settings.user.show.profile', ['user' => $user]);
        } catch (Exception $e) {
            alert('Oops! Error.', $e->getMessage(), 'error');
            return redirect()->back()->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        try {
            $user->delete();
            toast(__('User has been removed.'), 'success');
        } catch (\RuntimeException $e) {
            alert(__('Cannot delete user'), $e->getMessage(), 'error');
        }

        return redirect()->back();
    }
}
