<x-app-layout>
    <x-slot name="title">Search: {{ $query }}</x-slot>

    <div class="min-h-screen pt-20 pb-12" style="background-color: #2a2a2a;">

        {{-- Search Bar --}}

        <div class="max-w-[90%] mx-auto mb-1">
            <x-movie.search-bar />
        </div>

        {{-- Header --}}
        <div class="max-w-7xl mx-auto mb-4">
            <h1 class="text-2xl font-bold text-white">
                Results for <span class="text-indigo-400">"{{ $query }}"</span>
            </h1>
        </div>

        <div class="px-10 space-y-10">

            {{-- Exact Match --}}
            @if($movies->isNotEmpty())
            @php $firstMovie = $movies->first(); @endphp

            <div class="relative overflow-hidden rounded-2xl border border-white/10">
                <div class="absolute inset-0 bg-cover bg-center blur-2xl scale-110"
                    style="background-image:url('{{ $firstMovie->poster_url }}')"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-[#2a2a2a]/95 via-[#2a2a2a]/80 to-transparent"></div>

                <div class="relative z-10 p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-[200px_1fr_1fr] gap-6 items-center">

                        {{-- Kolom 1: Poster --}}
                        <div class="relative self-start pl-14">
                            <div class="absolute inset-0 bg-indigo-400/20 blur-2xl rounded-2xl scale-90"></div>
                            <img src="{{ $firstMovie->poster_url }}"
                                alt="{{ $firstMovie->title }}"
                                class="relative z-10 w-full rounded-2xl shadow-xl border border-white/10 object-cover">
                        </div>

                        {{-- Kolom 2: Info --}}
                        <div class="flex flex-col gap-3">
                            <p class="text-xs text-indigo-400 uppercase tracking-widest font-semibold">Best Match</p>
                            <h1 class="text-2xl lg:text-3xl font-black leading-tight tracking-tight text-white">
                                {{ $firstMovie->title }}
                            </h1>
                            <div class="flex flex-wrap gap-2">
                                @if($firstMovie->release_date)
                                <span class="px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-xs text-white/70">
                                    {{ \Carbon\Carbon::parse($firstMovie->release_date)->format('Y') }}
                                </span>
                                @endif
                                @if($firstMovie->rating)
                                <span class="px-3 py-1 rounded-lg bg-yellow-500/10 border border-yellow-400/20 text-yellow-300 text-xs">
                                    ⭐ {{ $firstMovie->rating }}
                                </span>
                                @endif
                            </div>
                            <a href="{{ route('movie.detail', $firstMovie->tmdb_movie_id) }}"
                                class="mt-auto self-start px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition">
                                View Details →
                            </a>
                        </div>

                        {{-- Kolom 3: Overview --}}
                        @if($firstMovie->overview)
                        <div class="border-l border-white/10 pl-6 pr-14 self-stretch flex items-center">
                            <p class="text-sm text-white/55 leading-relaxed line-clamp-6">
                                {{ $firstMovie->overview }}
                            </p>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
            @endif

            {{-- Similar + Actors --}}
            <div class="flex gap-10">

                {{-- Similar Movies --}}
                <div class="flex-[3]">
                    <h2 class="text-2xl font-bold text-white mb-6">Similar Films</h2>
                    <div class="flex flex-col gap-4">
                        @forelse($movies->slice(1, 8) as $movie)
                            <a href="{{ route('movie.detail', $movie->tmdb_movie_id) }}"
                                class="flex items-center gap-5 p-4 rounded-2xl hover:bg-white/5 transition">
                                <img src="{{ $movie->poster_url }}"
                                    class="w-16 h-24 object-cover rounded-xl flex-shrink-0"
                                    alt="{{ $movie->title }}">
                                <div class="flex flex-col gap-2 overflow-hidden">
                                    <p class="text-base font-semibold text-white truncate">{{ $movie->title }}</p>
                                    <div class="flex items-center gap-3">
                                        @if($movie->release_date)
                                        <span class="text-sm text-white/40">
                                            {{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}
                                        </span>
                                        @endif
                                        @if($movie->rating)
                                        <span class="text-sm text-yellow-400">⭐ {{ $movie->rating }}</span>
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
                <div class="flex-[1]">
                    <h2 class="text-2xl font-bold text-white mb-6">Similar Name Actors</h2>
                    <div class="flex flex-col gap-4">
                        @forelse($actors as $actor)
                            <a href="{{ route('actor.detail', $actor->tmdb_actor_id) }}"
                                class="flex items-center gap-4 p-4 rounded-2xl hover:bg-white/5 transition">
                                <img src="{{ $actor->image_url }}"
                                    class="w-14 h-14 object-cover rounded-full flex-shrink-0"
                                    alt="{{ $actor->name }}">
                                <p class="text-base text-white truncate">{{ $actor->name }}</p>
                            </a>
                        @empty
                            <div class="flex items-center gap-3 px-4 py-6 rounded-2xl border border-white/5 bg-white/[0.02]">
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