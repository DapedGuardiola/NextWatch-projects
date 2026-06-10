<x-app-layout>
    <x-slot name="title">
        {{ __('Edit Persona - NextWatch') }}
    </x-slot>

    @php
        $avatarUrl = auth()->user()->avatar 
            ? asset('storage/' . auth()->user()->avatar) 
            : "https://ui-avatars.com/api/?name=" . urlencode(auth()->user()->name) . "&background=333&color=fff";
    @endphp

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <div x-data="{ 
        showModal: false, 
        selectedGenres: {{ json_encode($myGenres ?? []) }},
        toggleGenre(id) {
            if (this.selectedGenres.includes(id)) {
                this.selectedGenres = this.selectedGenres.filter(g => g !== id);
            } else {
                if (this.selectedGenres.length < 4) this.selectedGenres.push(id);
            }
        }
    }">
        <div class="relative min-h-screen md:h-screen w-full overflow-hidden text-gray-200">
            <div class="absolute inset-0 bg-cover bg-center z-0" 
                 style="background-image: url('{{ $avatarUrl }}'); filter: blur(60px); transform: scale(1.2);">
            </div>
            <div class="absolute inset-0 bg-black/70 z-0"></div>

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

                {{-- MAIN CONTENT KANAN --}}
                <div class="w-full md:w-3/4 h-full overflow-y-auto scrollbar-hide pb-32 z-20">
                    <div class="bg-black/40 backdrop-blur-md rounded-2xl md:rounded-[2rem] p-6 md:p-10 shadow-2xl relative z-20 border border-white/5 mb-10">
                        
                        <div class="mb-6 md:mb-8">
                            <h2 class="text-2xl md:text-3xl font-bold text-white tracking-wide">Edit Your Persona</h2>
                            <p class="text-sm md:text-base text-gray-400 mt-2">Perbarui preferensi film dan genre favoritmu di sini agar rekomendasi "For You" semakin akurat.</p>
                        </div>

                        {{-- SEKSI 1: FAVORITE GENRES --}}
                        <div class="mb-8">
                            <h3 class="text-lg md:text-xl font-semibold text-white mb-4">Your Favorite Genres</h3>
                            <div class="flex flex-wrap gap-2 md:gap-3 items-center">
                                @forelse($allGenres as $g)
                                    @if(in_array($g->map_id, $myGenres))
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 rounded-full text-xs md:text-sm shadow-sm backdrop-blur-sm group">
                                            {{ $g->name }}
                                            <form action="{{ route('profile.persona.genres.destroy', $g->map_id) }}" method="POST" class="inline-flex items-center">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-cyan-500 hover:text-red-400 focus:outline-none transition-colors" title="Hapus {{ $g->name }}">
                                                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </span>
                                    @endif
                                @empty
                                    <span class="text-gray-500 text-xs md:text-sm italic">Belum ada genre dipilih.</span>
                                @endforelse

                                <button @click="showModal = true" type="button" class="px-3 py-1.5 md:px-4 md:py-2 bg-white/5 text-gray-300 border border-white/10 rounded-full text-xs md:text-sm hover:bg-white/10 transition cursor-pointer">
                                    + Edit Genre
                                </button>
                            </div>
                        </div>

                        {{-- SEKSI 2: MOVIES YOU LOVE (PERSONA FILM) --}}
                        <div>
                            <h3 class="text-lg md:text-xl font-semibold text-white mb-4">Movies You Love (Persona)</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @forelse($personaMovies as $item)
                                    @if($item->movie)
                                    <div class="relative rounded-xl overflow-hidden aspect-[2/3] border border-white/10 group">
                                        <img src="{{ $item->movie->poster_path ? 'https://image.tmdb.org/t/p/w500' . $item->movie->poster_path : asset('images/no-poster.jpg') }}" 
                                             class="w-full h-full object-cover transition duration-300 group-hover:scale-110">
                                    </div>
                                    @endif
                                @empty
                                    <div class="col-span-2 md:col-span-4 text-center py-6 md:py-10 text-gray-500 bg-white/5 rounded-xl border border-white/5 border-dashed">
                                        <p class="text-sm md:text-base">Belum ada film persona. Pilih beberapa film favoritmu!</p>
                                    </div>
                                @endforelse
                            </div>

                            <form action="{{ route('profile.persona.update') }}" method="POST">
                                @csrf
                                <div class="mt-6 md:mt-8 flex justify-end">
                                    <button type="submit" class="bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500 text-black font-bold py-2.5 px-6 md:py-3 md:px-8 rounded-full shadow-lg transition text-sm md:text-base w-full sm:w-auto">
                                        Save Persona
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                {{-- MODAL POP-UP EDIT GENRE --}}
                <div x-show="showModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
                    <div class="bg-[#494949] rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.away="showModal = false">
                        <div class="flex items-center justify-between px-5 py-4 md:px-6 md:py-5 border-b border-[#3f3f3f]">
                            <div>
                                <h2 class="text-base md:text-lg font-semibold text-white tracking-wide">Edit Favorite Genres</h2>
                                <p class="text-[10px] md:text-xs text-gray-400 mt-0.5">Pilih maksimal 4 genre yang paling kamu sukai.</p>
                            </div>
                            <button @click="showModal = false" class="p-1.5 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form action="{{ route('profile.persona.genres') }}" method="POST">
                            @csrf
                            <div class="p-5 md:p-6">
                                <div class="flex flex-wrap gap-2 mb-6 md:mb-8">
                                    @foreach($allGenres as $genre)
                                        <input type="checkbox" name="genres[]" value="{{ $genre->map_id }}" id="g{{ $genre->map_id }}" class="hidden peer" 
                                               :checked="selectedGenres.includes({{ $genre->map_id }})"
                                               @click="toggleGenre({{ $genre->map_id }})">
                                        <label for="g{{ $genre->map_id }}" 
                                               :class="selectedGenres.includes({{ $genre->map_id }}) ? 'bg-indigo-600 border-indigo-600 text-white' : (selectedGenres.length >= 4 ? 'bg-[#333333] text-[#5a5a5a] border-[#333333] cursor-not-allowed' : 'bg-[#5a5a5a] text-[#d4d4d8] border-[#3f3f3f] hover:border-indigo-400 hover:text-white')"
                                               class="px-3 py-1.5 md:px-4 md:py-2 rounded-full text-xs md:text-sm border cursor-pointer transition select-none">
                                            {{ $genre->name }}
                                        </label>
                                    @endforeach
                                </div>

                                <div class="flex justify-end gap-3 pt-4 border-t border-[#3f3f3f]">
                                    <button type="button" @click="showModal = false" class="px-4 py-2 md:px-6 md:py-2 text-gray-400 hover:text-white transition text-sm">Cancel</button>
                                    <button type="submit" class="px-4 py-2 md:px-6 md:py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition text-sm">Save Genres</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>