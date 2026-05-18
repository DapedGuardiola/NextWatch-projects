<x-app-layout>

<div class="min-h-screen bg-[#020817] text-white p-10">

    <h1 class="text-5xl font-black mb-10">
        My Watchlist
    </h1>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">

        @foreach($watchlists as $item)

            <a href="{{ route('movie.detail', $item->movie->tmdb_movie_id) }}">

                <div class="group">

                    <img
                        src="https://image.tmdb.org/t/p/w500/{{ $item->movie->poster_path }}"
                        class="rounded-3xl overflow-hidden transition duration-300 group-hover:scale-105"
                    >

                    <h2 class="mt-4 font-semibold text-lg">
                        {{ $item->movie->title }}
                    </h2>

                </div>

            </a>

        @endforeach

    </div>

</div>

</x-app-layout>