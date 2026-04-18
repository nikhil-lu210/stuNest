<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('administration.dashboard.index') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('Logo/logo_black_01.png') }}" width="90%">
            </span>
            <span class="app-brand-text demo menu-text fw-bold">{{ config('app.name') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('administration.dashboard.*') ? 'active' : '' }}">
            <a href="{{ route('administration.dashboard.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboard">{{ __('Dashboard') }}</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('administration.properties.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-home-plus"></i>
                <div data-i18n="Property Listings">{{ __('Property listings') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('administration.properties.pending') ? 'active' : '' }}">
                    <a href="{{ route('administration.properties.pending') }}" class="menu-link">{{ __('Pending review') }}</a>
                </li>
                <li class="menu-item {{ request()->routeIs('administration.properties.live') ? 'active' : '' }}">
                    <a href="{{ route('administration.properties.live') }}" class="menu-link">{{ __('Live properties') }}</a>
                </li>
                <li class="menu-item {{ request()->routeIs('administration.properties.rented') ? 'active' : '' }}">
                    <a href="{{ route('administration.properties.rented') }}" class="menu-link">{{ __('Rented') }}</a>
                </li>
                <li class="menu-item {{ request()->routeIs('administration.properties.drafts_archived') ? 'active' : '' }}">
                    <a href="{{ route('administration.properties.drafts_archived') }}" class="menu-link">{{ __('Drafts / archived') }}</a>
                </li>
                @can('create', \App\Models\Property\Property::class)
                    <li class="menu-item {{ request()->routeIs('administration.properties.create') ? 'active' : '' }}">
                        <a href="{{ route('administration.properties.create') }}" target="_blank" class="menu-link">{{ __('List your property') }}</a>
                    </li>
                @endcan
            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs('administration.tenancies.*') ? 'active' : '' }}">
            <a href="{{ route('administration.tenancies.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-file-check"></i>
                <div data-i18n="Tenancies & Applications">{{ __('Tenancies & applications') }}</div>
            </a>
        </li>

        @canany (['Institute Read', 'Institute Create', 'Institute Update'])
            <li class="menu-item {{ request()->routeIs('administration.institute.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-books"></i>
                    <div data-i18n="Institute">{{ __('Institute') }}</div>
                </a>
                <ul class="menu-sub">
                    @can ('Institute Read')
                        <li class="menu-item {{ request()->is('administration/institute/all*') ? 'active' : '' }}">
                            <a href="{{ route('administration.institute.index') }}" class="menu-link">{{ __('All Institutes') }}</a>
                        </li>
                    @endcan
                    @can ('Institute Create')
                        <li class="menu-item {{ request()->routeIs('administration.institute.create') ? 'active' : '' }}">
                            <a href="{{ route('administration.institute.create') }}" class="menu-link">{{ __('Create Institute') }}</a>
                        </li>
                    @endcan
                    @canany (['Institute Read', 'Institute Update'])
                        <li class="menu-item {{ request()->routeIs('administration.institute.representatives.*') ? 'active open' : '' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <div data-i18n="Representative">{{ __('Representative') }}</div>
                            </a>
                            <ul class="menu-sub">
                                @can ('Institute Read')
                                    <li class="menu-item {{ request()->routeIs('administration.institute.representatives.index') ? 'active' : '' }}">
                                        <a href="{{ route('administration.institute.representatives.index') }}" class="menu-link">{{ __('All Representatives') }}</a>
                                    </li>
                                @endcan
                                @can ('Institute Update')
                                    <li class="menu-item {{ request()->routeIs(['administration.institute.representatives.create', 'administration.institute.representatives.create.entry']) ? 'active' : '' }}">
                                        <a href="{{ route('administration.institute.representatives.create.entry') }}" class="menu-link">{{ __('Create New Representative') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                </ul>
            </li>
        @endcanany

        @canany (['User Create', 'User Read'])
            <li class="menu-item {{ request()->routeIs('administration.users.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-users"></i>
                    <div data-i18n="Administration">{{ __('Administration') }}</div>
                </a>
                <ul class="menu-sub">
                    @can ('User Read')
                        <li class="menu-item {{ request()->routeIs('administration.users.index') ? 'active' : '' }}">
                            <a href="{{ route('administration.users.index') }}" class="menu-link">{{ __('All Users') }}</a>
                        </li>
                    @endcan
                    @can ('User Create')
                        <li class="menu-item {{ request()->routeIs('administration.users.create') ? 'active' : '' }}">
                            <a href="{{ route('administration.users.create') }}" class="menu-link">{{ __('Create administration user') }}</a>
                        </li>
                    @endcan
                </ul>
            </li>

            <li class="menu-item {{ request()->routeIs('administration.landlords.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-building-community"></i>
                    <div data-i18n="Landlord">{{ __('Landlord') }}</div>
                </a>
                <ul class="menu-sub">
                    @can ('User Read')
                        <li class="menu-item {{ request()->routeIs('administration.landlords.index') ? 'active' : '' }}">
                            <a href="{{ route('administration.landlords.index') }}" class="menu-link">{{ __('All Landlords') }}</a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('administration.landlords.pending') ? 'active' : '' }}">
                            <a href="{{ route('administration.landlords.pending') }}" class="menu-link">{{ __('Pending Landlords') }}</a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('administration.landlords.rejected') ? 'active' : '' }}">
                            <a href="{{ route('administration.landlords.rejected') }}" class="menu-link">{{ __('Rejected Landlords') }}</a>
                        </li>
                    @endcan
                    @can ('User Create')
                        <li class="menu-item {{ request()->routeIs('administration.landlords.create') ? 'active' : '' }}">
                            <a href="{{ route('administration.landlords.create') }}" class="menu-link">{{ __('Create New Landlord') }}</a>
                        </li>
                    @endcan
                </ul>
            </li>

            <li class="menu-item {{ request()->routeIs('administration.agents.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-briefcase"></i>
                    <div data-i18n="Agent">{{ __('Agent') }}</div>
                </a>
                <ul class="menu-sub">
                    @can ('User Read')
                        <li class="menu-item {{ request()->routeIs('administration.agents.index') ? 'active' : '' }}">
                            <a href="{{ route('administration.agents.index') }}" class="menu-link">{{ __('All Agents') }}</a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('administration.agents.pending') ? 'active' : '' }}">
                            <a href="{{ route('administration.agents.pending') }}" class="menu-link">{{ __('Pending Agents') }}</a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('administration.agents.rejected') ? 'active' : '' }}">
                            <a href="{{ route('administration.agents.rejected') }}" class="menu-link">{{ __('Rejected Agents') }}</a>
                        </li>
                    @endcan
                    @can ('User Create')
                        <li class="menu-item {{ request()->routeIs('administration.agents.create') ? 'active' : '' }}">
                            <a href="{{ route('administration.agents.create') }}" class="menu-link">{{ __('Create New Agent') }}</a>
                        </li>
                    @endcan
                </ul>
            </li>

            <li class="menu-item {{ request()->routeIs('administration.students.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-school"></i>
                    <div data-i18n="Student">{{ __('Student') }}</div>
                </a>
                <ul class="menu-sub">
                    @can ('User Read')
                        <li class="menu-item {{ request()->routeIs('administration.students.index') ? 'active' : '' }}">
                            <a href="{{ route('administration.students.index') }}" class="menu-link">{{ __('All Students') }}</a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('administration.students.unverified') ? 'active' : '' }}">
                            <a href="{{ route('administration.students.unverified') }}" class="menu-link">{{ __('Unverified Students') }}</a>
                        </li>
                    @endcan
                    @can ('User Create')
                        <li class="menu-item {{ request()->routeIs('administration.students.create') ? 'active' : '' }}">
                            <a href="{{ route('administration.students.create') }}" class="menu-link">{{ __('Create New Student') }}</a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany (['Geography Read', 'Geography Create', 'Permission Create', 'Permission Read', 'Role Create', 'Role Read'])
            <li class="menu-item {{ request()->is('administration/settings/geography*') || request()->is('administration/settings/rolepermission*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-settings"></i>
                    <div>{{ __('System settings') }}</div>
                </a>
                <ul class="menu-sub">
                    @can ('Geography Read')
                        <li class="menu-item {{ request()->is('administration/settings/geography*') ? 'active' : '' }}">
                            <a href="{{ route('administration.settings.geography.index') }}" class="menu-link">{{ __('Geography') }}</a>
                        </li>
                    @endcan
                    @canany (['Role Read', 'Permission Read'])
                        <li class="menu-item {{ request()->is('administration/settings/rolepermission*') ? 'active' : '' }}">
                            <a href="{{ auth()->user()->can('Role Read') ? route('administration.settings.rolepermission.role.index') : route('administration.settings.rolepermission.permission.index') }}" class="menu-link">{{ __('Roles & permissions') }}</a>
                        </li>
                    @endcanany
                </ul>
            </li>
        @endcanany
    </ul>
</aside>
