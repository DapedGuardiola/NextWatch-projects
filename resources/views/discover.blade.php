<x-app-layout>
    <x-slot name="title">{{ __('Discover') }}</x-slot>

    <div class="min-h-screen px-10 pt-56 pb-12" style="background: linear-gradient(135deg, #2a2a2a 0%, #494949 50%, #3a3a3a 100%);">

        {{-- Header --}}
        <div class="mb-24">
            <h1 class="text-4xl font-bold text-white leading-tight">Discover <span class="text-indigo-400">Films</span></h1>
            <p class="text-gray-400 text-sm mt-2">Find your next favorite movie</p>

            {{-- Divider --}}
            <div class="mt-6 h-px bg-gradient-to-r from-indigo-500/50 via-gray-500/20 to-transparent"></div>
        </div>

        {{-- Grid Movie --}}
        <div class="flex flex-wrap gap-4">
            @forelse($movies as $movie)
                <x-movie.movie-modal
                :poster="$movie->poster_url"
                :title="$movie->title"
                :tmdb_movie_id="$movie->tmdb_movie_id"
                :year="$movie->year ?? null"
                :rating="$movie->rating ?? null"
                :overview="$movie->overview ?? null"
                :genres="$movie->genres->pluck('genre.name')->filter()->toArray() ?? []"
                :duration="$movie->runtime ?? null" />
            @empty
                <p class="text-gray-400">Tidak ada film ditemukan.</p>
            @endforelse
        </div>

    </div>
</x-app-layout>