@props(['poster' => null, 'title' => null])

<div class="shrink-0 w-44 flex flex-col cursor-pointer group">
    @isset($poster)
        <div class="rounded-2xl overflow-hidden aspect-[2/3] shadow-[6px_6px_0px_#5a5a5a] group-hover:shadow-[6px_6px_20px_rgba(0,0,0,0.8)] transition-all duration-300">
            <img src="{{ $poster }}"
                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                alt="{{ $title }}">
        </div>
    @endisset

    @isset($title)
        <p class="mt-2 text-sm font-medium text-center text-black px-1 truncate">
            {{ trim($title) }}
        </p>
    @endisset
</div>