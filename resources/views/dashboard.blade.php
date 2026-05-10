<x-app-layout>
    <x-slot name="title">
        {{ __('Dashboard') }}
    </x-slot>
    <div class="h-screen relative">
        <img src="{{ asset('images/extraction.jpg') }}"
            class="w-full h-full object-cover" alt="hero">

        <!-- Konten di atas gambar -->
        <div class="absolute bottom-10 left-10">
            <h1 class="text-white text-5xl font-bold">Extraction</h1>
        </div>
    </div>
    <div>
        <div class="mx-10 my-5">
            <h1>
                <p class="text-2xl font-bold">Top On Its Genre</p>
            </h1>
        </div>

        <div class="flex gap-4 px-10 max-w-[1600px] mx-auto overflow-x-auto scrollbar-hide">
            @foreach($movies as $movie)
            <x-movie.movie-modal
                :poster="$movie->poster_path"
                :title="$movie->title"
                :tmdb_movie_id="$movie->tmdb_movie_id"
                :year="$movie->year ?? null"
                :rating="$movie->rating ?? null"
                :overview="$movie->overview ?? null"
                :genres="$movie->genres->pluck('genre.name')->filter()->toArray() ?? []"
                :duration="$movie->runtime ?? null" />
            @endforeach
        </div>
        <div class="h-100">
            <p>a</p>
        </div>


</x-app-layout>