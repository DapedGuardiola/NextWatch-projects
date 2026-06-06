<x-app-layout>
    <x-slot name="title">{{ __('Dashboard') }}</x-slot>

    <div class="h-screen relative">
        <img src="https://image.tmdb.org/t/p/original/{{ $popularMovie['poster_path'] }}"
            class="w-full h-full object-cover object-center" alt="hero">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent from-20% via-[#212121]/75 via-70% to-[#212121] to-100%"></div>
        <div class="absolute bottom-[15%] left-10">
            <h1 class="text-white text-7xl font-bold">{{ $popularMovie['title'] }}</h1>
        </div>
    </div>

    <div class="-mt-[200px] max-w-[95%] left-0 right-0 relative z-10">
        <div class="mt-[135px]">
            <x-movie.search-bar />
        </div>

        @foreach($moviesByGenre as $genre => $movies)
        <div class="text-white mx-4 md:mx-8 lg:mx-10">
            <h2>
                <p class="text-2xl md:text-3xl text-white font-bold">Top on {{ $genre }}</p>
            </h2>
        </div>
        <div class="flex gap-3 md:gap-4 py-4 px-4 md:px-8 lg:px-10 w-full md:w-[95%] lg:w-[90%] mx-auto overflow-x-auto scrollbar-hide">
            @foreach($movies as $index => $movie)
            <x-movie.topmovies-modal
                :poster="'https://image.tmdb.org/t/p/original/' . $movie['poster_path']"
                :title="$movie['title']"
                :tmdb_movie_id="$movie['id']"
                :year="$movie['year'] ?? null"
                :rating="$movie['rating'] ?? null"
                :overview="$movie['overview'] ?? null"
                :genres="[]"
                :duration="$movie['runtime'] ?? null"
                :rank="$index + 1" />
            @endforeach
        </div>
        @endforeach

        <div class="mx-10 my-10">
            <p class="text-3xl text-white font-bold">Actors</p>
        </div>
        <div class="flex gap-12 px-2 max-w-[90%] overflow-hidden mx-auto overflow-x-auto scrollbar-hide">
            @foreach($actors as $actor)
            <x-movie.actor-card
                :actor_id="$actor->tmdb_actor_id"
                :image_url="$actor->image_url"
                :name="$actor->name" />
            @endforeach
        </div>
    </div>

    {{-- Auth Modal --}}
    @guest
    <div
        x-data="{
            open: false,
            showNudge: false,
            tab: '{{ old('name') ? 'register' : ($openModal ?? 'login') }}',

            openModal(type = 'login') {
                this.tab = type
                this.open = true
                this.showNudge = false
                document.body.classList.add('overflow-hidden')
            },

            closeModal() {
                this.open = false
                document.body.classList.remove('overflow-hidden')
            }
        }"

        @open-auth-modal.window="openModal($event.detail)"

        x-init="
            @if($errors->any())
                open = true
                showNudge = false
                document.body.classList.add('overflow-hidden')
            @elseif(isset($openModal))
                openModal('{{ $openModal }}')
            @else
                setTimeout(() => showNudge = true, 3000)
            @endif
        ">

        {{-- Nudge popup --}}
        <div
            x-show="showNudge && !open"
            x-transition
            class="fixed bottom-6 right-6 z-50 w-72"
            x-cloak>
            <div class="bg-[#1e1e22] border border-white/10 rounded-[18px] p-5 relative">

                <button
                    @click="showNudge = false"
                    class="absolute top-3 right-3 w-6 h-6 rounded-full bg-white/7 flex items-center justify-center text-white/40 hover:text-white/70">
                    ✕
                </button>

                <div class="flex items-center gap-2.5 mb-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/15 border border-indigo-500/25 flex items-center justify-center">
                        ▶
                    </div>
                    <p class="text-sm font-medium text-white/90">
                        Temukan film yang tepat untukmu
                    </p>
                </div>

                <p class="text-xs text-white/40 leading-relaxed mb-4">
                    Buat akun gratis untuk akses semua fitur rekomendasi personal.
                </p>

                <div class="flex gap-2">
                    <button
                        @click="openModal('login')"
                        class="flex-1 py-2 rounded-[10px] bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium transition">
                        Login
                    </button>

                    <button
                        @click="openModal('register')"
                        class="flex-1 py-2 rounded-[10px] bg-white/6 hover:bg-white/10 border border-white/10 text-white/70 text-xs font-medium transition">
                        Register
                    </button>
                </div>
            </div>
        </div>

        {{-- BACKDROP --}}
        <div
            x-show="open"
            x-transition.opacity
            @click="closeModal()"
            class="fixed inset-0 bg-black/60 backdrop-blur-md z-40"
            x-cloak></div>

        {{-- MODAL --}}
        <div
            x-show="open"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @keydown.escape.window="closeModal()"
            x-cloak>
            <div
                @click.away="closeModal()"
                class="bg-[#1a1a1f] border border-white/10 rounded-2xl shadow-2xl w-full max-w-3xl h-[560px] overflow-hidden flex">

                {{-- kiri poster --}}
                <div class="hidden lg:block w-64 relative flex-shrink-0">
                    <img
                        src="https://image.tmdb.org/t/p/original/{{ $popularMovie['poster_path'] }}"
                        class="w-full h-full object-cover"
                        alt="poster">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent to-[#1a1a1f]"></div>
                </div>

                {{-- kanan form --}}
                <div class="flex-1 p-8 h-full overflow-y-auto">

                    {{-- close --}}
                    <div class="flex justify-end mb-2 -mt-2">
                        <button
                            @click="closeModal()"
                            class="w-6 h-6 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white text-xs transition">
                            ✕
                        </button>
                    </div>
                    <div class="w-full block mb-6">
                                <img src="{{ asset('images/brand/logo3.png') }}" alt="NextWatch" class="hidden md:block h-10 w-auto mx-auto">
                            </div>
                    {{-- TAB --}}
                    <div class="flex gap-1 bg-white/5 rounded-xl p-1 mb-6">
                        <button
                            @click="tab = 'login'"
                            :class="tab === 'login'
                                ? 'bg-indigo-600 text-white'
                                : 'text-white/40 hover:text-white/70'"
                            class="flex-1 py-2 rounded-lg text-sm font-medium transition">
                            Login
                        </button>

                        <button
                            @click="tab = 'register'"
                            :class="tab === 'register'
                                ? 'bg-indigo-600 text-white'
                                : 'text-white/40 hover:text-white/70'"
                            class="flex-1 py-2 rounded-lg text-sm font-medium transition">
                            Register
                        </button>
                    </div>

                    {{-- LOGIN --}}
                    <div x-show="tab === 'login'">

                        <!-- Brand logo (Centered above login card) -->
                        <!-- <img src="{{ asset('images/brand/brand.png') }}" alt="NextWatch"
                            class="h-20 w-auto mx-auto mb-6" /> -->

                        <h2 class="text-xl font-bold text-white mb-1">
                            Selamat datang kembali
                        </h2>

                        <p class="text-sm text-white/40 mb-2">
                            Login untuk lanjutkan pengalamanmu
                        </p>

                        <form method="POST" action="{{ route('login') }}" class="space-y-4">
                            @csrf

                            <input
                                type="email"
                                name="email"
                                placeholder="email@kamu.com"
                                required
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white"
                                {{ $errors->has('email')
                                ? 'border-red-500 focus:border-red-500'
                                : 'border-white/10 focus:border-indigo-500' }}">
                            @error('email')
                            <p class="text-red-400 text-xs mt-1">
                                {{ $message }}
                            </p>
                            @enderror

                            <div x-data="{ showPassword: false }" class="relative">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    placeholder="Password"
                                    required
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 pr-12 py-2.5 text-white">

                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-3 flex items-center text-white/40 hover:text-white transition">
                                    {{-- eye open --}}
                                    <svg
                                        x-show="showPassword"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                        class="w-5 h-5">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>

                                    {{-- eye slash --}}
                                    <svg
                                        x-show="!showPassword"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                        class="w-5 h-5">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 3l18 18" />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M10.58 10.58A3 3 0 0013.42 13.42M9.88 5.09A10.94 10.94 0 0112 4.5c6 0 9.75 7.5 9.75 7.5a16.18 16.18 0 01-4.08 4.77M6.23 6.23A16.13 16.13 0 002.25 12s3.75 7.5 9.75 7.5a10.9 10.9 0 004.09-.77" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                            <p class="text-red-400 text-xs mt-1">
                                {{ $message }}
                            </p>
                            @enderror

                            <div class="flex items-center justify-between text-sm">
                                <label class="inline-flex items-center gap-2 text-white/60 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        class="rounded border-white/20 bg-white/5 text-indigo-600 focus:ring-indigo-500">
                                    <span>Ingat saya</span>
                                </label>

                                @if (Route::has('password.request'))
                                <a
                                    href="{{ route('password.request') }}"
                                    class="text-indigo-400 hover:text-indigo-300 transition">
                                    Lupa password?
                                </a>
                                @endif
                            </div>

                            <button
                                type="submit"
                                class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold">
                                Login
                            </button>
                            
                        </form>
                    </div>

                    {{-- REGISTER --}}
                    <div x-show="tab === 'register'">

                        <h2 class="text-xl font-bold text-white mb-1">
                            Buat akun baru
                        </h2>

                        <p class="text-sm text-white/40 mb-2">
                            Gratis, cepat, dan personal
                        </p>

                        <form method="POST" action="{{ route('register') }}" class="space-y-4">
                            @csrf

                            <input
                                type="text"
                                name="name"
                                placeholder="Nama"
                                required
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white"
                                value="{{ old('name') }}">
                            @error('name')
                            <p class="text-red-400 text-xs mt-1">
                                {{ $message }}
                            </p>
                            @enderror

                            <input
                                type="email"
                                name="email"
                                placeholder="Email"
                                required
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white"
                                value="{{ old('email') }}">
                            @error('email')
                            <p class="text-red-400 text-xs mt-1">
                                {{ $message }}
                            </p>
                            @enderror

                            <div x-data="{ showPassword: false }" class="relative">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    placeholder="Password"
                                    required
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 pr-12 py-2.5 text-white">
                                @error('password')
                                <p class="text-red-400 text-xs mt-1">
                                    {{ $message }}
                                </p>
                                @enderror

                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-3 flex items-center text-white/40 hover:text-white transition">
                                    {{-- eye open --}}
                                    <svg
                                        x-show="showPassword"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                        class="w-5 h-5">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>

                                    {{-- eye slash --}}
                                    <svg
                                        x-show="!showPassword"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                        class="w-5 h-5">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 3l18 18" />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M10.58 10.58A3 3 0 0013.42 13.42M9.88 5.09A10.94 10.94 0 0112 4.5c6 0 9.75 7.5 9.75 7.5a16.18 16.18 0 01-4.08 4.77M6.23 6.23A16.13 16.13 0 002.25 12s3.75 7.5 9.75 7.5a10.9 10.9 0 004.09-.77" />
                                    </svg>
                                </button>
                            </div>

                            <div x-data="{ showConfirmPassword: false }" class="relative">
                                <input
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    name="password_confirmation"
                                    placeholder="Confirm Password"
                                    required
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 pr-12 py-2.5 text-white">

                                <button
                                    type="button"
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute inset-y-0 right-3 flex items-center text-white/40 hover:text-white transition">
                                    {{-- eye open --}}
                                    <svg
                                        x-show="showConfirmPassword"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                        class="w-5 h-5">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>

                                    {{-- eye slash --}}
                                    <svg
                                        x-show="!showConfirmPassword"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                        class="w-5 h-5">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 3l18 18" />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M10.58 10.58A3 3 0 0013.42 13.42M9.88 5.09A10.94 10.94 0 0112 4.5c6 0 9.75 7.5 9.75 7.5a16.18 16.18 0 01-4.08 4.77M6.23 6.23A16.13 16.13 0 002.25 12s3.75 7.5 9.75 7.5a10.9 10.9 0 004.09-.77" />
                                    </svg>
                                </button>
                            </div>
                            @error('password_confirmation')
                            <p class="text-red-400 text-xs mt-1">
                                {{ $message }}
                            </p>
                            @enderror

                            <button
                                type="submit"
                                class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold">
                                Register
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>
    @endguest

</x-app-layout>