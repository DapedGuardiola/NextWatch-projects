<div class="relative w-full max-w-xl mx-auto" x-data="searchBar()">

    {{-- Input --}}
    <div class="relative">
        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
        </div>
        <input
            type="text"
            placeholder="Search films..."
            x-model="query"
            @input.debounce.5ms="search()"
            @keydown.enter="goToResults()"
            @keydown.escape="close()"
            @focus="if(query.length >= 2) open = true"
            class="w-full pl-10 pr-4 py-2.5 rounded-full
                   bg-black/30 border border-white/10
                   text-sm text-white placeholder-white/30
                   focus:outline-none focus:ring-1 focus:ring-white/20 focus:border-white/20
                   transition">
    </div>

    {{-- Dropdown hasil live search --}}
    <div
        x-show="open && (movies.length > 0 || actors.length > 0)"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.outside="close()"
        class="absolute top-full mt-2 w-full min-w-[480px] bg-[#1c1c1e] rounded-2xl shadow-2xl border border-white/10 overflow-hidden z-50"
        x-cloak
    >
        <div class="flex">

            {{-- Kolom Movie --}}
            <div :class="actors.length > 0 ? 'w-2/3 border-r border-white/10' : 'w-full'" x-show="movies.length > 0">
                <p class="text-xs text-white/40 uppercase tracking-widest px-4 pt-4 pb-2">Films</p>
                <template x-for="movie in movies" :key="movie.id">
                    <a :href="movie.url" class="flex items-center gap-3 px-4 py-2.5 hover:bg-white/5 transition">
                        <img :src="movie.poster_url" class="w-8 h-12 object-cover rounded-lg flex-shrink-0" alt="">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-sm text-white truncate" x-text="movie.title"></p>
                            <p class="text-xs text-white/40" x-text="'★ ' + movie.rating"></p>
                        </div>
                    </a>
                </template>
            </div>

            {{-- Kolom Actor --}}
            <div :class="movies.length > 0 ? 'w-1/3' : 'w-full'" x-show="actors.length > 0">
                <p class="text-xs text-white/40 uppercase tracking-widest px-4 pt-4 pb-2">Actors</p>
                <template x-for="actor in actors" :key="actor.id">
                    <a :href="actor.url" class="flex items-center gap-3 px-4 py-2.5 hover:bg-white/5 transition">
                        <img :src="actor.image_url" class="w-8 h-8 object-cover rounded-full flex-shrink-0" alt="">
                        <p class="text-sm text-white truncate" x-text="actor.name"></p>
                    </a>
                </template>
            </div>

        </div>

        {{-- Footer --}}
        <div class="px-4 py-3 border-t border-white/10">
            <button @click="goToResults()"
                class="text-xs text-indigo-400 hover:text-indigo-300 transition">
                See all results for "<span x-text="query"></span>" →
            </button>
        </div>
    </div>

    <script>
    function searchBar() {
        return {
            query: '',
            movies: [],
            actors: [],
            open: false,

            async search() {
                if (this.query.length < 2) {
                    this.movies = [];
                    this.actors = [];
                    this.open = false;
                    return;
                }

                const res = await fetch(`/search/live?q=${encodeURIComponent(this.query)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                this.movies = data.movies;
                this.actors = data.actors;
                this.open = true;
            },

            goToResults() {
                if (this.query.length > 0) {
                    window.location.href = `/search?q=${encodeURIComponent(this.query)}`;
                }
            },

            close() {
                this.open = false;
            }
        }
    }
    </script>
</div>