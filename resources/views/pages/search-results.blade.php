<x-app-layout>
    <x-slot name="title">Search: {{ $query }}</x-slot>

    <div class="min-h-screen pt-20 pb-12" style="background-color: #2a2a2a;">

        {{-- Search Bar --}}
        <div class="max-w-xl mx-auto px-4 mb-6">
            <x-movie.search-bar />
        </div>

        {{-- Header --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 mb-4">
            <h1 class="text-xl sm:text-2xl font-bold text-white">
                Results for <span class="text-indigo-400">"{{ $query }}"</span>
            </h1>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 space-y-8">

            {{-- Best Match --}}
            @if($movies->isNotEmpty())
            @php $firstMovie = $movies->first(); @endphp

            <div class="relative overflow-hidden rounded-2xl border border-white/10">
                <div class="absolute inset-0 bg-cover bg-center blur-2xl scale-110"
                    style="background-image:url('{{ $firstMovie->poster_url }}')"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-[#2a2a2a]/95 via-[#2a2a2a]/80 to-transparent"></div>

                <div class="relative z-10 p-5 sm:p-8">
                    {{-- Poster + Info --}}
                    <div class="flex gap-4 sm:grid sm:grid-cols-[140px_1fr] lg:grid-cols-[160px_1fr_1fr] sm:gap-6 items-start">

                        {{-- Poster --}}
                        <div class="relative w-20 sm:w-full flex-shrink-0 self-start">
                            <div class="absolute inset-0 bg-indigo-400/20 blur-2xl rounded-2xl scale-90"></div>
                            <img src="{{ $firstMovie->poster_url }}"
                                alt="{{ $firstMovie->title }}"
                                class="relative z-10 w-full rounded-xl sm:rounded-2xl shadow-xl border border-white/10 object-cover">
                        </div>

                        {{-- Info: label + judul + badge --}}
                        <div class="flex flex-col gap-2 sm:gap-3 min-w-0">
                            <p class="text-xs text-indigo-400 uppercase tracking-widest font-semibold">Best Match</p>
                            <h2 class="text-base sm:text-2xl lg:text-3xl font-black leading-tight tracking-tight text-white">
                                {{ $firstMovie->title }}
                            </h2>
                            <div class="flex flex-wrap gap-2">
                                @if($firstMovie->release_date)
                                <span class="px-2 py-0.5 sm:px-3 sm:py-1 rounded-lg bg-white/5 border border-white/10 text-xs text-white/70">
                                    {{ \Carbon\Carbon::parse($firstMovie->release_date)->format('Y') }}
                                </span>
                                @endif
                                @if($firstMovie->rating)
                                <span class="px-2 py-0.5 sm:px-3 sm:py-1 rounded-lg bg-yellow-500/10 border border-yellow-400/20 text-yellow-300 text-xs">
                                    ⭐ {{ $firstMovie->rating }}
                                </span>
                                @endif
                            </div>

                            @if($firstMovie->overview)
                            <a href="{{ route('movie.detail', $firstMovie->tmdb_movie_id) }}"
                                class="hidden sm:inline-flex mt-2 self-start px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition">
                                View Details →
                            </a>
                            @endif
                        </div>

                        {{-- Overview (desktop) --}}
                        @if($firstMovie->overview)
                        <div class="hidden lg:flex border-l border-white/10 pl-6 pr-6 self-stretch items-center">
                            <p class="text-sm text-white/55 leading-relaxed line-clamp-6">
                                {{ $firstMovie->overview }}
                            </p>
                        </div>
                        @endif

                    </div>

                    {{-- Overview + tombol khusus mobile --}}
                    @if($firstMovie->overview)
                    <div class="flex flex-col gap-3 mt-3 sm:hidden">
                        <p class="text-xs text-white/55 leading-relaxed line-clamp-4">
                            {{ $firstMovie->overview }}
                        </p>
                        <a href="{{ route('movie.detail', $firstMovie->tmdb_movie_id) }}"
                            class="self-start px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition">
                            View Details →
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Similar Films + Actors --}}
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">

                {{-- Similar Films --}}
                <div class="flex-[3] min-w-0">
                    <h2 class="text-lg sm:text-2xl font-bold text-white mb-4 sm:mb-6">Similar Films</h2>
                    <div class="flex flex-col gap-2 sm:gap-4">
                        @forelse($movies->slice(1, 8) as $movie)
                            <a href="{{ route('movie.detail', $movie->tmdb_movie_id) }}"
                                class="flex items-center gap-3 sm:gap-5 p-3 sm:p-4 rounded-2xl hover:bg-white/5 transition">
                                <img src="{{ $movie->poster_url }}"
                                    class="w-10 h-[60px] sm:w-14 sm:h-20 lg:w-16 lg:h-24 object-cover rounded-lg sm:rounded-xl flex-shrink-0"
                                    alt="{{ $movie->title }}">
                                <div class="flex flex-col gap-1 sm:gap-2 overflow-hidden">
                                    <p class="text-sm sm:text-base font-semibold text-white truncate">{{ $movie->title }}</p>
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        @if($movie->release_date)
                                        <span class="text-xs sm:text-sm text-white/40">
                                            {{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}
                                        </span>
                                        @endif
                                        @if($movie->rating)
                                        <span class="text-xs sm:text-sm text-yellow-400">⭐ {{ $movie->rating }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="flex items-center gap-3 px-4 py-6 rounded-2xl border border-white/5 bg-white/[0.02]">
                                <span class="text-2xl">🎬</span>
                                <div>
                                    <p class="text-sm font-medium text-white/40">Tidak ada film serupa</p>
                                    <p class="text-xs text-white/20 mt-0.5">Coba kata kunci lain</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Actors --}}
                <div class="lg:flex-[1] lg:min-w-0">
                    <h2 class="text-lg sm:text-2xl font-bold text-white mb-4 sm:mb-6">Similar Name Actors</h2>

                    {{-- Mobile: horizontal scroll, Desktop: list --}}
                    <div class="flex lg:flex-col gap-3 overflow-x-auto pb-2 lg:overflow-x-visible lg:pb-0 lg:gap-4 scrollbar-hide">
                        @forelse($actors as $actor)
                            <a href="{{ route('actor.detail', $actor->tmdb_actor_id) }}"
                                class="flex lg:flex items-center gap-3 lg:gap-4 p-3 lg:p-4 rounded-2xl hover:bg-white/5 transition
                                       flex-col lg:flex-row flex-shrink-0 lg:flex-shrink w-24 lg:w-auto text-center lg:text-left">
                                <img src="{{ $actor->image_url }}"
                                    class="w-12 h-12 lg:w-14 lg:h-14 object-cover rounded-full flex-shrink-0 mx-auto lg:mx-0"
                                    alt="{{ $actor->name }}">
                                <p class="text-xs lg:text-base text-white truncate w-full">{{ $actor->name }}</p>
                            </a>
                        @empty
                            <div class="flex items-center gap-3 px-4 py-6 rounded-2xl border border-white/5 bg-white/[0.02] w-full">
                                <span class="text-2xl">🎭</span>
                                <div>
                                    <p class="text-sm font-medium text-white/40">Tidak ada aktor ditemukan</p>
                                    <p class="text-xs text-white/20 mt-0.5">Coba kata kunci lain</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Empty state --}}
            @if($movies->isEmpty() && $actors->isEmpty())
            <div class="text-center py-20">
                <p class="text-white/40 text-lg">No results found for "<span class="text-white">{{ $query }}</span>"</p>
                <p class="text-white/20 text-sm mt-2">Try searching with different keywords</p>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>