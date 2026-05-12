@props(['movie'])

<section class="relative overflow-hidden rounded-[40px] border border-cyan-400/10">

    <!-- BACKGROUND -->
    <div
        class="absolute inset-0 bg-cover bg-center blur-2xl scale-110"
        style="background-image:url('https://image.tmdb.org/t/p/original/{{ $movie->backdrop_path ?? $movie->poster_path }}')">
    </div>

    <!-- OVERLAY -->
    <div class="absolute inset-0 bg-gradient-to-br from-[#020817]/95 via-[#020817]/80 to-cyan-950/40"></div>

    <!-- LIGHT EFFECT -->
    <div class="absolute -top-32 -right-32 w-[400px] h-[400px] bg-cyan-500/20 rounded-full blur-3xl"></div>

    <div class="relative z-10 p-8 lg:p-14">

        <div class="grid grid-cols-1 lg:grid-cols-[380px_1fr] gap-12 items-center">

            <!-- POSTER -->
            <div class="group relative">

                <div class="absolute inset-0 bg-cyan-400/20 blur-3xl rounded-[32px] scale-90"></div>

                <img
                    src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_path }}"
                    alt="{{ $movie->title }}"
                    class="relative z-10 w-full rounded-[32px]
                    shadow-[0_20px_80px_rgba(0,0,0,0.8)]
                    border border-white/10 object-cover
                    transition duration-500
                    group-hover:scale-[1.03]"
                >

            </div>

            <!-- CONTENT -->
            <div class="flex flex-col justify-between">

                <div>

                    <h1 class="text-5xl lg:text-7xl font-black leading-none tracking-tight">
                        {{ $movie->title }}
                    </h1>

                    <p class="mt-5 text-cyan-300 text-lg italic tracking-wide">
                        {{ $movie->tagline }}
                    </p>

                    <!-- META -->
                    <div class="flex flex-wrap gap-4 mt-8">

                        <div class="px-5 py-3 rounded-2xl bg-white/5 backdrop-blur-xl border border-white/10">
                            {{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}
                        </div>

                        <div class="px-5 py-3 rounded-2xl bg-white/5 backdrop-blur-xl border border-white/10">
                            {{ $movie->runtime }} min
                        </div>

                        <div class="px-5 py-3 rounded-2xl bg-yellow-500/10 border border-yellow-400/20 text-yellow-300">
                            ⭐ {{ $movie->rating }}
                        </div>

                    </div>

                    <!-- OVERVIEW -->
                    <div class="mt-10 max-w-3xl">

                        <h2 class="text-2xl font-semibold mb-5">
                            Overview
                        </h2>

                        <p class="text-gray-300 text-lg leading-relaxed">
                            {{ $movie->overview }}
                        </p>

                    </div>

                    <!-- GENRES -->
                    <div class="mt-10">

                        <h2 class="text-2xl font-semibold mb-5">
                            Genres
                        </h2>

                        <div class="flex flex-wrap gap-4">

                            @foreach($movie->genres as $genre)

                                <div class="px-5 py-3 rounded-2xl
                                    bg-cyan-500/10 border border-cyan-400/20
                                    backdrop-blur-xl
                                    hover:bg-cyan-500/20
                                    transition duration-300">

                                    {{ is_object($genre) ? $genre->name : $genre }}

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>

                <div class="mt-10">
                    <x-movie.action-buttons />
                </div>

            </div>

        </div>

    </div>

</section>