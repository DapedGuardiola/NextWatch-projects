<x-app-layout>

    <div class="bg-[#020817] text-white overflow-hidden">

        <!-- HERO SECTION -->
        <section class="relative overflow-hidden">

            <!-- BACKGROUND -->
            <div
                class="absolute inset-0 h-[1100px] bg-cover bg-center blur-2xl scale-110"
                style="background-image:url('https://image.tmdb.org/t/p/original/{{ $movie->backdrop_path ?? $movie->poster_path }}')">
            </div>

            <!-- OVERLAY -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#020817]/95 via-[#020817]/75 to-cyan-950/30"></div>

            <!-- SMOOTH GRADIENT -->
            <div class="absolute bottom-0 left-0 w-full h-[420px]
                bg-gradient-to-b
                from-transparent
                via-[#020817]/70
                to-[#020817]">
            </div>

            <!-- GLOW -->
            <div class="absolute -top-32 -right-32 w-[400px] h-[400px] bg-cyan-500/20 rounded-full blur-3xl"></div>

            <div class="relative z-10 max-w-[90%] mx-auto px-6 py-14">

                <div class="grid grid-cols-2 lg:grid-cols-[380px_1fr] gap-12 items-center">

                    <!-- POSTER -->
                    <div class="group relative max-w-[500px]">

                        <div class="absolute inset-0 bg-cyan-400/20 blur-3xl rounded-[32px] scale-90"></div>

                        <img
                            src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_path }}"
                            alt="{{ $movie->title }}"
                            class="relative z-10 w-full rounded-[32px]
                            shadow-[0_20px_80px_rgba(0,0,0,0.8)]
                            border border-white/10 object-cover
                            transition duration-500 group-hover:scale-[1.03]
                            aspect-[4/6]"
                        >

                    </div>

                    <!-- CONTENT -->
                    <div class="flex flex-col justify-between">

                        <div>

                            <!-- TITLE -->
                            <h1 class="text-5xl lg:text-7xl font-black leading-none tracking-tight">
                                {{ $movie->title }}
                            </h1>

                            <!-- TAGLINE -->
                            <p class="mt-5 text-cyan-300 text-lg italic tracking-wide">
                                {{ $movie->tagline }}
                            </p>

                            <!-- TOP META -->
                            <div class="flex flex-wrap items-center gap-3 mt-8 text-sm">

                                <!-- IMDb -->
                                <div class="flex items-center gap-2 px-4 py-2 rounded-xl
                                    bg-yellow-500/10 border border-yellow-400/20 text-yellow-300">

                                    <span class="font-black tracking-wide">
                                        IMDb
                                    </span>

                                    <span class="text-white font-semibold">
                                        {{ $movie->rating }}
                                    </span>

                                </div>

                                <!-- Year -->
                                <div class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-gray-200">
                                    {{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}
                                </div>

                                <!-- Runtime -->
                                <div class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-gray-200">
                                    {{ $movie->runtime }} min
                                </div>

                                <!-- Language -->
                                <div class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 uppercase text-gray-200">
                                    {{ $movie->original_language }}
                                </div>

                            </div>

                            <!-- CAST INFO -->
                            <div class="mt-8 space-y-4 max-w-3xl">

                                <!-- Director -->
                                <div class="flex gap-3">

                                    <span class="text-gray-500 min-w-[90px]">
                                        Director
                                    </span>

                                    <span class="text-white font-medium">
                                        {{ $movie->director ?? 'Unknown Director' }}
                                    </span>

                                </div>

                                <!-- Starring -->
                                <div class="flex gap-3">

                                    <span class="text-gray-500 min-w-[90px]">
                                        Starring
                                    </span>

                                    <span class="text-white font-medium">

                                        {{ $movie->actors->take(3)->pluck('name')->implode(', ') }}

                                    </span>

                                </div>

                            </div>

                            <!-- OVERVIEW -->
                            <div class="mt-10 max-w-3xl">

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

                                    @foreach($genreNames as $genre)

                                        <div class="px-4 py-2 rounded-full 
                                            bg-cyan-500/10 border border-cyan-400/20
                                            backdrop-blur-xl
                                            hover:bg-cyan-500/20
                                            text-cyan-200 text-sm
                                            transition duration-300">

                                            {{ is_object($genre) ? $genre->name : $genre }}

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="flex flex-col sm:flex-row gap-3 md:gap-4 pt-6">

                            <!-- WATCHLIST -->
                            @if($isInWatchlist)

                            <form action="{{ route('watchlist.destroy', $movie->tmdb_movie_id) }}" method="POST">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="group relative px-6 md:px-8 py-3 md:py-4 rounded-lg font-semibold text-base
                                    bg-red-500/20 hover:bg-red-500/30
                                    border border-red-400/30
                                    text-red-300 transition-all duration-300
                                    flex items-center justify-center gap-2">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2">

                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12"/>

                                    </svg>

                                    Remove Watchlist

                                </button>

                            </form>

                            @else

                            <form action="{{ route('watchlist.store', $movie->tmdb_movie_id) }}" method="POST">

                                @csrf

                                <button
                                    type="submit"
                                    class="group relative px-6 md:px-8 py-3 md:py-4 rounded-lg font-semibold text-base
                                    bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500
                                    text-black transition-all duration-300 shadow-lg hover:shadow-cyan-500/50
                                    overflow-hidden flex items-center justify-center gap-2">

                                    <span class="relative z-10 flex items-center gap-2">

                                        <svg class="w-5 h-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 4v16m8-8H4"/>

                                        </svg>

                                        Add Watchlist

                                    </span>

                                </button>

                            </form>

                            @endif

                            <!-- FAVORITE -->
                            @if($isFavorite)

                            <form action="{{ route('favorite.destroy', $movie->tmdb_movie_id) }}" method="POST">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="group relative px-6 md:px-8 py-3 md:py-4 rounded-lg font-semibold text-base
                                    bg-red-500/20 hover:bg-red-500/30
                                    border border-red-400/30
                                    text-red-300 transition-all duration-300
                                    flex items-center justify-center gap-2">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5"
                                        fill="currentColor"
                                        viewBox="0 0 24 24">

                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>

                                    </svg>

                                    Remove Favorite

                                </button>

                            </form>

                            @else

                            <form action="{{ route('favorite.store', $movie->tmdb_movie_id) }}" method="POST">

                                @csrf

                                <button
                                    type="submit"
                                    class="group relative px-6 md:px-8 py-3 md:py-4 rounded-lg font-semibold text-base
                                    bg-white/10 hover:bg-white/15 border border-white/20 hover:border-purple-400/50
                                    text-white transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/20
                                    flex items-center justify-center gap-2">

                                    <span class="relative z-10 flex items-center gap-2">

                                        <svg class="w-5 h-5"
                                            fill="currentColor"
                                            viewBox="0 0 24 24">

                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>

                                        </svg>

                                        Favorite

                                    </span>

                                </button>

                            </form>

                            @endif

                            <!-- SHARE -->
                            <button onclick="copyShareLink()"
                                class="group relative px-6 md:px-8 py-3 md:py-4 rounded-lg font-semibold text-base
                                bg-white/5 hover:bg-white/10
                                border border-white/10 hover:border-cyan-400/40
                                text-gray-100 transition-all duration-300
                                hover:text-cyan-200 hover:shadow-lg hover:shadow-cyan-500/20
                                flex items-center justify-center gap-2">

                                <span class="relative z-10 flex items-center gap-2">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.4"
                                        stroke-linecap="round"
                                        stroke-linejoin="round">

                                        <circle cx="18" cy="5" r="3"></circle>
                                        <circle cx="6" cy="12" r="3"></circle>
                                        <circle cx="18" cy="19" r="3"></circle>

                                        <line x1="8.7" y1="10.7" x2="15.3" y2="6.3"></line>
                                        <line x1="8.7" y1="13.3" x2="15.3" y2="17.7"></line>

                                    </svg>

                                    Share

                                </span>

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- CONTENT SECTION -->
        <div class="relative z-10 bg-[#020817]">

            <div class="max-w-7xl mx-auto px-6 py-14">

                <!-- COMMENTS -->
                <section>

                    <div class="relative rounded-[36px] border border-white/10 bg-[#0A0F1F]/60 backdrop-blur-2xl overflow-hidden">

                        <!-- ambient glow -->
                        <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-cyan-500/10 blur-3xl rounded-full"></div>
                        <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-purple-500/10 blur-3xl rounded-full"></div>

                        <div class="relative p-8 md:p-10">

                            <!-- HEADER -->
                            <div class="flex justify-between items-start mb-8">

                                <div>
                                    <h2 class="text-3xl font-bold tracking-tight">
                                        Discussion
                                    </h2>

                                    <p class="text-gray-400 text-sm mt-1">
                                        Join the conversation with other viewers
                                    </p>
                                </div>

                                <div class="px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm text-gray-300">
                                    {{ isset($comments) ? count($comments) : 0 }} comments
                                </div>

                            </div>

                            <!-- COMMENT INPUT -->
                            <form action="{{ route('comments.store', $movie->tmdb_movie_id) }}" method="POST">

                                @csrf

                                <div class="flex gap-4 mb-10">

                                    <div class="w-10 h-10 rounded-full bg-cyan-500 text-black font-bold flex items-center justify-center">

                                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}

                                    </div>

                                    <div class="flex-1">

                                        <textarea
                                            name="content"
                                            rows="2"
                                            placeholder="Add a comment..."
                                            class="w-full bg-transparent border-b border-white/10 focus:border-cyan-400 outline-none text-gray-200 placeholder-gray-500 resize-none pb-2"
                                        ></textarea>

                                        <div class="flex justify-end mt-3">

                                            <button
                                                type="submit"
                                                class="px-5 py-2 rounded-lg bg-cyan-500 text-black font-semibold hover:bg-cyan-400 transition">

                                                Comment

                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </form>

                            <!-- COMMENTS LIST -->
                            <div class="space-y-8">

                                @if(isset($comments) && count($comments))

                                    @foreach($comments as $comment)

                                        <div class="flex gap-4 group">

                                            <div class="flex-shrink-0">

                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-400 to-cyan-600
                                                            text-black font-bold flex items-center justify-center
                                                            shadow-[0_0_20px_rgba(34,211,238,0.35)]">

                                                    {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}

                                                </div>

                                            </div>

                                            <div class="flex-1">

                                                <div class="flex items-center gap-3">

                                                    <h3 class="font-semibold text-sm text-white">
                                                        {{ $comment->user->name ?? 'Unknown User' }}
                                                    </h3>

                                                    <span class="text-sm text-gray-500">
                                                        {{ $comment->created_at->diffForHumans() }}
                                                    </span>

                                                </div>

                                                <p class="mt-2 text-gray-200 text-[16px] leading-relaxed">
                                                    {{ $comment->content }}
                                                </p>

                                                <div class="flex gap-6 mt-3 text-sm text-gray-500">

                                                    <button class="hover:text-cyan-300 transition">
                                                        Like
                                                    </button>

                                                    <button class="hover:text-cyan-300 transition">
                                                        Reply
                                                    </button>

                                                    <button class="hover:text-red-400 transition">
                                                        Report
                                                    </button>

                                                </div>

                                            </div>

                                        </div>

                                    @endforeach

                                @else

                                    <div class="text-gray-400 text-sm">
                                        No comments yet. Start the discussion.
                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>

                </section>

                <!-- SIMILAR MOVIES -->
                <section class="mt-14">

                    <div class="flex justify-between items-center mb-6">

                        <h2 class="text-3xl font-bold">
                            Similar Movies
                        </h2>

                    </div>

                    <div class="flex gap-6 overflow-x-auto scrollbar-hide pb-6">

                        @foreach($similarMovies as $s)

                            <div class="group min-w-[220px]">

                                <div
                                    class="relative overflow-hidden rounded-[28px]
                                    border border-white/10
                                    transition duration-500
                                    group-hover:-translate-y-2
                                    group-hover:shadow-[0_20px_60px_rgba(34,211,238,0.25)]">

                                    <img
                                        src="https://image.tmdb.org/t/p/w500/{{ $s->poster_path }}"
                                        alt="{{ $s->title }}"
                                        class="w-full h-[330px] object-cover
                                        transition duration-500
                                        group-hover:scale-110"
                                    >

                                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>

                                    <div class="absolute bottom-0 p-5">

                                        <h3 class="font-semibold text-lg">
                                            {{ $s->title }}
                                        </h3>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </section>

            </div>

        </div>

    </div>

</x-app-layout>