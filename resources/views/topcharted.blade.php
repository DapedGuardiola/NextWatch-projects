<x-app-layout>
    <x-slot name="title">
        {{ __('Top Charted Movies') }}
    </x-slot>
    <div>
        <div class="mx-10 mt-[75px] mb-5">
            <h1>
                <p class="text-3xl text-white font-bold">All Time Best Movies</p>
            </h1>
        </div>
        <div class="mx-10 mb-5">
            <x-movie.popular-carousel :movies="$popularMovies" />
        </div>        
        
        @foreach($moviesByGenre as $genre => $movies)
            <div class="text-white mx-10">
                <h2>
                    <p class="text-3xl text-white font-bold">{{ $genre }}</p>
                </h2>
            </div>
            <div class="flex gap-4 py-4 px-10 w-[90%] mx-auto overflow-x-auto scrollbar-hide">
                @foreach($movies as $index => $movie)
                    <x-movie.topmovies-modal>
                        <x-slot name="poster">
                            {{ $movie['poster_path'] }}
                        </x-slot>
                        <x-slot name="title">
                            {{ $movie['title'] }}
                        </x-slot>
                        <x-slot name="rank">
                            {{ $index + 1 }}
                        </x-slot>
                    </x-movie.topmovies-modal>
                @endforeach
            </div>
            @endforeach
    </div>

</x-app-layout>