<nav x-data="{ open: false }" class="fixed top-0 left-0 right-0 z-50 bg-transparent">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 relative items-center">

            <!-- Logo (kiri) -->
            <div class="shrink-0 w-10 flex items-center">
                <a href="{{ route('dashboard') }}">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                </a>
            </div>

            <!-- Navigation Links (TENGAH) -->
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

            <!-- Settings Dropdown (kanan) -->
            <div class="hidden w-10 sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="shrink-0 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-transparent hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <img class="shrink-0 w-10 h-10 rounded-full" src="{{asset('images/image1.png')}}" alt="Image">
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
            </div>

            <!-- Hamburger (mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Responsive Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Home') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard.discover')" :active="request()->routeIs('dashboard.discover')">{{ __('Discover') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard.topCharted')" :active="request()->routeIs('dashboard.topCharted')">{{ __('Top Charted') }}</x-responsive-nav-link>
        </div>
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
    </div>
</nav>