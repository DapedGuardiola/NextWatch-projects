<x-app-layout>
    <x-slot name="title">{{ __('Discover') }}</x-slot>

    <div class="bg-[#020817] text-white min-h-screen overflow-hidden">

        {{-- HERO HEADER --}}
        <section class="relative overflow-hidden">

            {{-- Background blur dari poster film pertama --}}
            @if($movies->isNotEmpty())
            <div class="absolute inset-0 h-[420px] bg-cover bg-center blur-2xl scale-110"
                style="background-image:url('{{ $movies->first()->poster_url }}')">
            </div>
            @endif

            {{-- Overlay --}}
            <div class="absolute inset-0 h-[420px] bg-gradient-to-br from-[#020817]/95 via-[#020817]/80 to-cyan-950/30"></div>

            {{-- Bottom fade --}}
            <div class="absolute bottom-0 left-0 w-full h-[200px] bg-gradient-to-b from-transparent to-[#020817]"></div>

            {{-- Glow --}}

            {{-- Header content --}}
            <div class="relative z-10 max-w-[90%] mx-auto px-6 pt-32 pb-4">
                <h1 class="text-4xl lg:text-5xl font-black leading-none tracking-tight">
                    Discover
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-cyan-200">Films</span>
                </h1>
                <p class="text-gray-400 mt-2 text-sm">
                    {{ $movies->count() }} films found based on your filter
                </p>
            </div>

        </section>
        {{-- MOVIE GRID --}}
        <div class="relative z-10 bg-[#020817]">
            <div class="absolute z-1000 -top-[160px] -right-20 w-[350px] h-[350px] bg-cyan-500/15 rounded-full blur-3xl"></div>
            <div class="max-w-[90%] mx-auto px-6 pt-4 pb-12">

                @if($movies->isNotEmpty())
                <div class="grid grid-cols-5 justify-center gap-4">
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
                @else
                {{-- Empty state --}}
                <div class="flex flex-col items-center justify-center py-32 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">No films found</h3>
                    <p class="text-gray-500 text-sm mb-8">Try adjusting your filter to find more films</p>
                </div>
                @endif

            </div>
        </div>

    </div>
</x-app-layout>