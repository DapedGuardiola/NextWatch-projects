@props(['movie'])

<div class="bg-[#0B1120] rounded-3xl p-8 border border-cyan-400/20 shadow-2xl">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- POSTER -->
        <div>

            <img
                src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_path }}"
                alt="{{ $movie->title }}"
                class="rounded-2xl w-full object-cover shadow-2xl"
            >

        </div>

        <!-- DETAIL -->
        <div class="lg:col-span-2 flex flex-col justify-between">

            <div>

                <h1 class="text-5xl font-bold leading-tight">
                    {{ $movie->title }}
                </h1>

                <p class="text-cyan-300 mt-2 italic">
                    {{ $movie->tagline }}
                </p>

                <div class="flex flex-wrap gap-3 mt-5 text-sm">

                    <div class="bg-cyan-500/20 px-3 py-1 rounded-lg">
                        {{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}
                    </div>

                    <div class="bg-cyan-500/20 px-3 py-1 rounded-lg">
                        {{ $movie->runtime }} min
                    </div>

                    <div class="bg-yellow-500/20 px-3 py-1 rounded-lg">
                        ⭐ {{ $movie->rating }}
                    </div>

                </div>

                <div class="mt-6 text-gray-300 leading-relaxed max-w-3xl">
                    {{ $movie->overview }}
                </div>

            </div>

            <div class="mt-8">
                <x-movie.action-buttons />
            </div>

        </div>

    </div>

</div>