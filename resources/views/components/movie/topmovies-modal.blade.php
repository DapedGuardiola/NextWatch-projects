@props([
'poster' => null,
'tmdb_movie_id' => null,
'title' => null,
'year' => null,
'rating' => null,
'overview' => null,
'genres' => [],
'duration' => null,
'rank' => null,
])

<a href="{{ route('movie.detail',$tmdb_movie_id) }}"  onclick="logClick({{ $tmdb_movie_id }})" loading="lazy"
   class="shrink-0 w-24 md:w-44 lg:w-56 cursor-pointer relative block group" data-card>

    @isset($rank)
        @php
            $rankInt = (int)(string)$rank;
            $rankColor = match($rankInt) {
                1 => 'bg-yellow-400 text-yellow-900',
                2 => 'bg-gray-300 text-gray-800',
                3 => 'bg-amber-600 text-amber-100',
                default => 'bg-cyan-500 text-black',
            };
        @endphp
        <div class="absolute top-2 left-2 md:top-3 md:left-3 z-10 {{ $rankColor }}
            text-[10px] md:text-xs font-bold px-2 md:px-3 py-0.5 md:py-1 rounded-full shadow-lg">
            #{{ $rank }}
        </div>
    @endisset

    <div class="relative rounded-2xl aspect-[4/6] overflow-hidden border border-white/10
        transition duration-500
        [@media(hover:hover)]:md:group-hover:opacity-0
        [@media(hover:hover)]:md:group-hover:delay-500">

        @isset($poster)
            <img
                src="{{ $poster }}"
                class="w-full h-full object-cover transition duration-500
                       [@media(hover:hover)]:md:group-hover:scale-110"
                alt="{{ $title ?? '' }}"
            >
        @endisset

        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/10 to-transparent"></div>

        @isset($title)
            <div class="absolute bottom-0 p-2 md:p-3 lg:p-4">
                <h3 class="text-white font-semibold text-xs md:text-base lg:text-lg line-clamp-2">
                    {{ $title }}
                </h3>
            </div>
        @endisset
    </div>

    {{-- Hover detail panel --}}
    <div class="absolute top-0 left-0 z-20
                md:w-44 lg:w-56 h-[200px] md:h-[230px] lg:h-[260px]
                bg-[#1c1c1e] rounded-2xl overflow-hidden flex
                border border-white/10
                opacity-0 invisible pointer-events-none
                transition-all duration-300 delay-0
                [@media(hover:hover)]:group-hover:opacity-100
                [@media(hover:hover)]:group-hover:visible
                [@media(hover:hover)]:md:group-hover:w-[460px]
                [@media(hover:hover)]:lg:group-hover:w-[560px]
                [@media(hover:hover)]:md:group-hover:h-[280px]
                [@media(hover:hover)]:lg:group-hover:h-[335px]
                [@media(hover:hover)]:group-hover:pointer-events-auto
                [@media(hover:hover)]:group-hover:delay-500" data-panel>

        {{-- Poster kecil di kiri --}}
        @isset($poster)
        <div class="md:w-[180px] lg:w-[225px] flex-shrink-0 overflow-hidden">
            <img src="{{ $poster }}" class="w-full h-full object-cover" alt="{{ $title }}">
        </div>
        @endisset

        {{-- Info di kanan --}}
        <div class="flex flex-col gap-1.5 md:gap-2 p-3 md:p-4 flex-1 overflow-hidden">
            @isset($title)
            <p class="text-white font-semibold md:text-[16px] lg:text-[18px] leading-snug">{{ trim($title) }}</p>
            @endisset

            <div class="flex gap-1.5 md:gap-2 flex-wrap">
                @isset($year)
                <span class="md:text-xs text-white/40 bg-white/10 px-1.5 md:px-2 py-0.5 rounded">{{ $year }}</span>
                @endisset
                @isset($duration)
                <span class="md:text-xs text-white/40 bg-white/10 px-1.5 md:px-2 py-0.5 rounded">{{ $duration }} minutes</span>
                @endisset
            </div>

            @if(!empty($genres))
            <div class="flex gap-1 flex-wrap">
                @foreach($genres as $genre)
                <span class="md:text-[11px] text-amber-400 bg-amber-400/10 border border-amber-400/30 px-1.5 md:px-2 py-0.5 rounded-full">
                    {{ $genre }}
                </span>
                @endforeach
            </div>
            @endif

            @isset($overview)
            <p class="md:text-xs text-white/60 leading-relaxed line-clamp-[5] md:line-clamp-[7] lg:line-clamp-[11]">{{ $overview }}</p>
            @endisset

            @isset($rating)
            <div class="mt-auto flex items-center md:gap-1.5 md:text-xs text-white/50">
                <span class="text-amber-400 tracking-wider">★★★★☆</span>
                <span>{{ $rating }} / 10</span>
            </div>
            @endisset
        </div>
    </div>

</a>

<script>
// Hanya jalankan hover logic jika device support hover (bukan touchscreen)
if (window.matchMedia('(hover: hover)').matches) {
    document.querySelectorAll('[data-card]').forEach(card => {
        card.addEventListener('mouseenter', () => {
            const panel = card.querySelector('[data-panel]');
            const rect = card.getBoundingClientRect();
            const panelWidth = window.innerWidth >= 1024 ? 560 : window.innerWidth >= 768 ? 460 : 360;

            if (rect.right + panelWidth > window.innerWidth) {
                panel.style.left  = 'auto';
                panel.style.right = '0';
            } else {
                panel.style.left  = '0';
                panel.style.right = 'auto';
            }
        });
    });
}

function logClick(movieId) {
    fetch('/log-activity', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            type: 'click',
            tmdb_movie_id: movieId
        })
    });
}
</script>