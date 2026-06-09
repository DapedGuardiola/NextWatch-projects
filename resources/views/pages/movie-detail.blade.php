<x-app-layout>
    <x-slot name="title">
        {{ __($movie->title) }}
    </x-slot>
    <style>
        #youtube-player iframe {
            width: 100% !important;
            height: 500px !important;
            border-radius: 16px;
        }

        @media (max-width: 768px) {
            #youtube-player iframe {
                height: 220px !important;
                border-radius: 12px;
            }
        }
    </style>
    <div class="bg-[#020817] text-white overflow-hidden">

        <!-- HERO SECTION -->
        <section class="relative overflow-hidden" id="hero-section">

            <!-- BACKGROUND -->
            <div class="absolute inset-0 h-[1100px] bg-cover bg-center blur-2xl scale-110"
                style="background-image:url('https://image.tmdb.org/t/p/original/{{ $movie->backdrop_path ?? $movie->poster_path }}')">
            </div>

            <!-- OVERLAY -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#020817]/95 via-[#020817]/75 to-cyan-950/30"></div>

            <!-- SMOOTH GRADIENT -->
            <div class="absolute bottom-0 left-0 w-full h-[420px] bg-gradient-to-b from-transparent via-[#020817]/70 to-[#020817]"></div>

            <!-- GLOW -->
            <div class="absolute -top-32 -right-32 w-[400px] h-[400px] bg-cyan-500/20 rounded-full blur-3xl"></div>

            <div class="relative z-10 max-w-[90%] mx-auto px-4 md:px-6 py-8 md:py-14">

                {{-- MOBILE LAYOUT --}}
                <div class="flex flex-col gap-6 pt-8 md:hidden">

                    {{-- 1. YouTube Player --}}
                    @if($movie->trailer_key)
                    <div id="youtube-player-mobile" class="w-full rounded-2xl overflow-hidden"></div>
                    @else
                    <div class="flex items-center justify-center h-[220px] rounded-2xl border border-white/10 bg-white/5">
                        <p class="text-gray-400 text-sm">Trailer tidak tersedia</p>
                    </div>
                    @endif

                    {{-- 2. Poster + Meta --}}
                    <div class="flex gap-4 items-start">
                        <div class="w-28 flex-shrink-0">
                            <img src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_path }}"
                                alt="{{ $movie->title }}"
                                class="w-full rounded-2xl shadow-xl border border-white/10 object-cover aspect-[4/6]">
                        </div>
                        <div class="flex-1 flex flex-col gap-2 pt-1">
                            <h1 class="text-2xl font-black leading-tight tracking-tight">{{ $movie->title }}</h1>
                            {{-- Meta badges --}}
                            <div class="flex flex-wrap items-center gap-1.5 text-xs">
                                <div class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-yellow-500/10 border border-yellow-400/20 text-yellow-300">
                                    <span class="font-black">IMDb</span>
                                    <span class="text-white font-semibold">{{ $movie->rating }}</span>
                                </div>
                                <div class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-gray-200">
                                    {{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}
                                </div>
                                <div class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-gray-200">
                                    {{ $movie->runtime }} min
                                </div>
                                <a href="{{ route('dashboard.discover') }}?language={{ urlencode($movie->original_language) }}"
                                    class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 uppercase text-gray-200
                                    hover:bg-cyan-500/20 hover:border-cyan-400/40 transition duration-300">
                                    {{ $movie->original_language }}
                                </a>
                            </div>
                            {{-- Genres --}}
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($genreNames as $genre)
                                <a href="{{ route('dashboard.discover') }}?genre={{ urlencode(is_object($genre) ? $genre->name : $genre) }}"
                                    class="px-2.5 py-0.5 rounded-full bg-cyan-500/10 border border-cyan-400/20
                                    text-cyan-200 text-[11px] hover:bg-cyan-500/30 transition duration-300 inline-block">
                                    {{ is_object($genre) ? $genre->name : $genre }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- 3. Director + Cast --}}
                    <div class="space-y-3">
                        <div class="flex gap-3 text-sm">
                            <span class="text-gray-500 min-w-[80px]">Director</span>
                            @foreach($movie->directors as $director)
                            <span class="text-white font-medium">{{ $director->name ?? 'Unknown' }}</span>
                            @endforeach
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Starring</span>
                            <div class="grid grid-cols-3 gap-3 mt-3">
                                @foreach($movie->actors as $actor)
                                <x-movie.small.small-actor-card
                                    :actor_id="$actor->tmdb_actor_id"
                                    :image_url="$actor->image_url"
                                    :name="$actor->name" />
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- 4. Tagline + Overview --}}
                    <div>
                        @isset($movie->tagline)
                        <p class="text-cyan-300 text-base italic tracking-wide">{{ $movie->tagline }}</p>
                        @endisset
                        <p class="text-gray-300 text-sm leading-relaxed mt-2">{{ $movie->overview }}</p>
                    </div>

                    {{-- 5. Action Buttons (horizontal) --}}
                    <div class="flex items-center gap-3">

                        {{-- Watchlist --}}
                        @if($isInWatchlist)
                        <form action="{{ route('watchlist.destroy', $movie->tmdb_movie_id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="flex items-center gap-2 px-4 py-2.5 rounded-full font-semibold text-sm
                                bg-red-500/20 hover:bg-red-500/30 border border-red-400/30 text-red-300 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>Watchlist</span>
                            </button>
                        </form>
                        @else
                        <form action="{{ route('watchlist.store', $movie->tmdb_movie_id) }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 px-4 py-2.5 rounded-full font-semibold text-sm
                                bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500
                                text-black transition shadow-lg hover:shadow-cyan-500/50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Watchlist</span>
                            </button>
                        </form>
                        @endif

                        {{-- Favorite --}}
                        @if($isFavorite)
                        <form action="{{ route('favorite.destroy', $movie->tmdb_movie_id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="flex items-center gap-2 px-4 py-2.5 rounded-full font-semibold text-sm
                                bg-red-500/20 hover:bg-red-500/30 border border-red-400/30 text-red-300 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                </svg>
                                <span>Favorited</span>
                            </button>
                        </form>
                        @else
                        <form action="{{ route('favorite.store', $movie->tmdb_movie_id) }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 px-4 py-2.5 rounded-full font-semibold text-sm
                                bg-white/10 hover:bg-white/15 border border-white/20 hover:border-purple-400/50
                                text-white transition hover:shadow-lg hover:shadow-purple-500/20">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                </svg>
                                <span>Favorite</span>
                            </button>
                        </form>
                        @endif

                        {{-- Share --}}
                        <button onclick="copyShareLink()"
                            class="flex items-center gap-2 px-4 py-2.5 rounded-full font-semibold text-sm
                            bg-white/5 hover:bg-white/10 border border-white/10 hover:border-cyan-400/40
                            text-gray-100 transition hover:text-cyan-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                                <line x1="8.7" y1="10.7" x2="15.3" y2="6.3"/><line x1="8.7" y1="13.3" x2="15.3" y2="17.7"/>
                            </svg>
                            <span>Share</span>
                        </button>

                    </div>
                </div>

                {{-- DESKTOP LAYOUT --}}
                <div class="hidden md:grid grid-cols-2 lg:grid-cols-[380px_1fr] gap-10 items-center transition-all duration-700" id="default-layout">

                    <!-- POSTER -->
                    <div id="poster-col" class="group relative w-[325px] transition-all duration-700">
                        <div class="absolute inset-0 bg-cyan-400/20 blur-3xl rounded-[32px] scale-90"></div>
                        <img src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_path }}"
                            alt="{{ $movie->title }}"
                            class="relative z-10 w-full rounded-[32px] shadow-[0_20px_80px_rgba(0,0,0,0.8)]
                            border border-white/10 object-cover transition duration-500 group-hover:scale-[1.03] aspect-[4/6]">
                    </div>

                    <!-- CONTENT -->
                    <div id="trailer-layout" class="opacity-100 scale-100 transform transition-all duration-700 ease-out flex-col gap-10" wire:ignore>
                        @if($movie->trailer_key)
                        <div id="youtube-player" class="w-full rounded-2xl overflow-hidden"></div>
                        @else
                        <div class="flex items-center justify-center h-[300px] rounded-2xl border border-white/10 bg-white/5">
                            <p class="text-gray-400">Trailer tidak tersedia</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div id="content-col" class="hidden md:flex flex-col justify-between transition-all duration-700">
                    <div class="mt-10">
                        <h1 class="text-5xl lg:text-7xl font-black leading-none tracking-tight">{{ $movie->title }}</h1>
                    </div>
                    <div class="grid grid-cols-[max-content_1fr_max-content] gap-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-3 mt-8 text-sm">
                                <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-yellow-500/10 border border-yellow-400/20 text-yellow-300">
                                    <span class="font-black tracking-wide">IMDb</span>
                                    <span class="text-white font-semibold">{{ $movie->rating }}</span>
                                </div>
                                <div class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-gray-200">
                                    {{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}
                                </div>
                                <div class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-gray-200">
                                    {{ $movie->runtime }} min
                                </div>
                                <a href="{{ route('dashboard.discover') }}?language={{ urlencode($movie->original_language) }}"
                                    class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 uppercase text-gray-200
                                    hover:bg-cyan-500/20 hover:border-cyan-400/40 hover:text-cyan-100 transition duration-300 inline-block">
                                    {{ $movie->original_language }}
                                </a>
                            </div>
                            <div class="mt-8 space-y-4 max-w-3xl">
                                <div class="flex gap-3">
                                    <span class="text-gray-500 min-w-[90px]">Director</span>
                                    @foreach($movie->directors as $director)
                                    <span class="text-white font-medium">{{ $director->name ?? 'Unknown Director' }}</span>
                                    @endforeach
                                </div>
                                <div class="flex gap-3">
                                    <span class="text-gray-500 min-w-[90px]">Starring
                                        <div class="grid grid-cols-3 mt-4 ml-4 gap-10 overflow-hidden">
                                            @foreach($movie->actors as $actor)
                                            <x-movie.small.small-actor-card
                                                :actor_id="$actor->tmdb_actor_id"
                                                :image_url="$actor->image_url"
                                                :name="$actor->name" />
                                            @endforeach
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="mt-10 max-w-3xl mx-auto">
                                <p class="mt-5 text-cyan-300 text-lg italic tracking-wide">{{ $movie->tagline }}</p>
                                <p class="text-gray-300 text-lg leading-relaxed mt-2">{{ $movie->overview }}</p>
                                <div class="mt-2">
                                    <div class="flex flex-wrap gap-4">
                                        @foreach($genreNames as $genre)
                                        <a href="{{ route('dashboard.discover') }}?genre={{ urlencode(is_object($genre) ? $genre->name : $genre) }}"
                                            class="px-4 py-2 rounded-full bg-cyan-500/10 border border-cyan-400/20 backdrop-blur-xl
                                            hover:bg-cyan-500/30 hover:border-cyan-400/40 hover:shadow-lg hover:shadow-cyan-500/20
                                            text-cyan-200 hover:text-cyan-100 text-sm transition duration-300 cursor-pointer inline-block">
                                            {{ is_object($genre) ? $genre->name : $genre }}
                                        </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col justify-between py-10">
                            {{-- Watchlist --}}
                            <div>
                                @if($isInWatchlist)
                                <form action="{{ route('watchlist.destroy', $movie->tmdb_movie_id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="group relative px-6 md:px-8 py-3 md:py-4 rounded-full font-semibold text-base
                                        bg-red-500/20 hover:bg-red-500/30 border border-red-400/30 text-red-300 transition-all duration-300
                                        flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('watchlist.store', $movie->tmdb_movie_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="group relative px-6 md:px-8 py-3 md:py-4 rounded-full font-semibold text-base
                                        bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500
                                        text-black transition-all duration-300 shadow-lg hover:shadow-cyan-500/50
                                        overflow-hidden flex items-center justify-center gap-2">
                                        <span class="relative z-10 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </span>
                                    </button>
                                </form>
                                @endif
                            </div>
                            {{-- Favorite --}}
                            <div>
                                @if($isFavorite)
                                <form action="{{ route('favorite.destroy', $movie->tmdb_movie_id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="group relative px-6 md:px-8 py-3 md:py-4 rounded-full font-semibold text-base
                                        bg-red-500/20 hover:bg-red-500/30 border border-red-400/30 text-red-300 transition-all duration-300
                                        flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                        </svg>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('favorite.store', $movie->tmdb_movie_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="group relative px-6 md:px-8 py-3 md:py-4 rounded-full font-semibold text-base
                                        bg-white/10 hover:bg-white/15 border border-white/20 hover:border-purple-400/50
                                        text-white transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/20
                                        flex items-center justify-center gap-2">
                                        <span class="relative z-10 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                            </svg>
                                        </span>
                                    </button>
                                </form>
                                @endif
                            </div>
                            {{-- Share --}}
                            <div>
                                <button onclick="copyShareLink()"
                                    class="group relative px-6 md:px-8 py-3 md:py-4 rounded-full font-semibold text-base
                                    bg-white/5 hover:bg-white/10 border border-white/10 hover:border-cyan-400/40
                                    text-gray-100 transition-all duration-300 hover:text-cyan-200
                                    hover:shadow-lg hover:shadow-cyan-500/20 flex items-center justify-center gap-2">
                                    <span class="relative z-10 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                                            <line x1="8.7" y1="10.7" x2="15.3" y2="6.3"/><line x1="8.7" y1="13.3" x2="15.3" y2="17.7"/>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- CONTENT SECTION -->
        <div class="relative z-10 bg-[#020817]">

            <div class="max-w-7xl mx-auto px-6 py-14">

                <!-- COMMENTS -->
                <section>

                    <div class="relative rounded-[36px] border border-white/10 bg-[#0A0F1F]/60 backdrop-blur-2xl overflow-hidden">

                        <!-- ambient glow -->
                        <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-cyan-500/10 blur-3xl rounded-full"></div>
                        <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-purple-500/10 blur-3xl rounded-full"></div>

                        <div class="relative p-8 md:p-10">

                            <!-- HEADER -->
                            <div class="flex justify-between items-start mb-5">

                                <div>
                                    <h2 class="text-xl md:text-3xl font-bold tracking-tight">Discussion</h2>
                                    <p class="text-gray-400 text-[10px] md:text-sm mt-1">Join the conversation with other viewers</p>
                                </div>

                                <div class="px-4 py-2 rounded-full bg-white/5 border border-white/10 text-xs md:text-sm text-gray-300 justify-items-center">
                                    {{ isset($comments) ? count($comments) : 0 }} comments
                                </div>

                            </div>

                            <!-- COMMENTS LIST -->
                            <div class="space-y-3">

                                @if(isset($comments) && count($comments))

                                @foreach($comments as $comment)

                                {{-- KOMENTAR UTAMA --}}
                                <div>

                                    <div class="flex gap-4">

                                        {{-- AVATAR + GARIS VERTIKAL --}}
                                        <div class="flex flex-col items-center flex-shrink-0">
                                            <div class="w-7 h-7 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-cyan-400 to-cyan-600
                                                            text-black font-bold flex items-center justify-center text-xs md:text-sm
                                                            shadow-[0_0_20px_rgba(34,211,238,0.35)]">
                                                {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                        </div>

                                        <div class="flex-1 min-w-0">

                                            {{-- HEADER --}}
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex items-center gap-3 flex-wrap">
                                                    <h3 class="font-semibold text-xs md:text-sm text-white">
                                                        {{ $comment->user->name ?? 'Unknown User' }}
                                                    </h3>
                                                    <span class="text-xs md:text-sm text-gray-500">
                                                        {{ $comment->created_at->diffForHumans() }}
                                                        @if($comment->updated_at->gt($comment->created_at->addSecond()))
                                                        <span class="italic">(edited)</span>
                                                        @endif
                                                    </span>
                                                </div>

                                                {{-- DROPDOWN MENU (hanya pemilik) --}}
                                                @auth
                                                @if(Auth::id() === $comment->user_id)
                                                <div class="relative" x-data="{ open: false }">
                                                    <button @click="open = !open"
                                                        class="p-1 rounded-lg text-gray-500 hover:text-white hover:bg-white/10 transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                            <circle cx="5" cy="12" r="2" />
                                                            <circle cx="12" cy="12" r="2" />
                                                            <circle cx="19" cy="12" r="2" />
                                                        </svg>
                                                    </button>
                                                    <div x-show="open"
                                                        @click.outside="open = false"
                                                        x-transition
                                                        class="absolute right-0 mt-1 w-32 rounded-xl bg-[#0d1424] border border-white/10
                                                                    shadow-xl z-50 overflow-hidden">
                                                        <button
                                                            @click="open = false; toggleEdit({{ $comment->id }})"
                                                            class="w-full text-left px-4 py-2 text-xs md:text-sm text-gray-300
                                                                    hover:bg-white/10 hover:text-yellow-300 transition flex items-center gap-2">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                                                            m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit
                                                        </button>
                                                        <button
                                                            @click="open = false; deleteComment({{ $comment->id }})"
                                                            class="w-full text-left px-4 py-2 text-xs md:text-sm text-gray-300
                                                                    hover:bg-white/10 hover:text-red-400 transition flex items-center gap-2">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7
                                                                            m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                                @endif
                                                @endauth
                                            </div>

                                            {{-- TEKS KOMENTAR --}}
                                            <div id="comment-text-{{ $comment->id }}">
                                                <p class="mt-0 text-gray-200 text-xs md:text-sm leading-relaxed break-words">
                                                    {{ $comment->content }}
                                                </p>
                                            </div>

                                            {{-- FORM EDIT (hidden) --}}
                                            @auth
                                            @if(Auth::id() === $comment->user_id)
                                            <div id="edit-form-{{ $comment->id }}" class="hidden mt-2">
                                                <form action="{{ route('movie.comment.update', $comment->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <textarea
                                                        name="content"
                                                        rows="2"
                                                        class="w-full resize-none bg-white/5 border border-white/10 rounded-lg
                                                                focus:border-cyan-400 outline-none text-gray-200
                                                                p-3 text-xs md:text-base transition">{{ $comment->content }}</textarea>
                                                    <div class="flex gap-2 mt-2">
                                                        <button type="submit"
                                                            class="px-4 py-1.5 rounded-lg bg-cyan-500 hover:bg-cyan-400
                                                                        text-black text-xs md:text-sm font-semibold transition">
                                                            Save
                                                        </button>
                                                        <button type="button"
                                                            onclick="toggleEdit({{ $comment->id }})"
                                                            class="px-4 py-1.5 rounded-lg bg-white/10 hover:bg-white/20
                                                                        text-gray-300 text-xs md:text-sm transition">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            {{-- FORM DELETE (hidden, submit via JS) --}}
                                            <form id="delete-form-{{ $comment->id }}"
                                                action="{{ route('movie.comment.destroy', $comment->id) }}"
                                                method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            @endif
                                            @endauth

                                            {{-- ACTIONS --}}
                                            <div class="flex gap-6 mt-1 text-xs md:text-sm text-gray-500 items-center">
                                            
                                                {{-- LIKE BUTTON --}}
                                                @auth
                                                <button
                                                    data-like-btn="{{ $comment->id }}"
                                                    data-liked="{{ $comment->isLikedBy(Auth::id()) ? 'true' : 'false' }}"
                                                    class="flex items-center gap-1.5 transition
                                                        {{ $comment->isLikedBy(Auth::id()) ? 'text-cyan-400' : 'hover:text-cyan-300' }}">
                                                    <svg class="w-4 h-4" fill="{{ $comment->isLikedBy(Auth::id()) ? 'currentColor' : 'none' }}"
                                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21H5a2 2 0 01-2-2v-7a2 2 0 012-2h2.924L10 4.382A1 1 0 0111 4h.5a1.5 1.5 0 011.5 1.5V10z" />
                                                    </svg>
                                                    <span data-like-count="{{ $comment->id }}">{{ $comment->likes->count() }}</span>
                                                </button>
                                                @else
                                                <span class="flex items-center gap-1.5 cursor-default">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21H5a2 2 0 01-2-2v-7a2 2 0 012-2h2.924L10 4.382A1 1 0 0111 4h.5a1.5 1.5 0 011.5 1.5V10z" />
                                                    </svg>
                                                    {{ $comment->likes->count() }}
                                                </span>
                                                @endauth
                                            
                                                {{-- REPLY BUTTON --}}
                                                @auth
                                                <button
                                                    data-reply-toggle="{{ $comment->id }}"
                                                    class="hover:text-cyan-300 transition">
                                                    Reply
                                                </button>
                                                @endauth
                                            
                                                {{-- REPORT BUTTON --}}
                                                @auth
                                                @if(Auth::id() !== $comment->user_id)
                                                <button
                                                    data-report-btn="{{ $comment->id }}"
                                                    data-reported="{{ $comment->isReportedBy(Auth::id()) ? 'true' : 'false' }}"
                                                    class="flex items-center gap-1.5 transition
                                                        {{ $comment->isReportedBy(Auth::id()) ? 'text-red-400 cursor-default' : 'hover:text-red-400' }}">
                                                    <span>{{ $comment->isReportedBy(Auth::id()) ? 'Reported' : 'Report' }}</span>
                                                </button>
                                                @endif
                                                @endauth
                                            
                                            </div>

                                        </div>
                                    </div>

                                    {{-- FORM REPLY KE KOMENTAR UTAMA --}}
                                    @auth
                                    <div id="reply-form-{{ $comment->id }}" class="hidden ml-14 mt-3">
                                        <form action="{{ route('movie.comment') }}" method="POST" class="flex gap-2">
                                            @csrf
                                            <input type="hidden" name="movie_id" value="{{ $movie->tmdb_movie_id }}">
                                            <input type="hidden" name="reply_id" value="{{ $comment->id }}">
                                            <textarea
                                                name="content"
                                                rows="1"
                                                placeholder="Reply to {{ $comment->user->name ?? '' }}…"
                                                class="flex-1 resize-none bg-transparent border-b border-white/10
                                                        focus:border-cyan-400 outline-none text-gray-200
                                                        placeholder-gray-500 pb-2 text-xs md:text-sm transition"></textarea>
                                            <button type="submit"
                                                class="self-end px-4 py-2 rounded-lg bg-cyan-500 hover:bg-cyan-400
                                                            text-black text-xs md:text-sm font-semibold transition">
                                                Reply
                                            </button>
                                        </form>
                                    </div>
                                    @endauth

                                    {{-- REPLY --}}
                                    @if($comment->replies->isNotEmpty())
                                    <div class="mt-3 ml-10 md:ml-14">
                                        {{-- TOGGLE BUTTON --}}
                                        <button
                                            onclick="toggleReplies({{ $comment->id }})"
                                            id="toggle-replies-btn-{{ $comment->id }}"
                                            class="flex items-center gap-1.5 text-xs text-cyan-400 hover:text-cyan-300 transition mb-3">
                                            <svg id="toggle-replies-icon-{{ $comment->id }}"
                                                class="w-3.5 h-3.5 transition-transform duration-200"
                                                style="transform: rotate(-90deg)"
                                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                            <span id="toggle-replies-label-{{ $comment->id }}">
                                                @php $totalReplies = $comment->allRepliesFlat()->count(); @endphp
                                                Show {{ $totalReplies }} {{ Str::plural('reply', $totalReplies) }}
                                            </span>
                                        </button>

                                        {{-- REPLIES LIST --}}
                                        <div id="replies-list-{{ $comment->id }}" class="hidden space-y-3">
                                            @foreach($comment->allRepliesFlat() as $reply)
                                                <x-movie.comment-reply :reply="$reply" :movie="$movie" />
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                </div>
                                {{-- END KOMENTAR UTAMA --}}

                                @endforeach

                                @else

                                <div class="text-gray-400 text-sm">
                                    No comments yet. Start the discussion.
                                </div>

                                @endif

                            </div>

                            <!-- COMMENT INPUT -->
                            @auth
                            <form action="{{ route('movie.comment') }}" method="POST">
                                @csrf
                                <input type="hidden" name="movie_id" value="{{ $movie->tmdb_movie_id }}">

                                <div class="flex gap-4 mt-5">

                                    <div class="w-7 h-7 md:w-10 md:h-10 rounded-full text-xs md:text-sm bg-cyan-500 text-black font-bold flex items-center justify-center flex-shrink-0">
                                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                    </div>

                                    <div class="flex-1">
                                        <textarea
                                            name="content"
                                            rows="2"
                                            placeholder="Add a comment..."
                                            class="w-full bg-transparent border-b border-white/10 focus:border-cyan-400 outline-none text-xs md:text-base text-gray-200 placeholder-gray-500 resize-none pb-2"></textarea>

                                        <div class="flex justify-end mt-3">
                                            <button type="submit"
                                                class="px-5 py-2 rounded-lg bg-cyan-500 text-black text-xs md:text-base font-semibold hover:bg-cyan-400 transition">
                                                Comment
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </form>
                            @else
                            <div class="mb-10 flex items-center gap-3 px-5 py-4 rounded-xl bg-white/5 border border-white/10 text-xs md:text-sm text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>
                                    <a href="{{ route('login') }}" class="text-cyan-400 hover:underline">Login</a> to join the discussion.
                                </span>
                            </div>
                            @endauth

                        </div>

                    </div>

                </section>

                <!-- SIMILAR MOVIES -->
                <section class="mt-14">

                    <div class="flex justify-between items-center mb-6">

                        <h2 class="text-xl sm:text-3xl font-bold">
                            Similar Movies
                        </h2>

                    </div>

                    <div class="flex gap-8 w-full mx-auto overflow-hidden overflow-x-auto scrollbar-hide">
                        @foreach($similarMovies as $similar)
                            <x-movie.movie-modal
                                :poster="$similar->poster_url"
                                :title="$similar->title"
                                :tmdb_movie_id="$similar->tmdb_movie_id"
                                :year="$similar->year ?? null"
                                :rating="$similar->rating ?? null"
                                :overview="$similar->overview ?? null"
                                :genres="$similar->genres->pluck('genre.name')->filter()->toArray() ?? []"
                                :duration="$similar->runtime ?? null" />
                        @endforeach
                    </div>

                </section>

            </div>

        </div>
    </div>

    @if($movie->trailer_key)
    <script src="https://www.youtube.com/iframe_api"></script>
    <script>
        let player, playerMobile;
        const trailerKey = "{{ $movie->trailer_key }}";
        const isMobile = window.innerWidth < 768;

        function onYouTubeIframeAPIReady() {
            // Desktop player
            if (document.getElementById('youtube-player')) {
                player = new YT.Player('youtube-player', {
                    width: '100%', height: '500',
                    videoId: trailerKey,
                    playerVars: { autoplay: 0, rel: 0, modestbranding: 1, controls: 1 },
                    events: { onError: onPlayerError }
                });
            }
            // Mobile player
            if (document.getElementById('youtube-player-mobile')) {
                playerMobile = new YT.Player('youtube-player-mobile', {
                    width: '100%', height: '220',
                    videoId: trailerKey,
                    playerVars: { autoplay: 0, rel: 0, modestbranding: 1, controls: 1 },
                    events: { onError: onPlayerErrorMobile }
                });
            }
        }

        function onPlayerError(event) {
            showFallback('youtube-player');
        }

        function onPlayerErrorMobile(event) {
            showFallback('youtube-player-mobile');
        }

        function showFallback(containerId) {
            document.getElementById(containerId).innerHTML = `
            <a href="https://www.youtube.com/watch?v=${trailerKey}" target="_blank">
                <div class="relative group w-full rounded-2xl overflow-hidden cursor-pointer">
                    <img src="https://img.youtube.com/vi/${trailerKey}/maxresdefault.jpg"
                        class="w-full object-cover"
                        onerror="this.src='https://img.youtube.com/vi/${trailerKey}/hqdefault.jpg'">
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-4
                        bg-black/50 group-hover:bg-black/30 transition duration-300">
                        <div class="w-16 h-16 rounded-full bg-red-600 flex items-center justify-center
                            group-hover:scale-110 transition duration-300 shadow-xl">
                            <svg class="w-7 h-7 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                        <p class="text-white text-sm bg-black/40 px-4 py-2 rounded-full">Klik untuk tonton di YouTube</p>
                    </div>
                </div>
            </a>`;
        }
    </script>
    @endif

    {{-- TOGGLE REPLY FORM --}}
    <script>
        document.querySelectorAll('[data-reply-toggle]').forEach(btn => {
            btn.addEventListener('click', () => {
                const form = document.getElementById('reply-form-' + btn.dataset.replyToggle);
                if (!form) return;
                form.classList.toggle('hidden');
                if (!form.classList.contains('hidden')) {
                    form.querySelector('textarea')?.focus();
                }
            });
        });

        // Toggle edit form
        function toggleEdit(id) {
            const textEl = document.getElementById('comment-text-' + id);
            const formEl = document.getElementById('edit-form-' + id);
            if (!textEl || !formEl) return;
            textEl.classList.toggle('hidden');
            formEl.classList.toggle('hidden');
            if (!formEl.classList.contains('hidden')) {
                formEl.querySelector('textarea')?.focus();
            }
        }

        // Delete dengan konfirmasi
        function deleteComment(id) {
            if (!confirm('Hapus komentar ini?')) return;
            document.getElementById('delete-form-' + id)?.submit();
        }

        function copyShareLink() {
            const shareUrl = window.location.href;

            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    url: shareUrl,
                }).catch(() => {});
                return;
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(shareUrl)
                    .then(() => alert('Link berhasil disalin'))
                    .catch(() => prompt('Salin link ini:', shareUrl));
                return;
            }

            prompt('Salin link ini:', shareUrl);
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle all movie cards with hover panels
            document.querySelectorAll('[data-card]').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    const panel = card.querySelector('[data-panel]');
                    if (!panel) return;
                    
                    const rect = card.getBoundingClientRect();
                    
                    // Determine panel width based on screen size
                    let panelWidth;
                    if (window.innerWidth >= 1024) {
                        panelWidth = 560;
                    } else if (window.innerWidth >= 768) {
                        panelWidth = 460;
                    } else {
                        panelWidth = 360;
                    }
                    
                    // Check if panel would exceed viewport width
                    const panelRightEdge = rect.left + panelWidth;
                    
                    if (panelRightEdge > window.innerWidth) {
                        // Panel akan keluar dari kanan, posisikan ke kiri
                        panel.style.left = 'auto';
                        panel.style.right = '0';
                    } else {
                        // Panel bisa normal ke kanan
                        panel.style.left = '0';
                        panel.style.right = 'auto';
                    }
                });
                
                // Repositionkan saat window resize
                card.addEventListener('mousemove', function() {
                    const panel = card.querySelector('[data-panel]');
                    if (!panel) return;
                    
                    const rect = card.getBoundingClientRect();
                    let panelWidth;
                    
                    if (window.innerWidth >= 1024) {
                        panelWidth = 560;
                    } else if (window.innerWidth >= 768) {
                        panelWidth = 460;
                    } else {
                        panelWidth = 360;
                    }
                    
                    const panelRightEdge = rect.left + panelWidth;
                    
                    if (panelRightEdge > window.innerWidth) {
                        panel.style.left = 'auto';
                        panel.style.right = '0';
                    } else {
                        panel.style.left = '0';
                        panel.style.right = 'auto';
                    }
                });
            });
            
            // Handle window resize untuk re-check positioning
            window.addEventListener('resize', function() {
                document.querySelectorAll('[data-card]').forEach(card => {
                    const panel = card.querySelector('[data-panel]');
                    if (!panel || panel.style.visibility === 'hidden') return;
                    
                    const rect = card.getBoundingClientRect();
                    let panelWidth;
                    
                    if (window.innerWidth >= 1024) {
                        panelWidth = 560;
                    } else if (window.innerWidth >= 768) {
                        panelWidth = 460;
                    } else {
                        panelWidth = 360;
                    }
                    
                    const panelRightEdge = rect.left + panelWidth;
                    
                    if (panelRightEdge > window.innerWidth) {
                        panel.style.left = 'auto';
                        panel.style.right = '0';
                    } else {
                        panel.style.left = '0';
                        panel.style.right = 'auto';
                    }
                });
            });
        });

        document.querySelectorAll('[data-like-btn]').forEach(btn => {
            btn.addEventListener('click', async () => {
                const commentId = btn.dataset.likeBtn;
                const isLiked   = btn.dataset.liked === 'true';
                const countEl   = document.querySelector(`[data-like-count="${commentId}"]`);
        
                // Optimistic update
                const newLiked = !isLiked;
                btn.dataset.liked = String(newLiked);
                applyLikeStyle(btn, newLiked);
        
                try {
                    const res  = await fetch(`/comments/${commentId}/like`, {
                        method:  'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept':       'application/json',
                            'Content-Type': 'application/json',
                        },
                    });
                    const data = await res.json();
        
                    // Sync dengan server
                    btn.dataset.liked = String(data.liked);
                    applyLikeStyle(btn, data.liked);
                    if (countEl) countEl.textContent = data.like_count;
        
                } catch (err) {
                    // Rollback jika gagal
                    btn.dataset.liked = String(isLiked);
                    applyLikeStyle(btn, isLiked);
                    console.error('Like error:', err);
                }
            });
        });
        
        function applyLikeStyle(btn, liked) {
            const svg = btn.querySelector('svg');
            if (liked) {
                btn.classList.add('text-cyan-400');
                btn.classList.remove('text-gray-500', 'hover:text-cyan-300');
                if (svg) svg.setAttribute('fill', 'currentColor');
            } else {
                btn.classList.remove('text-cyan-400');
                btn.classList.add('text-gray-500', 'hover:text-cyan-300');
                if (svg) svg.setAttribute('fill', 'none');
            }
        }
        
        const modalHTML = `
        <div id="global-report-modal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" id="report-backdrop"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-[#0d1424] border border-white/10 shadow-2xl p-6 z-10">
                <h3 class="text-lg font-bold text-white mb-1">Laporkan Komentar</h3>
                <p class="text-sm text-gray-400 mb-5">Pilih alasan pelaporanmu agar tim kami bisa meninjau.</p>
                <div id="report-form-wrapper">
                    <div class="space-y-2 mb-5">
                        ${[
                            ['inappropriate', 'Konten tidak pantas'],
                            ['spam', 'Spam atau iklan'],
                            ['hate_speech', 'Ujaran kebencian'],
                            ['other', 'Lainnya'],
                        ].map(([value, label]) => `
                            <label class="flex items-center gap-3 px-4 py-3 rounded-xl cursor-pointer
                                        border border-white/10 hover:border-cyan-400/40 hover:bg-white/5 transition">
                                <input type="radio" name="reason" value="${value}" class="accent-cyan-400"
                                    ${value === 'inappropriate' ? 'checked' : ''}>
                                <span class="text-sm text-gray-200">${label}</span>
                            </label>
                        `).join('')}
                    </div>
                    <textarea id="report-note" rows="2" placeholder="Catatan tambahan (opsional)..."
                        class="w-full resize-none bg-white/5 border border-white/10 rounded-xl
                            focus:border-cyan-400 outline-none text-gray-200 placeholder-gray-500
                            p-3 text-sm transition mb-4"></textarea>
                    <div class="flex gap-3 justify-end">
                        <button id="report-cancel" class="px-5 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-gray-300 text-sm transition">
                            Batal
                        </button>
                        <button id="report-submit" class="px-5 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold transition">
                            Kirim Laporan
                        </button>
                    </div>
                </div>
                <div id="report-success" class="hidden text-center py-4">
                    <svg class="w-12 h-12 text-cyan-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-white font-semibold">Laporan Terkirim</p>
                    <p class="text-gray-400 text-sm mt-1">Tim kami akan meninjau komentar ini.</p>
                </div>
            </div>
        </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // State
        let activeCommentId = null;

        // Buka modal
        document.querySelectorAll('[data-report-btn]').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.dataset.reported === 'true') return;
                activeCommentId = btn.dataset.reportBtn;
                document.getElementById('global-report-modal').classList.remove('hidden');
                document.getElementById('report-success').classList.add('hidden');
                document.getElementById('report-form-wrapper').classList.remove('hidden');
                document.getElementById('report-note').value = '';
                document.body.style.overflow = 'hidden';
            });
        });

        // Tutup modal
        function closeGlobalReportModal() {
            document.getElementById('global-report-modal').classList.add('hidden');
            document.body.style.overflow = '';
            activeCommentId = null;
        }

        document.getElementById('report-backdrop').addEventListener('click', closeGlobalReportModal);
        document.getElementById('report-cancel').addEventListener('click', closeGlobalReportModal);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeGlobalReportModal(); });

        // Submit
        document.getElementById('report-submit').addEventListener('click', async () => {
            if (!activeCommentId) return;

            const reason  = document.querySelector('#global-report-modal input[name="reason"]:checked')?.value;
            const note    = document.getElementById('report-note').value;
            const submitBtn = document.getElementById('report-submit');

            submitBtn.disabled    = true;
            submitBtn.textContent = 'Mengirim...';

            try {
                const res  = await fetch(`/comments/${activeCommentId}/report`, {
                    method:  'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ reason, note }),
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    document.getElementById('report-form-wrapper').classList.add('hidden');
                    document.getElementById('report-success').classList.remove('hidden');

                    // Update tombol
                    const btn = document.querySelector(`[data-report-btn="${activeCommentId}"]`);
                    if (btn) {
                        btn.dataset.reported = 'true';
                        btn.classList.add('text-red-400', 'cursor-default');
                        btn.classList.remove('hover:text-red-400');
                        btn.querySelector('span').textContent = 'Reported';
                    }

                    if (data.deleted) {
                        // Komentar dihapus, reload halaman setelah modal tertutup
                        setTimeout(() => {
                            closeGlobalReportModal();
                            window.location.reload();
                        }, 2000);
                    } else {
                        setTimeout(closeGlobalReportModal, 2000);
                    }
                } else {
                    alert(data.message || 'Gagal mengirim laporan.');
                }
            } catch (err) {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                submitBtn.disabled    = false;
                submitBtn.textContent = 'Kirim Laporan';
            }
        });
        
        function toggleReplies(id) {
            const list  = document.getElementById('replies-list-' + id);
            const label = document.getElementById('toggle-replies-label-' + id);
            const icon  = document.getElementById('toggle-replies-icon-' + id);
            const count = list.querySelectorAll(':scope > *').length;

            const isHidden = list.classList.toggle('hidden');

            icon.style.transform = isHidden ? 'rotate(-90deg)' : 'rotate(0deg)';
            label.textContent    = isHidden
                ? `Show ${count} ${count === 1 ? 'reply' : 'replies'}`
                : `Hide ${count} ${count === 1 ? 'reply' : 'replies'}`;
        }
    </script>
</x-app-layout>