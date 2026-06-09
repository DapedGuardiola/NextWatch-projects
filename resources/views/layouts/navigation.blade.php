<nav x-data="{ open: false }" class="fixed top-0 left-0 right-0 z-50 bg-transparent"
    :class="open ? 'bg-[#141414]' : 'bg-transparent'">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 relative items-center">

            {{-- Logo --}}
            <div class="shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <img src="{{ asset('images/brand/logo2.png') }}" alt="NextWatch" class="hidden md:block h-10 w-auto">
                    <img src="{{ asset('images/brand/logo2.png') }}" alt="NextWatch" class="md:hidden h-8 w-auto">
                </a>
            </div>

            {{-- Nav Links (desktop) --}}
            <div class="hidden sm:flex sm:absolute sm:left-1/2 sm:-translate-x-1/2 gap-2">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Home') }}
                </x-nav-link>
                <x-nav-link href="#" @click.prevent="$dispatch('open-discover')" :active="request()->routeIs('discover.results')"
                    class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium leading-5 transition
                    cursor-pointer hover:bg-gray-400/50 active:bg-gray-400/50
                    border-transparent focus:outline-none">
                    {{ __('Discover') }}
                </x-nav-link>
                <x-nav-link :href="route('dashboard.topCharted')" :active="request()->routeIs('dashboard.topCharted')">
                    {{ __('Top Charted') }}
                </x-nav-link>
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-2">

                {{-- Avatar/Profile (desktop) --}}
                <div class="hidden sm:flex sm:items-center">
                    @auth
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
                            <x-dropdown-link :href="route('profile.index')">{{ __('Profile') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('watchlist.index')">{{ __('Watchlist') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                    @else
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="shrink-0 inline-flex items-center px-3 py-2 border border-transparent rounded-md text-gray-400 bg-transparent hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div class="w-10 h-10 rounded-full bg-gray-500/80 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link href="#" @click.prevent="window.dispatchEvent(new CustomEvent('open-auth-modal', { detail: 'login' }))">Login</x-dropdown-link>
                            <x-dropdown-link href="#" @click.prevent="window.dispatchEvent(new CustomEvent('open-auth-modal', { detail: 'register' }))">Register</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                    @endauth
                </div>

                {{-- Hamburger (mobile only) --}}
                <button @click="open = !open"
                    class="sm:hidden inline-flex items-center justify-center p-2 rounded-lg
                           text-gray-400 hover:text-white hover:bg-white/10
                           focus:outline-none transition duration-150">
                    {{-- Icon hamburger --}}
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    {{-- Icon close --}}
                    <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="open"
        x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="sm:hidden bg-[#141414]">

        {{-- Nav Links --}}
        @auth
        <div class="px-4 pt-3 pb-2 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="block px-4 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/10 transition
               {{ request()->routeIs('dashboard') ? 'bg-white/10' : '' }}">
               {{ __('Home') }}
            </a>
            <a href="#" @click.prevent="$dispatch('open-discover'); open = false"
               class="block px-4 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/10 transition
               {{ request()->routeIs('discover.results') ? 'bg-white/10' : '' }}">
               {{ __('Discover') }}
            </a>
            <a href="{{ route('dashboard.topCharted') }}"
               class="block px-4 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/10 transition
               {{ request()->routeIs('dashboard.topCharted') ? 'bg-white/10' : '' }}">
               {{ __('Top Charted') }}
            </a>
        </div>

        {{-- Divider --}}
        <div class="border-t border-white/10 mx-4"></div>

        {{-- User Section --}}
        <div class="px-4 pt-3 pb-4">
            <div class="space-y-1">
                <a href="{{ route('profile.index') }}" class="block px-4 py-2 rounded-lg text-sm text-white hover:bg-white/10 transition">{{ __('Profile') }}</a>
                <a href="{{ route('watchlist.index') }}" class="block px-4 py-2 rounded-lg text-sm text-white hover:bg-white/10 transition">{{ __('Watchlist') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 rounded-lg text-sm text-red-400 hover:bg-white/10 transition">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="px-4 pt-3 pb-4 space-y-1">
            <a href="#"
               @click.prevent="window.dispatchEvent(new CustomEvent('open-auth-modal', { detail: 'login' })); open = false"
               class="block px-4 py-2 rounded-lg text-sm text-white hover:bg-white/10 transition">
               {{ __('Login') }}
            </a>
            <a href="#"
               @click.prevent="window.dispatchEvent(new CustomEvent('open-auth-modal', { detail: 'register' })); open = false"
               class="block px-4 py-2 rounded-lg text-sm text-white hover:bg-white/10 transition">
               {{ __('Register') }}
            </a>
        </div>
        @endauth
    </div>
</nav>