<x-app-layout>
    <div class="h-screen relative">
        <img src="{{ $collectionData->backdrop_url }}" alt="{{ $collectionData->name }}" class="absolute inset-0 object-cover object-center w-full h-full overflow-hidden">
        <div class="absolute backdrop-blur-3xl inset-0 bg-gradient-to-b from-transparent via-transparent from-0 via-[#212121]/20 via-25% via-[#212121]/50 via-50% via-[#212121]/70 via-75% to-[#212121] to-100%">
            <div class="ml-20 mt-12 font-bold text-2xl text-white">Movie in{{ $collectionData->name }} Collection</div>
            <div class="grid grid-cols-5 max-w-[90%] place-items-center gap-8 mt-10 mx-auto mb-10">
                @foreach($collectionData->movies as $movie)
                <x-movie.movie-modal
                    :poster="$movie->poster_url"
                    :title="$movie->title"
                    :tmdb_movie_id="$movie->tmdb_movie_id"
                    :year="$movie->year ?? null"
                    :rating="$movie->rating ?? null"
                    :overview="$movie->overview ?? null"
                    :genres="$movie->genres->pluck('genre.name')->filter()->toArray() ?? []"
                    :duration="$movie->runtime ?? null" />
                @endforeach
            </div>
        </div>

    </div>
</x-app-layout>