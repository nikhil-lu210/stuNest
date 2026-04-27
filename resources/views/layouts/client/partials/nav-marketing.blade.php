{{-- Fixed transparent navbar — marketing (home, explore, etc.) --}}
<nav
    id="navbar"
    class="fixed top-0 z-50 w-full overflow-visible border-b border-transparent bg-transparent py-6 transition-all duration-300"
    x-data="{ mobileMenuOpen: false }"
    x-on:keydown.escape.window="mobileMenuOpen = false"
    x-on:click.outside="mobileMenuOpen = false"
>
    <div class="relative mx-auto flex max-w-7xl items-center justify-between px-6">
        <a href="{{ route('client.home') }}" class="flex shrink-0 cursor-pointer items-center" x-on:click="mobileMenuOpen = false">
            <x-stunest-logo class="h-10 w-auto sm:h-11 md:h-12 max-w-[min(100vw-9rem,320px)] sm:max-w-[300px] object-left object-contain" />
        </a>

        <div class="hidden items-center gap-8 text-sm font-medium md:flex">
            <a href="{{ route('client.explore') }}" class="text-gray-900 transition-colors hover:text-gray-600">Explore</a>
            <a href="{{ route('client.home') }}#how-it-works" class="text-gray-900 transition-colors hover:text-gray-600">How it works</a>
            <a href="{{ url('/register?role=landlord') }}" class="text-gray-900 transition-colors hover:text-gray-600">List a property</a>
        </div>

        <div class="flex items-center gap-1 sm:gap-2 md:gap-4">
            @guest
                <a
                    href="{{ route('login') }}"
                    class="hidden rounded-full px-4 py-2 text-sm font-medium transition-colors hover:text-gray-600 md:block"
                >
                    {{ __('Log in') }}
                </a>
                <div
                    x-data="{ open: false }"
                    @click.outside="open = false"
                    class="relative inline-block hidden text-left md:block"
                >
                    <button
                        type="button"
                        @click="open = !open"
                        class="inline-flex items-center gap-1.5 rounded-full bg-primary-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-transform hover:bg-primary-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 active:scale-95"
                        x-bind:aria-expanded="open"
                        aria-haspopup="true"
                    >
                        {{ __('Sign up') }}
                        <i data-lucide="chevron-down" class="h-4 w-4 shrink-0 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" aria-hidden="true"></i>
                    </button>
                    <div
                        x-show="open"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="translate-y-1 opacity-0"
                        x-transition:enter-end="translate-y-0 opacity-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="translate-y-0 opacity-100"
                        x-transition:leave-end="translate-y-1 opacity-0"
                        class="absolute right-0 z-50 mt-2 w-56 origin-top-right overflow-hidden rounded-xl border border-gray-200 bg-white py-1 shadow-lg"
                        role="menu"
                    >
                        @include('layouts.client.partials.nav-public-signup-links', [
                            'isMobileMenu' => false,
                            'closeDesktopDropdown' => true,
                        ])
                    </div>
                </div>
            @else
                @include('layouts.client.partials.nav-user-menu', ['wrapperClass' => ''])
            @endguest

            <a
                href="{{ route('client.explore') }}"
                class="rounded-full p-2 transition-colors hover:bg-gray-100 md:hidden"
                aria-label="{{ __('Search') }}"
            >
                <i data-lucide="search" class="h-6 w-6"></i>
            </a>

            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-full text-gray-900 transition-colors hover:bg-gray-100 md:hidden"
                aria-expanded="false"
                x-bind:aria-expanded="mobileMenuOpen"
                aria-controls="marketing-mobile-menu"
                aria-label="{{ __('Open menu') }}"
                x-on:click="mobileMenuOpen = !mobileMenuOpen"
            >
                <i data-lucide="menu" class="h-6 w-6" x-show="!mobileMenuOpen" x-cloak></i>
                <i data-lucide="x" class="h-6 w-6" x-show="mobileMenuOpen" x-cloak></i>
            </button>
        </div>
    </div>

    {{-- Mobile slide-down panel --}}
    <div
        id="marketing-mobile-menu"
        x-show="mobileMenuOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-y-2 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-1 opacity-0"
        class="absolute left-0 right-0 top-full z-[100] border-b border-gray-100 bg-white/95 px-6 py-4 shadow-lg backdrop-blur-md md:hidden"
    >
        <div class="mx-auto flex max-w-7xl flex-col gap-1">
            <a
                href="{{ route('client.explore') }}"
                class="rounded-xl px-4 py-3 text-base font-medium text-gray-900 transition-colors hover:bg-gray-50"
                x-on:click="mobileMenuOpen = false"
            >
                {{ __('Explore') }}
            </a>
            <a
                href="{{ route('client.home') }}#how-it-works"
                class="rounded-xl px-4 py-3 text-base font-medium text-gray-900 transition-colors hover:bg-gray-50"
                x-on:click="mobileMenuOpen = false"
            >
                {{ __('How it works') }}
            </a>
            <a
                href="{{ url('/register?role=landlord') }}"
                class="rounded-xl px-4 py-3 text-base font-medium text-gray-900 transition-colors hover:bg-gray-50"
                x-on:click="mobileMenuOpen = false"
            >
                {{ __('List a property') }}
            </a>
            @guest
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <a
                        href="{{ route('login') }}"
                        class="block rounded-xl px-4 py-3 text-base font-medium text-gray-900 transition-colors hover:bg-gray-50"
                        x-on:click="mobileMenuOpen = false"
                    >
                        {{ __('Log in') }}
                    </a>
                    <p class="mt-1 px-4 pt-2 text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Sign up') }}</p>
                    <div class="mt-1 flex flex-col gap-0.5">
                        @include('layouts.client.partials.nav-public-signup-links', [
                            'isMobileMenu' => true,
                            'onClickClose' => 'mobileMenuOpen = false',
                        ])
                    </div>
                </div>
            @endguest
        </div>
    </div>
</nav>
