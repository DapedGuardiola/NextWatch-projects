<x-app-layout>
    <x-slot name="title">
        {{ __('User Profile - NextWatch') }}
    </x-slot>

    @php
        $avatarUrl = auth()->user()->avatar
            ? asset('storage/' . auth()->user()->avatar)
            : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=333&color=fff';
    @endphp

    {{-- Gaya CSS tambahan untuk menyembunyikan scrollbar sesuai permintaan PM --}}
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    {{-- Wrapper Alpine.js untuk fitur 3 Modal --}}
    <div x-data="{ openGenre: false, openActor: false, openDirector: false }" class="relative min-h-screen md:h-screen w-full overflow-hidden text-gray-200">
        
        {{-- Background Blur --}}
        <div class="absolute inset-0 bg-cover bg-center z-0"
            style="background-image: url('{{ $avatarUrl }}'); filter: blur(60px); transform: scale(1.2);">
        </div>
        <div class="absolute inset-0 bg-black/60 z-0"></div>

        {{-- CONTAINER UTAMA: Membagi halaman menjadi 2 kontainer utama --}}
        <div class="relative z-10 flex flex-col md:flex-row h-full max-w-7xl mx-auto p-4 sm:p-6 gap-2 md:gap-0 overflow-hidden">

            {{-- SIDEBAR KIRI (Navigasi Profil Universal) --}}
            <div class="w-full md:w-1/4 flex flex-row md:flex-col gap-2 md:gap-5 font-semibold overflow-x-auto md:overflow-visible scrollbar-hide flex-shrink-0 items-center md:items-stretch z-30 pb-2 md:pb-0 pt-16 md:pt-20">
                <a href="{{ route('profile.index') }}" 
                   class="{{ Route::is('profile.index') ? 'bg-white text-black font-bold' : 'text-gray-400 hover:text-white' }} py-2 px-3 md:py-2.5 md:px-5 rounded-xl text-center shadow-md text-xs md:text-base whitespace-nowrap flex-shrink-0 transition duration-200">
                   User Profile
                </a>
                <a href="{{ route('profile.settings') }}" 
                   class="{{ Route::is('profile.settings') ? 'bg-white text-black font-bold' : 'text-gray-400 hover:text-white' }} py-2 px-3 md:py-2.5 md:px-5 rounded-xl text-center shadow-md text-xs md:text-base whitespace-nowrap flex-shrink-0 transition duration-200">
                   Account Settings
                </a>
                <a href="{{ route('profile.persona') }}" 
                   class="{{ Route::is('profile.persona') ? 'bg-white text-black font-bold' : 'text-gray-400 hover:text-white' }} py-2 px-3 md:py-2.5 md:px-5 rounded-xl text-center shadow-md text-xs md:text-base whitespace-nowrap flex-shrink-0 transition duration-200">
                   Edit Persona
                </a>
                <a href="{{ route('favorites.index') }}" 
                   class="{{ Route::is('favorites.index') ? 'bg-white text-black font-bold' : 'text-gray-400 hover:text-white' }} py-2 px-3 md:py-2.5 md:px-5 rounded-xl text-center shadow-lg text-xs md:text-base whitespace-nowrap flex-shrink-0 transition duration-200">
                   Favorite Movies
                </a>
                <a href="{{ route('watchlist.index') }}" 
                   class="{{ Route::is('watchlist.index') ? 'bg-white text-black font-bold' : 'text-gray-400 hover:text-white' }} py-2 px-3 md:py-2.5 md:px-5 rounded-xl text-center shadow-md text-xs md:text-base whitespace-nowrap flex-shrink-0 transition duration-200">
                   Watchlist
                </a>

                <form method="POST" action="{{ route('logout') }}" class="md:mt-8 flex-shrink-0">
                    @csrf
                    <button type="submit" class="text-red-500 w-full py-2 px-3 md:py-2.5 md:px-5 text-center hover:text-red-400 transition font-bold text-xs md:text-base whitespace-nowrap">
                        Sign Out
                    </button>
                </form>
            </div>

            {{-- CONTAINER KANAN: Konten Utama (Scrollable dengan Scrollbar Tersembunyi) --}}
            <div class="w-full md:w-3/4 pt-4 md:pt-20 pb-24 md:pb-10 h-full overflow-y-auto scrollbar-hide z-20">
                
                {{-- 1. FORM EDIT PROFIL --}}
                <div class="bg-gray-900/40 backdrop-blur-md border border-gray-700/50 rounded-2xl md:rounded-3xl p-5 md:p-8 flex flex-col sm:flex-row gap-6 md:gap-8 mb-8 md:mb-10 shadow-2xl">
                    <div class="w-full sm:w-1/3 flex flex-col items-center">
                        <img src="{{ $avatarUrl }}" alt="Profile" class="w-32 h-32 sm:w-40 sm:h-40 md:w-48 md:h-48 rounded-2xl md:rounded-3xl object-cover mb-4 shadow-xl">
                        <form id="avatar-form" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="avatar" id="avatar-input" class="hidden" accept="image/*">
                        </form>
                        <button onclick="document.getElementById('avatar-input').click()"
                            class="bg-white text-black font-semibold py-2.5 px-6 rounded-xl w-full sm:w-auto hover:bg-gray-200 transition text-sm">
                            Edit Profile
                        </button>
                        @error('avatar')
                            <p class="text-xs text-red-500 mt-2 text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="w-full sm:w-2/3">
                        <form action="{{ route('profile.update') }}" method="POST" class="flex flex-col gap-4 md:gap-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="text-xs sm:text-sm text-gray-400">Name</label>
                                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                                    class="w-full bg-gray-800/50 p-2.5 sm:p-3 rounded-xl mt-1 text-sm sm:text-base text-white border border-transparent focus:border-yellow-500 focus:bg-gray-800 focus:ring-0 outline-none transition">
                                @error('name')
                                    <p class="text-xs text-red-500 mt-1 ml-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="w-full sm:w-1/2">
                                    <label class="text-xs sm:text-sm text-gray-400">Gender</label>
                                    <select name="gender" class="w-full bg-gray-800/50 p-2.5 sm:p-3 rounded-xl mt-1 text-sm sm:text-base text-white border border-transparent focus:border-yellow-500 focus:bg-gray-800 focus:ring-0 outline-none transition appearance-none">
                                        <option value="" disabled {{ !auth()->user()->gender ? 'selected' : '' }}>Select Gender</option>
                                        <option value="Male" {{ auth()->user()->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ auth()->user()->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ auth()->user()->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <p class="text-xs text-red-500 mt-1 ml-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="w-full sm:w-1/2">
                                    <label class="text-xs sm:text-sm text-gray-400">Date of Birth</label>
                                    <input type="date" name="dob" value="{{ auth()->user()->dob }}"
                                        class="w-full bg-gray-800/50 p-2.5 sm:p-3 rounded-xl mt-1 text-sm sm:text-base text-white border border-transparent focus:border-yellow-500 focus:bg-gray-800 focus:ring-0 outline-none transition color-scheme-dark"
                                        style="color-scheme: dark;">
                                    @error('dob')
                                        <p class="text-xs text-red-500 mt-1 ml-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="text-xs sm:text-sm text-gray-400">Bio</label>
                                <textarea name="bio" rows="3"
                                    class="w-full bg-gray-800/50 p-2.5 sm:p-3 rounded-xl mt-1 text-sm sm:text-base text-gray-300 border border-transparent focus:border-yellow-500 focus:bg-gray-800 focus:ring-0 outline-none transition">{{ auth()->user()->bio }}</textarea>
                                @error('bio')
                                    <p class="text-xs text-red-500 mt-1 ml-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end mt-2">
                                <button type="submit" class="bg-yellow-600/80 hover:bg-yellow-500 text-white font-semibold py-2 px-6 rounded-xl transition text-sm w-full sm:w-auto">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 2. SEKSI: YOUR CINEMATIC PREFERENCES --}}
                <div class="bg-gray-900/40 backdrop-blur-md border border-gray-700/50 rounded-2xl md:rounded-3xl p-5 md:p-8 shadow-2xl">
                    <div class="mb-6 border-b border-gray-700/50 pb-4">
                        <h2 class="text-xl md:text-2xl font-bold text-white">Your Cinematic Preferences</h2>
                        <p class="text-xs sm:text-sm text-gray-400 mt-1">Berdasarkan aktivitas dan pilihan film kamu selama ini.</p>
                    </div>

                    @if($userTaste || $userGenres->count() > 0)
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                            
                            {{-- Grafik Genre --}}
                            <div class="bg-black/30 rounded-2xl p-5 border border-white/5 flex flex-col h-full">
                                <h3 class="text-sm md:text-base font-semibold text-gray-300 mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    Genre Affinity
                                </h3>
                                <div class="flex-grow flex items-center justify-center w-full">
                                    <canvas id="genreChart" style="max-height: 220px; width: 100%;"></canvas>
                                end
                                <button @click="openGenre = true" class="mt-4 w-full py-2 bg-white/5 hover:bg-white/10 rounded-xl text-xs font-medium transition text-gray-300">
                                    Lihat Detail Genre
                                </button>
                            </div>

                            {{-- Info Aktor, Sutradara, Era --}}
                            <div class="flex flex-col gap-4">
                                
                                {{-- Actors --}}
                                <div class="bg-black/30 rounded-2xl p-5 border border-white/5 relative">
                                    <h3 class="text-xs sm:text-sm font-semibold text-gray-400 mb-3">Top Actors</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(array_slice($actorsData, 0, 4, true) as $name => $percent)
                                            <span class="px-2.5 py-1.5 bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 rounded-full text-xs">
                                                {{ $name }} <span class="opacity-70 ml-1">{{ $percent }}%</span>
                                            </span>
                                        @endforeach
                                        @if(count($actorsData) == 0) <span class="text-xs text-gray-500">Belum ada data</span> @endif
                                    </div>
                                    @if(count($actorsData) > 4)
                                        <button @click="openActor = true" class="absolute top-4 right-4 text-xs text-cyan-500 hover:text-cyan-400">View All</button>
                                    @endif
                                </div>

                                {{-- Directors --}}
                                <div class="bg-black/30 rounded-2xl p-5 border border-white/5 relative">
                                    <h3 class="text-xs sm:text-sm font-semibold text-gray-400 mb-3">Top Directors</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(array_slice($directorsData, 0, 4, true) as $name => $percent)
                                            <span class="px-2.5 py-1.5 bg-purple-500/10 border border-purple-500/20 text-purple-400 rounded-full text-xs">
                                                {{ $name }} <span class="opacity-70 ml-1">{{ $percent }}%</span>
                                            </span>
                                        @endforeach
                                        @if(count($directorsData) == 0) <span class="text-xs text-gray-500">Belum ada data</span> @endif
                                    </div>
                                    @if(count($directorsData) > 4)
                                        <button @click="openDirector = true" class="absolute top-4 right-4 text-xs text-purple-500 hover:text-purple-400">View All</button>
                                    @endif
                                </div>

                                {{-- Era --}}
                                <div class="bg-black/30 rounded-2xl p-5 border border-white/5">
                                    <h3 class="text-xs sm:text-sm font-semibold text-gray-400 mb-3">Preferred Release Era</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(array_slice($erasData, 0, 4, true) as $eraName => $percent)
                                            <span class="px-3 py-1.5 bg-yellow-500/10 border border-yellow-500/20 text-yellow-500 rounded-full text-[11px] font-semibold uppercase tracking-wider">
                                                {{ $eraName }} <span class="opacity-70 ml-1 text-[10px]">{{ $percent }}%</span>
                                            </span>
                                        @endforeach
                                        @if(count($erasData) == 0) <span class="text-xs text-gray-500">Belum ada data</span> @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    @else
                        <div class="text-center py-10 bg-white/5 rounded-2xl border border-white/10 border-dashed px-4">
                            <p class="text-gray-400 text-sm">Data preferensimu belum cukup. Yuk mulai beraktivitas dan tambahkan film ke favorit!</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- 3. MODAL POP-UPS UNTUK DAFTAR PANJANG --}}
        <div x-show="openGenre" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
            <div class="bg-[#2a2a2a] rounded-2xl shadow-xl w-full max-w-lg max-h-[80vh] flex flex-col border border-white/10 m-4" @click.away="openGenre = false">
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                    <h2 class="text-base sm:text-lg font-bold text-white">Semua Genre (Persentase Kecocokan)</h2>
                    <button @click="openGenre = false" class="text-gray-400 hover:text-white transition">✕</button>
                </div>
                <div class="p-6 overflow-y-auto scrollbar-hide">
                    <ul class="space-y-3">
                        @foreach($userGenres as $index => $ug)
                            <li class="flex justify-between items-center text-sm bg-black/20 p-3 rounded-lg border border-white/5">
                                <span class="text-gray-300 font-medium">{{ $ug->genre ? $ug->genre->name : 'Unknown' }}</span>
                                <span class="text-cyan-400 font-bold bg-cyan-400/10 px-2 py-1 rounded">Score: {{ $genreWeights[$index] }}%</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div x-show="openActor" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
            <div class="bg-[#2a2a2a] rounded-2xl shadow-xl w-full max-w-lg max-h-[80vh] flex flex-col border border-white/10 m-4" @click.away="openActor = false">
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                    <h2 class="text-base sm:text-lg font-bold text-white">Daftar Semua Aktor Favorit</h2>
                    <button @click="openActor = false" class="text-gray-400 hover:text-white transition">✕</button>
                </div>
                <div class="p-6 overflow-y-auto scrollbar-hide flex flex-wrap gap-2">
                    @foreach($actorsData as $name => $percent)
                        <span class="px-3 py-1.5 bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 rounded-lg text-sm">
                            {{ $name }} <span class="opacity-70 ml-1">{{ $percent }}%</span>
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <div x-show="openDirector" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
            <div class="bg-[#2a2a2a] rounded-2xl shadow-xl w-full max-w-lg max-h-[80vh] flex flex-col border border-white/10 m-4" @click.away="openDirector = false">
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                    <h2 class="text-base sm:text-lg font-bold text-white">Daftar Semua Sutradara Favorit</h2>
                    <button @click="openDirector = false" class="text-gray-400 hover:text-white transition">✕</button>
                </div>
                <div class="p-6 overflow-y-auto scrollbar-hide flex flex-wrap gap-2">
                    @foreach($directorsData as $name => $percent)
                        <span class="px-3 py-1.5 bg-purple-500/10 border border-purple-500/20 text-purple-400 rounded-lg text-sm">
                            {{ $name }} <span class="opacity-70 ml-1">{{ $percent }}%</span>
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('avatar-input').addEventListener('change', function() {
            if (this.files && this.files[0]) document.getElementById('avatar-form').submit();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('genreChart');
            if(ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(array_slice($genreLabels ?? [], 0, 5)) !!},
                        datasets: [{
                            label: 'Affinity Score (%)',
                            data: {!! json_encode(array_slice($genreWeights ?? [], 0, 5)) !!},
                            backgroundColor: 'rgba(6, 182, 212, 0.4)',
                            borderColor: 'rgba(6, 182, 212, 1)',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { 
                                beginAtZero: true,
                                max: {{ !empty($genreWeights) ? min(100, round(max($genreWeights) * 1.05)) : 100 }},                               
                                grid: { color: 'rgba(255,255,255,0.05)' },
                                ticks: { color: '#9ca3af', callback: function(value) { return value + "%" } }
                            },
                            y: { 
                                grid: { display: false },
                                ticks: { color: '#d1d5db', font: { weight: 'bold' } }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) { return context.raw + '% Match'; }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>