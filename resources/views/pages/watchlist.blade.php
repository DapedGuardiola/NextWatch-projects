<x-app-layout>
    <x-slot name="title">
        {{ __('Watchlist - NextWatch') }}
    </x-slot>

    @php
        $avatarUrl = auth()->user()->avatar 
            ? asset('storage/' . auth()->user()->avatar) 
            : "https://ui-avatars.com/api/?name=" . urlencode(auth()->user()->name) . "&background=333&color=fff";
    @endphp

    <div class="relative min-h-screen w-full overflow-hidden text-gray-200">
        <div class="absolute inset-0 bg-cover bg-center z-0" 
             style="background-image: url('{{ $avatarUrl }}'); filter: blur(60px); transform: scale(1.2);">
        </div>
        <div class="absolute inset-0 bg-black/70 z-0"></div>

        <div class="relative z-10 flex h-full max-w-7xl mx-auto p-6">
            
            {{-- SIDEBAR KIRI --}}
            <div class="w-1/4 pr-8 flex flex-col gap-6 mt-20 font-semibold">
                <a href="{{ route('profile.index') }}" class="text-gray-400 hover:text-white text-center transition">User Profile</a>
                <a href="{{ route('profile.settings') }}" class="text-gray-400 hover:text-white text-center transition">Account Settings</a>
                <a href="{{ route('profile.persona') }}" class="text-gray-400 hover:text-white text-center transition">Edit Persona</a>
                <a href="{{ route('favorites.index') }}" class="text-gray-400 hover:text-white text-center transition">Favorite Movies</a>
                
                <a href="{{ route('watchlist.index') }}" class="bg-white text-black py-2 px-4 rounded-xl text-center shadow-lg">Watchlist</a>
                
                <form method="POST" action="{{ route('logout') }}" class="mt-12">
                    @csrf
                    <button type="submit" class="text-red-600 font-bold w-full text-center hover:text-red-500 transition">Sign Out</button>
                </form>
            </div>

            {{-- MAIN CONTENT KANAN --}}
            <div class="w-3/4 mt-20 mb-20">
                <div class="bg-black/40 backdrop-blur-md rounded-[2rem] p-10 shadow-2xl relative z-20 border border-white/5">
                    
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-3xl font-bold text-white tracking-wide">My Watchlist</h2>
                        <a href="{{ route('dashboard.discover') }}" title="Discover more movies" class="text-white text-4xl font-light hover:text-cyan-400 hover:scale-110 transition cursor-pointer">
                            +
                        </a>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse($watchlists as $item)
                            @if($item->movie)
                            <a href="{{ route('movie.detail', $item->movie->tmdb_movie_id) }}" class="group flex flex-col items-center cursor-pointer">
                                <div class="relative w-full aspect-[2/3] rounded-2xl overflow-hidden shadow-lg border border-white/5 transition duration-300 group-hover:-translate-y-2 group-hover:shadow-2xl">
                                    <img src="{{ $item->movie->poster_path ? 'https://image.tmdb.org/t/p/w500/' . ltrim($item->movie->poster_path, '/') : asset('images/no-poster.jpg') }}"                                         alt="{{ $item->movie->title }}"
                                         class="w-full h-full object-cover transition duration-300 group-hover:scale-110">
                                    
                                    <div class="absolute top-3 left-1/2 -translate-x-1/2 bg-[#FFB800] text-black text-[11px] font-bold px-3 py-1 rounded-full backdrop-blur-md shadow-md">
                                        {{ $item->movie->genres->first()->genre->name ?? 'Movie' }}
                                    </div>
                                </div>
                                <h3 class="mt-4 text-center text-sm font-medium text-gray-200 line-clamp-1 px-2 group-hover:text-white transition">
                                    {{ $item->movie->title }}
                                </h3>
                            </a>
                            @endif
                        @empty
                            <div class="col-span-4 text-center py-20 text-gray-500 bg-white/5 rounded-xl border border-white/5 border-dashed">
                                <p>Your watchlist is empty. Add some movies to watch later!</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>