<div class="shrink-0 w-56 sm:w-64 flex flex-col">
    @isset($poster)
        <div class="relative h-72 sm:h-80 rounded-xl overflow-hidden">
            @isset($rank)
                @php
                    $rankInt = (int)(string)$rank;
                    $rankColor = match($rankInt) {
                        1 => 'bg-yellow-400 text-yellow-900',   // Emas
                        2 => 'bg-gray-300 text-gray-800',       // Perak
                        3 => 'bg-amber-600 text-amber-100',     // Bronze
                        default => 'bg-blue-500 text-white',    // Lainnya
                    };
                @endphp
                <div class="absolute top-2 left-2 z-10 {{ $rankColor }} text-xs font-bold px-2 py-1 rounded">
                    #{{ $rank }}
                </div>
            @endisset
            <img src="{{ $poster }}" class="w-full h-full object-cover" alt="{{ $title ?? '' }}">
        </div>
    @endisset
    @isset($title)
        <div class="flex text-sm mt-2"><span class="self-center mx-auto">{{ $title }}</span></div>
    @endisset
</div>