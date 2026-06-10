<x-app-layout>
    <x-slot name="title">
        {{ __('Watchlist - NextWatch') }}
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
                    
                    <div class="flex justify-between items-center mb-6 md:mb-8">
                        <h2 class="text-2xl md:text-3xl font-bold text-white tracking-wide">My Watchlist</h2>
                        <a href="{{ route('dashboard.discover') }}" title="Discover more movies" class="text-white text-3xl md:text-4xl font-light hover:text-cyan-400 hover:scale-110 transition cursor-pointer">
                            +
                        </a>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                        @forelse($watchlists as $item)
                            @if($item->movie)
                            <a href="{{ route('movie.detail', $item->movie->tmdb_movie_id) }}" class="group flex flex-col items-center cursor-pointer">
                                <div class="relative w-full aspect-[2/3] rounded-xl md:rounded-2xl overflow-hidden shadow-lg border border-white/5 transition duration-300 group-hover:-translate-y-2 group-hover:shadow-2xl">
                                    <img src="{{ $item->movie->poster_path ? 'https://image.tmdb.org/t/p/w500/' . ltrim($item->movie->poster_path, '/') : asset('images/no-poster.jpg') }}"                                         alt="{{ $item->movie->title }}"
                                         class="w-full h-full object-cover transition duration-300 group-hover:scale-110">
                                    
                                    <div class="absolute top-2 md:top-3 left-1/2 -translate-x-1/2 bg-[#FFB800] text-black text-[9px] md:text-[11px] font-bold px-2 md:px-3 py-1 rounded-full backdrop-blur-md shadow-md whitespace-nowrap max-w-[90%] overflow-hidden text-ellipsis">
                                        {{ $item->movie->genres->first()->genre->name ?? 'Movie' }}
                                    </div>
                                </div>
                                <h3 class="mt-3 md:mt-4 text-center text-xs md:text-sm font-medium text-gray-200 line-clamp-1 px-2 group-hover:text-white transition w-full">
                                    {{ $item->movie->title }}
                                </h3>
                            </a>
                            @endif
                        @empty
                            <div class="col-span-2 md:col-span-4 text-center py-12 md:py-20 text-gray-500 bg-white/5 rounded-xl border border-white/5 border-dashed">
                                <p class="text-sm md:text-base">Your watchlist is empty. Add some movies to watch later!</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>