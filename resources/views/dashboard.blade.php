<x-app-layout>
    <x-slot name="title">
        {{ __('Dashboard') }}
    </x-slot>
    <div class="h-screen relative">
        <img src="{{ $topOne->poster_url }}"
            class="w-full h-full object-cover object-center" alt="hero">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent from-20% via-[#212121]/75 via-70% to-[#212121] to-100%"></div>

        <div class="absolute top-[60%] left-10">
            <h1 class="text-white text-7xl font-bold">{{ $topOne->title }}</h1>
        </div>
    </div>
    <div class="-mt-[200px] max-w [95%] left-0 right-0 relative z-10 ">
        <div>
            <x-movie.search-bar />
        </div>

        @if(auth()->check() && auth()->user()->is_personalized && auth()->user()->persona_ready && isset($forYou) && $forYou->count() > 0)
        <div class="mx-10 mb-10">
            <h1>
                <p class="text-3xl text-white font-bold">For You</p>
                <p class="text-sm text-gray-400 mt-1">Top movies on your preference</p>
            </h1>
        </div>
        <div class="flex gap-8 px-2 max-w-[90%] mx-auto overflow-hidden overflow-x-auto scrollbar-hide">
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
        @endif
        
        <div class="mx-10 my-10">
            <h1>
                <p class="text-3xl text-white font-bold">Suggested Collection</p>
                <p class="text-sm text-gray-400 mt-1">Top Collections by your preference</p>
            </h1>
        </div>
        <div class="flex gap-8 px-2 max-w-[90%] mx-auto overflow-hidden overflow-x-auto scrollbar-hide">
            @foreach($collections as $collection)
            <x-movie.collection-modal
                :poster="$collection->backdrop_url"
                :name="$collection->name"
                :tmdb_collection_id="$collection->tmdb_collection_id"
                :overview="$collection->overview ?? null" />
            @endforeach
        </div>

        <div class="mx-10 my-10">
            <h1>
                <p class="text-3xl text-white font-bold">Top On Its Genre</p>
                <p class="text-sm text-gray-400 mt-1">One the best movie in each genre</p>
            </h1>
        </div>
        <div class="flex gap-8 px-2 max-w-[90%] overflow-hidden mx-auto overflow-x-auto scrollbar-hide">
            @foreach($topByGenre as $movie)
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
                <p class="text-sm text-gray-400 mt-1">Suggested actors for you</p>
            </h1>
        </div>
        <div class="flex gap-8 px-2 max-w-[90%] overflow-hidden mx-auto overflow-x-auto scrollbar-hide">
            @foreach($actors as $actor)
            <x-movie.actor-card
                :actor_id="$actor->tmdb_actor_id"
                :image_url="$actor->image_url"
                :name="$actor->name"/>
            @endforeach
        </div>
        <div class="mx-4 sm:mx-10 my-6 sm:my-10">
            <h1>
                <p class="text-xl sm:text-3xl text-white font-bold">Others Movie</p>
                <p class="text-xs sm:text-sm text-gray-400 mt-1">Other movies you must know</p>
            </h1>
        </div>
        <div class="max-w-full mx-auto overflow-visible">
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6 md:gap-8 px-2 max-w-[95%] sm:max-w-[90%] mx-auto" id="others-grid">
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
        </script>
    </div> </x-app-layout>