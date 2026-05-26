<x-app-layout>
    <x-slot name="title">
        Watchlist - NextWatch
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-12 flex flex-row gap-12 text-gray-200">
        
        <div class="w-1/4 flex flex-col gap-6 font-medium text-center mt-14">
            <a href="{{ route('profile.index') }}" class="text-gray-300 hover:text-white transition">User Profile</a>
            <a href="{{ route('profile.settings') }}" class="text-gray-300 hover:text-white transition">Account Settings</a>
            
            <a href="{{ route('profile.persona') }}" class="text-gray-300 hover:text-white transition">Edit Persona</a>
            
            <a href="{{ route('favorites.index') }}" class="text-gray-300 hover:text-white transition">Favorite Movies</a>
            
            <a href="{{ route('watchlist.index') }}" class="bg-white text-black py-2 px-4 rounded-xl shadow-lg font-semibold">Watchlist</a>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <button type="submit" class="text-red-600 hover:text-red-500 font-bold transition">Sign Out</button>
            </form>
        </div>

        <div class="w-3/4">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-white tracking-wide">Watch List</h2>
                <button class="text-white text-3xl font-light hover:text-gray-400 transition">+</button>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-10">
                @forelse($watchlists as $item)
                    @if($item->movie)
                    <div class="group flex flex-col items-center cursor-pointer">
                        <div class="relative w-full aspect-[2/3] rounded-2xl overflow-hidden shadow-lg border border-white/5 transition duration-300 group-hover:-translate-y-2 group-hover:shadow-2xl">
                            <img src="{{ $item->movie->poster_path ? 'https://image.tmdb.org/t/p/w500' . $item->movie->poster_path : asset('images/no-poster.jpg') }}" 
                                 alt="{{ $item->movie->title }}"
                                 class="w-full h-full object-cover">
                            
                            <div class="absolute top-3 left-1/2 -translate-x-1/2 bg-[#FFB800] text-black text-[11px] font-bold px-3 py-1 rounded-full backdrop-blur-md">
                                {{ $item->movie->genres->first()->genre->name ?? 'Movie' }}
                            </div>
                        </div>
                        <h3 class="mt-4 text-center text-sm font-medium text-gray-200 line-clamp-1 px-2">
                            {{ $item->movie->title }}
                        </h3>
                    </div>
                    @endif
                @empty
                    <div class="col-span-4 text-center py-20 text-gray-500">
                        <p>Your watch list is empty.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>