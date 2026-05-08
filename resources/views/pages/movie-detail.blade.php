<x-app-layout>

    <div class="min-h-screen bg-[#020817] text-white px-6 py-8">

        <div class="max-w-7xl mx-auto">

            <!-- MAIN DETAIL -->
            <div class="bg-[#0B1120] rounded-3xl p-8 border border-cyan-400/20 shadow-2xl">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- POSTER -->
                    <div>
                        <img
                            src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_path }}"
                            alt="{{ $movie->title }}"
                            class="w-full rounded-2xl object-cover shadow-2xl"
                        >
                    </div>

                    <!-- CONTENT -->
                    <div class="lg:col-span-2 flex flex-col justify-between">

                        <div>

                            <!-- TITLE -->
                            <h1 class="text-4xl lg:text-5xl font-bold leading-tight">
                                {{ $movie->title }}
                            </h1>

                            <!-- TAGLINE -->
                            <p class="mt-2 text-cyan-300 italic">
                                {{ $movie->tagline }}
                            </p>

                            <!-- META -->
                            <div class="flex flex-wrap gap-3 mt-5">

                                <div class="bg-cyan-500/20 px-4 py-2 rounded-xl text-sm">
                                    {{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}
                                </div>

                                <div class="bg-cyan-500/20 px-4 py-2 rounded-xl text-sm">
                                    {{ $movie->runtime }} min
                                </div>

                                <div class="bg-yellow-500/20 px-4 py-2 rounded-xl text-sm">
                                    ⭐ {{ $movie->rating }}
                                </div>

                            </div>

                            <!-- OVERVIEW -->
                            <div class="mt-8">

                                <h2 class="text-xl font-semibold mb-3">
                                    Overview
                                </h2>

                                <p class="text-gray-300 leading-relaxed">
                                    {{ $movie->overview }}
                                </p>

                            </div>

                            <!-- GENRES -->
                            <div class="mt-6">

                                <h2 class="text-xl font-semibold mb-3">
                                    Genres
                                </h2>

                                <div class="flex flex-wrap gap-3">

                                    @if(!empty($movie->genres) && count($movie->genres))

                                        @foreach($movie->genres as $genre)

                                            <div class="bg-white/10 px-4 py-2 rounded-xl text-sm">
                                                {{ is_object($genre) ? $genre->name : $genre }}
                                            </div>

                                        @endforeach

                                    @else

                                        <div class="bg-white/10 px-4 py-2 rounded-xl text-sm">
                                            Action
                                        </div>

                                        <div class="bg-white/10 px-4 py-2 rounded-xl text-sm">
                                            Adventure
                                        </div>

                                        <div class="bg-white/10 px-4 py-2 rounded-xl text-sm">
                                            Sci-Fi
                                        </div>

                                    @endif

                                </div>

                            </div>

                        </div>

                        <!-- BUTTONS -->
                        <div class="flex gap-4 flex-wrap mt-8">

                            <button class="px-5 py-3 rounded-xl bg-cyan-500 hover:bg-cyan-400 transition font-semibold">
                                + Watchlist
                            </button>

                            <button class="px-5 py-3 rounded-xl bg-pink-500 hover:bg-pink-400 transition font-semibold">
                                ❤ Favorite
                            </button>

                            <button
                                onclick="copyShareLink()"
                                class="px-5 py-3 rounded-xl bg-gray-700 hover:bg-gray-600 transition font-semibold"
                            >
                                ↗ Share
                            </button>

                        </div>

                    </div>

                </div>

            </div>

            <!-- COMMENTS -->
            <div class="mt-10 bg-[#0B1120] rounded-3xl p-8 border border-cyan-400/20">

                <div class="flex justify-between items-center mb-6">

                    <h2 class="text-2xl font-bold">
                        Comments
                    </h2>

                    <span class="text-gray-400">
                        {{ isset($comments) ? count($comments) : 0 }} comments
                    </span>

                </div>

                <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">

                    @if(isset($comments) && count($comments))

                        @foreach($comments as $comment)

                            <div class="bg-[#111827] rounded-2xl p-5 border border-white/5">

                                <div class="flex gap-4">

                                    <div class="w-12 h-12 rounded-full bg-cyan-500 flex items-center justify-center font-bold text-black">

                                        {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}

                                    </div>

                                    <div class="flex-1">

                                        <div class="flex justify-between items-center">

                                            <h3 class="font-semibold">
                                                {{ $comment->user->name ?? 'Unknown User' }}
                                            </h3>

                                            <span class="text-gray-500 text-sm">
                                                {{ $comment->created_at ?? 'now' }}
                                            </span>

                                        </div>

                                        <p class="text-gray-300 mt-3 leading-relaxed">
                                            {{ $comment->body ?? 'No comment text' }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    @else

                        <div class="text-gray-400">
                            No comments yet.
                        </div>

                    @endif

                </div>

            </div>

            <!-- SIMILAR MOVIES -->
            <div class="mt-10">

                <h2 class="text-2xl font-bold mb-5">
                    Similar Movies
                </h2>

                <div class="flex gap-5 overflow-x-auto scrollbar-hide pb-4">

                    @foreach($similarMovies as $s)

                        <x-movie.movie-modal>

                            <x-slot name="poster">
                                https://image.tmdb.org/t/p/w500/{{ $s->poster_path }}
                            </x-slot>

                            <x-slot name="title">
                                {{ $s->title }}
                            </x-slot>

                        </x-movie.movie-modal>

                    @endforeach

                </div>

            </div>

        </div>

    </div>

    <script>
        function copyShareLink() {
            navigator.clipboard.writeText(window.location.href);
            alert('Link copied!');
        }
    </script>

</x-app-layout>