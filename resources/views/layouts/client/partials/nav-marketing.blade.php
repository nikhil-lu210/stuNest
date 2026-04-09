{{-- Fixed transparent navbar — home page --}}
<nav id="navbar" class="fixed top-0 w-full z-50 transition-all duration-300 bg-transparent py-6">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
        <a href="{{ route('client.home') }}" class="flex items-center gap-2 cursor-pointer">
            <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-xl leading-none tracking-tighter">S</span>
            </div>
            <span class="text-xl font-bold tracking-tight">{{ config('app.name') }}.</span>
        </a>

        <div class="hidden md:flex items-center gap-8 font-medium text-sm">
            <a href="{{ route('client.explore') }}" class="text-gray-900 hover:text-gray-600 transition-colors">Explore</a>
            <a href="{{ route('client.home') }}#how-it-works" class="text-gray-900 hover:text-gray-600 transition-colors">How it works</a>
            <a href="{{ url('/register?role=landlord') }}" class="text-gray-900 hover:text-gray-600 transition-colors">List a property</a>
        </div>

        <div class="flex items-center gap-4">
            @guest
                <a href="{{ route('login') }}" class="hidden md:block text-sm font-medium hover:text-gray-600 transition-colors px-4 py-2">
                    Log in
                </a>
                <a href="{{ route('register') }}" class="hidden md:block bg-black text-white text-sm font-medium px-5 py-2.5 rounded-full hover:bg-gray-800 transition-transform active:scale-95">
                    Sign up
                </a>
            @else
                <span class="hidden md:inline text-sm text-gray-600 truncate max-w-[10rem]">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline hidden sm:block">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-gray-600 hover:text-gray-900">Log out</button>
                </form>
            @endguest
            <a href="{{ route('client.explore') }}" class="md:hidden p-2 rounded-full hover:bg-gray-100 transition-colors" aria-label="Search">
                <i data-lucide="search" class="w-6 h-6"></i>
            </a>
        </div>
    </div>
</nav>
