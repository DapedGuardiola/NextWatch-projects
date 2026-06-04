<nav x-data="{ open: false }" class="fixed top-0 left-0 right-0 z-50 bg-transparent">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 relative items-center">

            <div class="shrink-0 w-10 flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <!-- Desktop / tablet: full brand (show on md and up)
                    <img src="{{ asset('images/brand/logo2.png') }}" alt="NextWatch" class="hidden md:block h-10 w-auto">
                    Mobile: smaller logo (same asset) -->
                    <!--<img src="{{ asset('images/brand/logo2.png') }}" alt="NextWatch" class="md:hidden h-8 w-auto"> -->
                </a>
            </div>

            <div class="hidden sm:flex sm:absolute sm:left-1/2 sm:-translate-x-1/2 gap-2">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Home') }}
                </x-nav-link>
                {{-- Discover → trigger modal --}}
                <x-nav-link @click="$dispatch('open-discover')" :active="request()->routeIs('discover.results')"
                    class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium leading-5 transition
                    hover:bg-gray-400/50 active:bg-gray-400/50
                    border-transparent focus:outline-none">
                    {{ __('Discover') }}
                </x-nav-link>
                <x-nav-link :href="route('dashboard.topCharted')" :active="request()->routeIs('dashboard.topCharted')">
                    {{ __('Top Charted') }}
                </x-nav-link>
            </div>

            <div class="hidden w-10 sm:flex sm:items-center">
            @auth
                {{-- Sudah login: dropdown profile biasa --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        @php
                            $avatarUrl = Auth::user()->avatar
                                ? asset('storage/' . Auth::user()->avatar)
                                : "https://ui-avatars.com/api/?name=" . urlencode(Auth::user()->name) . "&background=333&color=fff";
                        @endphp
                        <button class="shrink-0 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-transparent hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <img class="shrink-0 w-10 h-10 rounded-full object-cover" src="{{ $avatarUrl }}" alt="{{ Auth::user()->name }}">
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.index')">{{ __('Profile UI') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            @else
                {{-- Belum login: icon default + dropdown login/register --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="shrink-0 inline-flex items-center px-3 py-2 border border-transparent rounded-md text-gray-400 bg-transparent hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div class="w-10 h-10 rounded-full bg-gray-500/80 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                    <x-dropdown-link
                        href="#"
                        @click.prevent="window.dispatchEvent(new CustomEvent('open-auth-modal', { detail: 'login' }))">
                        Login
                    </x-dropdown-link>

                    <x-dropdown-link
                        href="#"
                        @click.prevent="window.dispatchEvent(new CustomEvent('open-auth-modal', { detail: 'register' }))">
                        Register
                    </x-dropdown-link>
                </x-slot>
                </x-dropdown>
            @endauth
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Home') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard.discover')" :active="request()->routeIs('dashboard.discover')">{{ __('Discover') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard.topCharted')" :active="request()->routeIs('dashboard.topCharted')">{{ __('Top Charted') }}</x-responsive-nav-link>
        </div>

        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="mt-3 space-y-1 px-4">
                <x-responsive-nav-link :href="route('login')">{{ __('Login') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">{{ __('Register') }}</x-responsive-nav-link>
            </div>
        </div>
        @endauth
    </div>
</nav>