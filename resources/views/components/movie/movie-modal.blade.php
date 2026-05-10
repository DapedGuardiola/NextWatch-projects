@props([
'poster' => null,
'tmdb_movie_id' => null,
'title' => null,
'year' => null,
'rating' => null,
'overview' => null,
'genres' => [],
'duration' => null,
])

<a href="{{ route('movie.detail',$tmdb_movie_id) }}" class="shrink-0 sm:w-56 cursor-pointer relative block group"data-card>

    {{-- Tampilan default: poster + title --}}
    <div class="rounded-2xl overflow-hidden aspect-[4/6] shadow-[6px_6px_0px_#5a5a5a]
                transition-all duration-300 delay-0
                group-hover:opacity-0 group-hover:delay-500">
        @isset($poster)
        <img src="{{ $poster }}"
            class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
            alt="{{ $title }}">
        @endisset
    </div>

    @isset($title)
    <p class="mt-2 text-sm font-medium text-center text-white px-1 truncate
                  transition-opacity duration-200 delay-0
                  group-hover:opacity-0 group-hover:delay-500">
        {{ trim($title) }}
    </p>
    @endisset

    {{-- Panel detail: muncul setelah 0.5 detik hover --}}
    <div class="absolute top-0 left-0 z-10 w-56 h-[260px]
                bg-[#1c1c1e] rounded-2xl overflow-hidden flex
                border border-white/10
                opacity-0 invisible pointer-events-none
                transition-all duration-300 delay-0
                group-hover:opacity-100 group-hover:visible group-hover: w-[560px] group-hover:pointer-events-auto group-hover:delay-500" data-panel>

        {{-- Poster kecil di kiri --}}
        @isset($poster)
        <div class="w-[173px] flex-shrink-0 overflow-hidden">
            <img src="{{ $poster }}" class="w-full h-full object-cover" alt="{{ $title }}">
        </div>
        @endisset

        {{-- Info di kanan --}}
        <div class="flex flex-col gap-2 p-4 flex-1 overflow-hidden">
            @isset($title)
            <p class="text-white font-semibold text-[15px] leading-snug">{{ trim($title) }}</p>
            @endisset

            <div class="flex gap-2 flex-wrap">
                @isset($year)
                <span class="text-xs text-white/40 bg-white/10 px-2 py-0.5 rounded">{{ $year }}</span>
                @endisset
                @isset($duration)
                <span class="text-xs text-white/40 bg-white/10 px-2 py-0.5 rounded">{{ $duration }}</span>
                @endisset
            </div>

            @if(!empty($genres))
            <div class="flex gap-1 flex-wrap">
                @foreach($genres as $genre)
                <span class="text-[11px] text-amber-400 bg-amber-400/10 border border-amber-400/30 px-2 py-0.5 rounded-full">
                    {{ $genre }}
                </span>
                @endforeach
            </div>
            @endif

            @isset($overview)
            <p class="text-xs text-white/60 leading-relaxed line-clamp-4">{{ $overview }}</p>
            @endisset

            @isset($rating)
            <div class="mt-auto flex items-center gap-1.5 text-xs text-white/50">
                <span class="text-amber-400 tracking-wider">★★★★☆</span>
                <span>{{ $rating }} / 10</span>
            </div>
            @endisset
        </div>
    </div>
</a>

<script>
document.querySelectorAll('[data-card]').forEach(card => {
    card.addEventListener('mouseenter', () => {
        const panel = card.querySelector('[data-panel]');
        const rect = card.getBoundingClientRect();
        const panelWidth = 560;

        if (rect.right + panelWidth > window.innerWidth) {
            panel.style.left = 'auto';
            panel.style.right = '0';
        } else {
            panel.style.left = '0';
            panel.style.right = 'auto';
        }
    });
});
</script>