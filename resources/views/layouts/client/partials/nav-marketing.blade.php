{{-- Fixed transparent navbar — marketing (home, explore, etc.) --}}
<nav
    id="navbar"
    class="fixed top-0 z-50 w-full overflow-visible border-b border-transparent bg-transparent py-6 transition-all duration-300"
    x-data="{ mobileMenuOpen: false }"
    x-on:keydown.escape.window="mobileMenuOpen = false"
    x-on:click.outside="mobileMenuOpen = false"
>
    <div class="relative mx-auto flex max-w-7xl items-center justify-between px-6">
        <a href="{{ route('client.home') }}" class="flex shrink-0 cursor-pointer items-center gap-2" x-on:click="mobileMenuOpen = false">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-black">
                <span class="text-xl font-bold leading-none tracking-tighter text-white">S</span>
            </div>
            <span class="text-xl font-bold tracking-tight">{{ config('app.name') }}.</span>
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
                <a
                    href="{{ route('register') }}"
                    class="hidden rounded-full bg-black px-5 py-2.5 text-sm font-medium text-white transition-transform hover:bg-gray-800 active:scale-95 md:block"
                >
                    {{ __('Sign up') }}
                </a>
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
                    <a
                        href="{{ route('register') }}"
                        class="mt-1 block rounded-xl bg-black px-4 py-3 text-center text-base font-semibold text-white transition-colors hover:bg-gray-800"
                        x-on:click="mobileMenuOpen = false"
                    >
                        {{ __('Sign up') }}
                    </a>
                </div>
            @endguest
        </div>
    </div>
</nav>
