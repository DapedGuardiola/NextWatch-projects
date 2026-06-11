<x-app-layout>
    <x-slot name="title">
        {{ __('Top Charted Movies') }}
    </x-slot>
    <div>
        <div class="mx-4 md:mx-8 lg:mx-10 mt-[75px] mb-5">
            <h1>
                <p class="text-2xl md:text-3xl text-white font-bold">You Must Know These Movies!!</p>
            </h1>
        </div>
        <div class="mx-4 md:mx-8 lg:mx-10 mb-5">
            <x-movie.popular-carousel :movies="$popularMovies" />
        </div>        
        
        @foreach($moviesByGenre as $genre => $movies)
            <div class="mx-4 sm:mx-10 my-6 sm:my-10">
                <h2>
                    <p class="text-xl sm:text-3xl text-white font-bold">{{ $genre }}</p>
                </h2>
            </div>

            <div class="max-w-[90%] mx-auto relative">
                <button id="prev_{{ $loop->index }}"
                    onclick="scrollByItems(document.getElementById('scroll_{{ $loop->index }}'), -1)"
                    class="hidden absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 items-center justify-center rounded-full bg-black/60 border border-white/10 text-white hover:bg-black/80 transition duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <div id="scroll_{{ $loop->index }}"
                    class="flex gap-4 md:gap-8 overflow-hidden overflow-x-auto scrollbar-hide scroll-smooth">
                    @foreach($movies as $index => $movie)
                        <x-movie.topmovies-modal
                            :poster="'https://image.tmdb.org/t/p/original/' . $movie['poster_path']"
                            :title="$movie['title']"
                            :tmdb_movie_id="$movie['id']"
                            :year="$movie['year'] ?? null"
                            :rating="$movie['rating'] ?? null"
                            :overview="$movie['overview'] ?? null"
                            :genres="$movie['genres'] ?? []"
                            :duration="$movie['runtime'] ?? null"
                            :rank="$index + 1" />
                    @endforeach
                </div>

                <button id="next_{{ $loop->index }}"
                    onclick="scrollByItems(document.getElementById('scroll_{{ $loop->index }}'), 1)"
                    class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-black/60 border border-white/10 text-white hover:bg-black/80 transition duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        @endforeach
    </div>

    <script>
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
                if (window.innerWidth < 640) {
                    prev.classList.add('hidden'); prev.classList.remove('flex');
                    next.classList.add('hidden'); next.classList.remove('flex');
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

        // Inisialisasi semua genre section secara otomatis
        document.querySelectorAll('[id^="scroll_"]').forEach(el => {
            const index = el.id.replace('scroll_', '');
            initScrollArrows(`scroll_${index}`, `prev_${index}`, `next_${index}`);
        });
    </script>
</x-app-layout>