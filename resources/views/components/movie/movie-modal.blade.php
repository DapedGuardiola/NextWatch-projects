<div class="group shrink-0 w-56 sm:w-64">

    <div class="relative rounded-2xl overflow-hidden border border-white/10
        transition duration-500 group-hover:-translate-y-2
        group-hover:shadow-[0_20px_60px_rgba(34,211,238,0.25)]">

        @isset($poster)

            <img
                src="{{ $poster }}"
                class="w-full h-80 object-cover transition duration-500 group-hover:scale-110"
                alt="{{ $title ?? '' }}"
            >

        @endisset

        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/10 to-transparent"></div>

        @isset($title)

            <div class="absolute bottom-0 p-4">

                <h3 class="text-white font-semibold text-lg">
                    {{ $title }}
                </h3>

            </div>

        @endisset

    </div>

</div>