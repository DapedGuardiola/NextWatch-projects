<x-app-layout>
    <x-slot name="title">
        {{ __('Dashboard') }}
    </x-slot>
    <div class="w-screen md:h-screen relative">
        <img src="{{ $topOne->poster_url }}"
            class="block sm:hidden w-full h-full object-cover"
            alt="hero">

        <!-- Tablet & Desktop -->
        <img src="{{ $topOne->background_url }}"
            class="hidden sm:block w-full h-full object-cover object-center"
            alt="hero">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent from-10% via-[#020817]/75 via-20% to-[#020817] to-100%"></div>

        <div class="absolute bottom-[200px] left-6 sm:left-10 right-6 sm:right-10">
            <div class="flex justify-between items-end sm:items-center">
                <div class="max-w-[70%] sm:max-w-[80%]">
                    <h1 class="sm:py-1 text-white text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-bold leading-tight line-clamp-2 sm:line-clamp-0">
                        {{ $topOne->title }}
                    </h1>
                </div>

                {{-- Mobile --}}
                <a href="{{ route('movie.detail', $topOne->tmdb_movie_id) }}"
                    class="sm:hidden inline-block px-6 py-2 text-sm rounded-xl bg-black/30 text-white hover:bg-black/50 font-regular transition duration-300 shadow-lg">
                    Detail
                </a>

                {{-- Desktop --}}
                <a href="{{ route('movie.detail', $topOne->tmdb_movie_id) }}"
                    class="hidden sm:inline-block px-6 py-2 text-sm rounded-xl bg-black/30 text-white hover:bg-black/50 font-regular sm:rounded-3xl sm:text-xl transition duration-300 shadow-lg">
                    See Detail
                </a>
            </div>
        </div>

    </div>
    <div class="-mt-[180px] sm:-mt-[200px] w-full left-0 right-0 relative z-10 mx-auto">
        <div class="px-4 mt-0 sm:mt-6 sm:px-6 lg:px-0">
            <x-movie.search-bar />
        </div>

        @if(auth()->check() && auth()->user()->is_personalized && auth()->user()->persona_ready && isset($forYou) && $forYou->count() > 0)
        <div class="mx-4 sm:mx-10 my-6 sm:-mt-6 sm:my-10">
            <h1>
                <p class="text-xl sm:text-3xl text-white font-bold">For You</p>
                <p class="text-xs sm:text-sm text-gray-400 mt-1">Top movies on your preference</p>
            </h1>
        </div>
        <div class="max-w-[90%] mx-auto relative">
            {{-- Arrow Left --}}
            <button
                id="ContPrev"
                onclick="scrollByItems(document.getElementById('ScrollContainer'), -1)"
                class="hidden absolute left-0 top-1/2 -translate-y-1/2 z-10
                    w-10 h-10 items-center justify-center
                    rounded-full bg-black/60 border border-white/10
                    text-white hover:bg-black/80 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            {{-- Scroll Container --}}
            <div id="ScrollContainer"
                class="flex gap-4 md:gap-8 overflow-hidden overflow-x-auto scrollbar-hide scroll-smooth">
                @foreach($forYou as $movie)
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

            {{-- Arrow Right --}}
            <button
                id="ContNext"
                onclick="scrollByItems(document.getElementById('ScrollContainer'), 1)"
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10
                    w-10 h-10 flex items-center justify-center
                    rounded-full bg-black/60 border border-white/10
                    text-white hover:bg-black/80 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
        @endif

        <div class="mx-4 sm:mx-10 my-6 sm:my-10">
            <h1>
                <p class="text-xl sm:text-3xl text-white font-bold">Suggested Collection</p>
                <p class="text-xs sm:text-sm text-gray-400 mt-1">Top Collections by your preference</p>
            </h1>
        </div>

        <div class="max-w-[90%] mx-auto relative">
            <button id="collectionPrev" onclick="scrollByItems(document.getElementById('collectionScroll'), -1)"
                class="hidden absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 items-center justify-center rounded-full bg-black/60 border border-white/10 text-white hover:bg-black/80 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <div id="collectionScroll" class="flex gap-4 md:gap-8 overflow-hidden overflow-x-auto scrollbar-hide scroll-smooth">
                @foreach($collections as $collection)
                <x-movie.collection-modal
                    :poster="$collection->backdrop_url"
                    :name="$collection->name"
                    :tmdb_collection_id="$collection->tmdb_collection_id"
                    :overview="$collection->overview ?? null" />
                @endforeach
            </div>
            <button id="collectionNext" onclick="scrollByItems(document.getElementById('collectionScroll'), 1)"
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-black/60 border border-white/10 text-white hover:bg-black/80 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>

        <div class="mx-4 sm:mx-10 my-6 sm:my-10">
            <h1>
                <p class="text-xl sm:text-3xl text-white font-bold">Actors</p>
                <p class="text-xs sm:text-sm text-gray-400 mt-1">Suggested actors for you</p>
            </h1>
        </div>

        <div class="max-w-[90%] mx-auto relative">
            <button id="actorPrev" onclick="scrollByItems(document.getElementById('actorScroll'), -1)"
                class="hidden absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 items-center justify-center rounded-full bg-black/60 border border-white/10 text-white hover:bg-black/80 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <div id="actorScroll" class="flex gap-4 md:gap-8 overflow-hidden overflow-x-auto scrollbar-hide scroll-smooth">
                @foreach($actors as $actor)
                <x-movie.actor-card
                    :actor_id="$actor->tmdb_actor_id"
                    :image_url="$actor->image_url"
                    :name="$actor->name" />
                @endforeach
            </div>
            <button id="actorNext" onclick="scrollByItems(document.getElementById('actorScroll'), 1)"
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-black/60 border border-white/10 text-white hover:bg-black/80 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>

        @if(isset($watchlist) && $watchlist->isNotEmpty())
        <div class="mx-4 sm:mx-10 my-6 sm:my-10">
            <h1>
                <p class="text-xl sm:text-3xl text-white font-bold">In Your Watchlist</p>
                <p class="text-xs sm:text-sm text-gray-400 mt-1">Movies you want to watch later</p>
            </h1>
        </div>
        <div class="flex gap-4 md:gap-8 max-w-[90%] overflow-hidden mx-auto overflow-x-auto scrollbar-hide">
            @foreach($watchlist as $movie)
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
            <a href="{{ route('watchlist.index') }}" class="flex flex-col items-center justify-center">
                <button
                    class="flex z-10 bg-white/15 hover:bg-white/25 text-white border-0
                    w-7 h-7 md:w-8 md:h-8 rounded-full flex items-center justify-center
                    text-base md:text-lg cursor-pointer transition">&#8250;</button>
                <p class="text-sm text-white font-bold justify-content justify-center items-center flex mt-2">
                    See All
                </p>
            </a>
        </div>
        @endif

        <div class="mx-4 sm:mx-10 my-6 sm:my-10">
            <h1>
                <p class="text-xl sm:text-3xl text-white font-bold">Upcoming Movie</p>
                <p class="text-xs sm:text-sm text-gray-400 mt-1">We think you'll love these – coming soon</p>
            </h1>
        </div>

        <div class="max-w-[90%] mx-auto relative">
            <button id="upcomingPrev" onclick="scrollByItems(document.getElementById('upcomingScroll'), -1)"
                class="hidden absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 items-center justify-center rounded-full bg-black/60 border border-white/10 text-white hover:bg-black/80 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <div id="upcomingScroll" class="flex gap-4 md:gap-8 overflow-hidden overflow-x-auto scrollbar-hide scroll-smooth">
                @foreach($upcomming as $movie)
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
            <button id="upcomingNext" onclick="scrollByItems(document.getElementById('upcomingScroll'), 1)"
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-black/60 border border-white/10 text-white hover:bg-black/80 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>

        <div class="mx-4 sm:mx-10 my-6 sm:my-10">
            <h1>
                <p class="text-xl sm:text-3xl text-white font-bold">Others Movie</p>
                <p class="text-xs sm:text-sm text-gray-400 mt-1">Other movies you must know</p>
            </h1>
        </div>
        <div class="max-w-full mx-auto justify-items-center scrollbar-hide">
            <div class="grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5
                gap-3 sm:gap-6 md:gap-8
                w-full max-w-[95%] sm:max-w-[90%] mx-auto justify-items-center" id="others-grid">
                @foreach($others as $movie)
                <div class="others-movie-item">
                    <x-movie.movie-modal
                        :poster="$movie->poster_url"
                        :title="$movie->title"
                        :tmdb_movie_id="$movie->tmdb_movie_id"
                        :year="$movie->year ?? null"
                        :rating="$movie->rating ?? null"
                        :overview="$movie->overview ?? null"
                        :genres="$movie->genres->pluck('genre.name')->filter()->toArray() ?? []"
                        :duration="$movie->runtime ?? null" />
                </div>
                @endforeach
            </div>
        </div>
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

            // Scroll Arrows Logic
            function scrollByItems(container, direction, count = 2) {
                const item = container.querySelector(':scope > *');
                if (!item) return;
                const gap = parseFloat(getComputedStyle(container).gap) || 0;
                const scrollAmount = (item.offsetWidth + gap) * count;
                container.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
            }

            function initScrollArrows(scrollId, prevId, nextId) {
                const container = document.getElementById(scrollId);
                const prev = document.getElementById(prevId);
                const next = document.getElementById(nextId);
                if (!container || !prev || !next) return;

                function update() {
                    // Sembunyikan semua arrow di mobile
                    if (window.innerWidth < 640) {
                        prev.classList.add('hidden');
                        prev.classList.remove('flex');
                        next.classList.add('hidden');
                        next.classList.remove('flex');
                        return;
                    }

                    const { scrollLeft, scrollWidth, clientWidth } = container;
                    prev.classList.toggle('hidden', scrollLeft <= 0);
                    prev.classList.toggle('flex', scrollLeft > 0);
                    const atEnd = scrollLeft + clientWidth >= scrollWidth - 1;
                    next.classList.toggle('hidden', atEnd);
                    next.classList.toggle('flex', !atEnd);
                }

                container.addEventListener('scroll', update);
                window.addEventListener('resize', update);
                window.addEventListener('load', update);
            }

            initScrollArrows('ScrollContainer',  'ContPrev',       'ContNext');
            initScrollArrows('collectionScroll', 'collectionPrev', 'collectionNext');
            initScrollArrows('actorScroll',      'actorPrev',      'actorNext');
            initScrollArrows('upcomingScroll',   'upcomingPrev',   'upcomingNext');
        </script>
    </div>
</x-app-layout>