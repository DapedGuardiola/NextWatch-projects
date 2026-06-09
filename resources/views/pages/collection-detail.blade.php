<x-app-layout>
    <x-slot name="title">
        {{ __($collection->name) }}
    </x-slot>

    <div class="min-h-screen relative bg-[#020817]">
        <img src="{{ $collection->backdrop_url }}" alt="{{ $collection->name }}"
            class="absolute inset-0 object-cover object-center w-full h-full overflow-hidden opacity-30 pointer-events-none">

        <div class="relative min-h-screen bg-gradient-to-b from-transparent via-[#020817]/40 via-25% via-[#020817]/80 via-50% to-[#020817] to-100% pt-12">

            {{-- Info Koleksi --}}
            <div class="px-4 sm:px-10 lg:px-20 max-w-5xl mb-6 sm:mb-10">
                <div class="font-bold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight">
                    {{ $collection->name }}
                </div>

                @if($collection->overview)
                    <p class="text-gray-300 mt-3 sm:mt-4 text-xs sm:text-sm leading-relaxed
                        bg-black/40 border border-white/5 p-3 sm:p-5 rounded-2xl backdrop-blur-md">
                        {{ $collection->overview }}
                    </p>
                @endif

                <div class="font-bold text-base sm:text-lg lg:text-xl text-white mt-6 sm:mt-10">
                    Movies in {{ $collection->name }}
                </div>
            </div>

            {{-- Grid Film --}}
            <div class="max-w-full mx-auto px-10 overflow-x-auto justify-items-center scrollbar-hide">
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5
                    gap-3 sm:gap-6 md:gap-8
                    w-full max-w-[95%] sm:max-w-[90%] mx-auto justify-items-center">
                    @foreach($collection->movies as $movie)
                        <x-movie.movie-modal
                            :poster="$movie->poster_url"
                            :title="$movie->title"
                            :tmdb_movie_id="$movie->tmdb_movie_id"
                            :year="$movie->release_date ? date('Y', strtotime($movie->release_date)) : null"
                            :rating="$movie->rating ?? null"
                            :overview="$movie->overview ?? null"
                            :genres="$movie->genres->pluck('genre.name')->filter()->toArray() ?? []"
                            :duration="$movie->runtime ?? null" />
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>