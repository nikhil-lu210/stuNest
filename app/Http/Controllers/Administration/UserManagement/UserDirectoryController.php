<?php

namespace App\Http\Controllers\Administration\UserManagement;

use App\Http\Controllers\Administration\Settings\User\UserController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * User directory routes: reuses settings UserController where full UI already exists.
 */
class UserDirectoryController extends Controller
{
    protected function page(string $title): View
    {
        return view('administration.user-management.placeholder', ['title' => $title]);
    }

    protected function directoryListing(
        string $pageTitleMeta,
        string $pageHeading,
        string $breadcrumbParent,
        string $breadcrumbCurrent,
        string $cardTitle,
        Collection $users,
        ?string $createRouteName = null,
        ?string $createLabel = null,
    ): View {
        return view('administration.user-management.directory-index', [
            'pageTitleMeta' => $pageTitleMeta,
            'pageHeading' => $pageHeading,
            'breadcrumbParent' => $breadcrumbParent,
            'breadcrumbCurrent' => $breadcrumbCurrent,
            'cardTitle' => $cardTitle,
            'users' => $users,
            'createRoute' => $createRouteName ? route($createRouteName) : null,
            'createLabel' => $createLabel,
        ]);
    }

    protected function usersWithRole(string $roleName, ?string $accountStatus = null): Collection
    {
        $query = User::query()->with('roles')->orderBy('first_name')->orderBy('last_name')->whereRoleName($roleName);

        if ($accountStatus !== null) {
            $query->status($accountStatus);
        }

        return $query->get();
    }

    public function allUsers(Request $request): View
    {
        return app(UserController::class)->index($request);
    }

    public function landlordsIndex(): View
    {
        return $this->directoryListing(
            __('Landlord'),
            __('All Landlords'),
            __('Landlord'),
            __('All Landlords'),
            __('All Landlords'),
            $this->usersWithRole('Landlord'),
            'administration.landlords.create',
            __('Create New Landlord'),
        );
    }

    public function landlordsPending(): View
    {
        return $this->directoryListing(
            __('Landlord'),
            __('Pending Landlords'),
            __('Landlord'),
            __('Pending Landlords'),
            __('Pending Landlords'),
            $this->usersWithRole('Landlord', User::ACCOUNT_STATUS_PENDING),
            'administration.landlords.create',
            __('Create New Landlord'),
        );
    }

    public function landlordsRejected(): View
    {
        return $this->directoryListing(
            __('Landlord'),
            __('Rejected Landlords'),
            __('Landlord'),
            __('Rejected Landlords'),
            __('Rejected Landlords'),
            $this->usersWithRole('Landlord', User::ACCOUNT_STATUS_REJECTED),
            'administration.landlords.create',
            __('Create New Landlord'),
        );
    }

    public function agentsIndex(): View
    {
        return $this->directoryListing(
            __('Agent'),
            __('All Agents'),
            __('Agent'),
            __('All Agents'),
            __('All Agents'),
            $this->usersWithRole('Agent'),
            'administration.agents.create',
            __('Create New Agent'),
        );
    }

    public function agentsPending(): View
    {
        return $this->directoryListing(
            __('Agent'),
            __('Pending Agents'),
            __('Agent'),
            __('Pending Agents'),
            __('Pending Agents'),
            $this->usersWithRole('Agent', User::ACCOUNT_STATUS_PENDING),
            'administration.agents.create',
            __('Create New Agent'),
        );
    }

    public function agentsRejected(): View
    {
        return $this->directoryListing(
            __('Agent'),
            __('Rejected Agents'),
            __('Agent'),
            __('Rejected Agents'),
            __('Rejected Agents'),
            $this->usersWithRole('Agent', User::ACCOUNT_STATUS_REJECTED),
            'administration.agents.create',
            __('Create New Agent'),
        );
    }

    public function agentsCreate(): View
    {
        return $this->page(__('Create New Agent'));
    }

    public function studentsIndex(): View
    {
        return $this->directoryListing(
            __('Student'),
            __('All Students'),
            __('Student'),
            __('All Students'),
            __('All Students'),
            $this->usersWithRole('Student'),
            'administration.students.create',
            __('Create New Student'),
        );
    }

    public function studentsUnverified(): View
    {
        return $this->directoryListing(
            __('Student'),
            __('Unverified Students'),
            __('Student'),
            __('Unverified Students'),
            __('Unverified Students'),
            $this->usersWithRole('Student', User::ACCOUNT_STATUS_UNVERIFIED),
            'administration.students.create',
            __('Create New Student'),
        );
    }
}
