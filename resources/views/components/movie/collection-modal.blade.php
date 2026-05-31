@props([
'poster' => null,
'tmdb_collection_id' => null,
'name' => null,
'overview' => null,
])

<a href="{{ route('collection.detail',$tmdb_collection_id) }}" loading="lazy" class="shrink-0 cursor-pointer relative block group"data-card>

    <div class="relative rounded-2xl w-[500px] shrink-0 aspect-[16/9] overflow-hidden border border-white/10
        transition duration-500 group-hover:opacity-0 group-hover:delay-500">

        @isset($poster)

            <img
                src="{{ $poster }}"
                class="w-full h-full object-cover object-center transition duration-500 group-hover:scale-110"
                alt="{{ $name ?? '' }}"
            >

        @endisset

        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/10 to-transparent"></div>

        @isset($name)

            <div class="absolute bottom-0 p-4">

                <h3 class="text-white font-semibold text-lg">
                    {{ $name }}
                </h3>

            </div>

        @endisset

    </div>

    {{-- Panel detail: muncul setelah 0.5 detik hover --}}
    
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