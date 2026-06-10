<x-app-layout>
    <x-slot name="title">
        {{ __('Top Charted Movies') }}
    </x-slot>
    <div>
        <div class="mx-4 md:mx-8 lg:mx-10 mt-[75px] mb-5">
            <h1>
                <p class="text-2xl md:text-3xl text-white font-bold">You Must Know These Movies!!</p>
            </h1>
        </div>
        <div class="mx-4 md:mx-8 lg:mx-10 mb-5">
            <x-movie.popular-carousel :movies="$popularMovies" />
        </div>        
        
        @foreach($moviesByGenre as $genre => $movies)
            <div class="text-white mx-4 md:mx-8 lg:mx-10">
                <h2>
                    <p class="text-xl sm:text-3xl text-white font-bold">{{ $genre }}</p>
                </h2>
            </div>
            <div class="flex gap-4 md:gap-8 max-w-[90%] mx-auto overflow-hidden overflow-x-auto scrollbar-hide">
                @foreach($movies as $index => $movie)
                    <x-movie.topmovies-modal
                        :poster="'https://image.tmdb.org/t/p/original/' . $movie['poster_path']"
                        :title="$movie['title']"
                        :tmdb_movie_id="$movie['id']"
                        :year="$movie['year'] ?? null"
                        :rating="$movie['rating'] ?? null"
                        :overview="$movie['overview'] ?? null"
                        :genres="$movie['genres'] ?? []"
                        :duration="$movie['runtime'] ?? null" 
                        :rank="$index + 1" />
                @endforeach
            </div>
        @endforeach
    </div>
</x-app-layout>