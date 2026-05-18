<x-app-layout>
    <x-slot name="title">
        {{ __('Dashboard') }}
    </x-slot>
    <div class="h-screen relative">
        <img src="{{ $popularMovie->poster_url }}"
            class="w-full h-full object-cover object-center" alt="hero">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent from-20% via-[#212121]/75 via-70% to-[#212121] to-100%"></div>

        <!-- Konten di atas gambar -->
        <div class="absolute bottom-[25%] left-10">
            <h1 class="text-white text-7xl font-bold">{{ $popularMovie->title }}</h1>
        </div>
    </div>
    <div class="-mt-[200px] max-w [95%] left-0 right-0 relative z-10 ">
        <div class="mx-10 my-10">
            <h1>
                <p class="text-3xl text-white font-bold">Top On Its Genre</p>
            </h1>
        </div>
        <div class="flex gap-8 px-2 max-w-[90%] mx-auto overflow-hidden overflow-x-auto scrollbar-hide">
            @foreach($movies as $movie)
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

        <div class="mx-10 my-10">
            <h1>
                <p class="text-3xl text-white font-bold">Top On Its Genre</p>
            </h1>
        </div>
        <div class="flex gap-8 px-2 max-w-[90%] overflow-hidden mx-auto overflow-x-auto scrollbar-hide">
            @foreach($movies as $movie)
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
        <div class="mx-10 my-10">
            <h1>
                <p class="text-3xl text-white font-bold">Actors</p>
            </h1>
        </div>
        <div class="flex gap-12 px-2 max-w-[90%] overflow-hidden mx-auto overflow-x-auto scrollbar-hide">
            @foreach($actors as $actor)
            <x-movie.actor-card
                :actor_id="$actor->tmdb_actor_id"
                :image_url="$actor->image_url"
                :name="$actor->name"/>
            @endforeach
        </div>


</x-app-layout>