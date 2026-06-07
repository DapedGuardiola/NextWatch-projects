@props([
'poster' => null,
'tmdb_collection_id' => null,
'name' => null,
'overview' => null,
])

<a href="{{ route('collection.detail',$tmdb_collection_id) }}" loading="lazy" class="shrink-0 cursor-pointer relative block group" data-card>

    <div class="relative rounded-2xl w-[500px] shrink-0 aspect-[16/9] overflow-hidden border border-white/10
        transition duration-500 group-hover:opacity-0">

        @isset($poster)
            <img
                src="{{ $poster }}"
                class="w-full h-full object-cover object-center transition duration-700"
                alt="{{ $name ?? '' }}"
            >
        @endisset

        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/10 to-transparent"></div>

        @isset($name)
            <div class="absolute bottom-0 p-4">
                <h3 class="text-white font-semibold text-lg">{{ $name }}</h3>
            </div>
        @endisset

    </div>

    {{-- HOVER PANEL --}}
    <div class="absolute top-0 left-0 z-20
                w-[500px] h-[281px]
                rounded-2xl overflow-hidden
                border border-white/10
                opacity-0 invisible pointer-events-none
                transition-all duration-300 delay-0
                group-hover:opacity-100 group-hover:visible
                group-hover:pointer-events-auto" data-panel>

        {{-- Poster full --}}
        @isset($poster)
        <img src="{{ $poster }}" class="w-full h-full object-cover object-center" alt="{{ $name }}">
        @endisset

        {{-- Overlay gradient --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>

        {{-- Teks --}}
        <div class="absolute bottom-0 left-0 right-0 p-4 flex flex-col gap-1
                translate-y-[calc(0.75rem+3*1.2rem)] group-hover:translate-y-0
                transition-all duration-500 ease-out">

        @isset($name)
        <h3 class="text-white font-semibold text-lg leading-snug">{{ $name }}</h3>
        @endisset

        @isset($overview)
        <p class="text-xs text-white/70 leading-relaxed line-clamp-3
                opacity-0 group-hover:opacity-100
                transition-opacity duration-300 delay-200">
            {{ $overview }}
        </p>
        @endisset

    </div>

    </div>

</a>

<script>
document.querySelectorAll('[data-card]').forEach(card => {
    card.addEventListener('mouseenter', () => {
        const panel = card.querySelector('[data-panel]');
        if (!panel) return;
        const rect = card.getBoundingClientRect();
        const panelWidth = 500;

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