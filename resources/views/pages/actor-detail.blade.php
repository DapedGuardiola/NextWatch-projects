<x-app-layout>
    <x-slot name="title">
        {{ $actorsData->name }} - Actor Profile
    </x-slot>

    <div class="min-h-screen relative bg-[#0b0c10] pb-20 font-sans">

        {{-- Background Glow --}}
        <div class="absolute top-0 left-0 right-0 h-[60vh] bg-gradient-to-b from-[#1f2833]/40 to-[#0b0c10] pointer-events-none"></div>

        <div class="max-w-full mx-auto px-6 pt-14 relative z-10 justify-items-center">

            {{-- HERO SECTION --}}
            <div class="w-full md:w-[95%] mb-20">

                {{-- MOBILE LAYOUT --}}
                <div class="flex flex-col gap-5 md:hidden">

                    {{-- 1. Foto + Nama + Info --}}
                    <div class="flex gap-4 items-start">

                        {{-- Foto --}}
                        <div class="w-28 flex-shrink-0 relative">
                            <div class="absolute inset-0 bg-cyan-400/15 blur-2xl rounded-[20px] scale-90"></div>
                            <img src="{{ $actorsData->image_url }}"
                                alt="{{ $actorsData->name }}"
                                class="relative z-10 w-full rounded-[20px] shadow-xl border border-white/10 object-cover aspect-[2/3]">
                        </div>

                        {{-- Nama + Meta --}}
                        <div class="flex-1 flex flex-col gap-2.5 pt-1">
                            <h1 class="text-2xl font-black text-white leading-tight tracking-tight">{{ $actorsData->name }}</h1>

                            <div class="flex flex-wrap gap-1.5">
                                @if($actorsData->gender ?? false)
                                <span class="px-2.5 py-1 rounded-lg bg-purple-500/10 border border-purple-400/20 text-purple-300 text-xs">
                                    {{ $actorsData->gender == 2 ? 'Actor' : 'Actress' }}
                                </span>
                                @endif
                                <span class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-yellow-500/10 border border-yellow-400/20 text-yellow-300 text-xs">
                                    <span class="font-black">Popularity</span>
                                    <span class="text-white font-semibold">{{ number_format($actorsData->popularity ?? 0, 1) }}</span>
                                </span>
                                @if($actorsData->birthday ?? false)
                                <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-gray-300 text-xs">
                                    Born: {{ date('Y', strtotime($actorsData->birthday)) }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 2. Biography --}}
                    <div>
                        <p class="text-cyan-300 text-sm italic font-semibold mb-2">Biography</p>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ $actorsData->biography ?? 'Biografi terperinci untuk ' . $actorsData->name . ' belum tersedia.' }}
                        </p>
                    </div>

                </div>

                {{-- DESKTOP LAYOUT --}}
                <div class="hidden md:flex flex-col md:flex-row gap-10">

                    {{-- Kiri: Foto Aktor --}}
                    <div class="w-full md:w-[320px] flex-shrink-0">
                        <div class="rounded-3xl overflow-hidden shadow-[0_0_40px_rgba(69,162,158,0.15)] border border-white/5 bg-[#141518] aspect-[2/3]">
                            <img src="{{ $actorsData->image_url }}" alt="{{ $actorsData->name }}" class="w-full h-full object-cover">
                        </div>
                    </div>

                    {{-- Kanan: Detail --}}
                    <div class="flex-grow pt-4">
                        <h1 class="text-5xl md:text-6xl font-black text-white tracking-tight mb-6">{{ $actorsData->name }}</h1>

                        <div class="flex flex-wrap gap-3 mb-8">
                            @if($actorsData->gender ?? false)
                            <span class="px-4 py-1.5 rounded-lg bg-purple-500/10 border border-purple-400/20 text-purple-300 text-sm">
                                {{ $actorsData->gender == 2 ? 'Actor' : 'Actress' }}
                            </span>
                            @endif
                            <span class="flex items-center gap-1 px-4 py-1.5 rounded-lg bg-yellow-500/10 border border-yellow-400/20 text-yellow-300 text-sm">
                                <span class="font-black">Popularity</span>
                                <span class="text-white font-semibold">{{ number_format($actorsData->popularity ?? 0, 1) }}</span>
                            </span>
                            @if($actorsData->birthday ?? false)
                            <span class="px-4 py-1.5 rounded-lg bg-white/5 border border-white/10 text-gray-300 text-sm">
                                Born: {{ date('Y', strtotime($actorsData->birthday)) }}
                            </span>
                            @endif
                        </div>

                        <div class="mb-4">
                            <h3 class="text-[#45a29e] text-lg font-bold mb-3 italic">Biography</h3>
                            <p class="text-gray-300 leading-relaxed text-justify opacity-90 max-w-4xl">
                                {{ $actorsData->biography ?? 'Biografi terperinci untuk ' . $actorsData->name . ' belum tersedia di pangkalan data saat ini.' }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- 2. GRID FILM (MOVIES STARRED) --}}
            <div class="mb-20">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl sm:text-3xl font-bold text-white flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-[#66fcf1] rounded-full"></span>
                        Movies Starred By {{ $actorsData->name }}
                    </h2>
                </div>

                {{-- Grid Film --}}
                <div class="max-w-full mx-auto px-10 justify-items-center scrollbar-hide">
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5
                        gap-3 sm:gap-6 md:gap-8
                        w-full mx-auto justify-items-center">
                        @foreach($actorsData->actormovies as $actormovies)
                        @if($actormovies->movies)
                        <x-movie.movie-modal
                            :poster="$actormovies->movies->poster_url"
                            :title="$actormovies->movies->title"
                            :tmdb_movie_id="$actormovies->movies->tmdb_movie_id"
                            :year="$actormovies->movies->release_date ? date('Y', strtotime($actormovies->movies->release_date)) : null"
                            :rating="$actormovies->movies->rating ?? null"
                            :overview="$actormovies->movies->overview ?? null"
                            :genres="$actormovies->movies->genres->pluck('genre.name')->filter()->toArray() ?? []"
                            :duration="$actormovies->movies->runtime ?? null" />
                        @endif
                    @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. GRID SIMILAR ACTORS (BERDASARKAN IRISAN GENRE) --}}
            <div class="w-full pt-8 border-t border-white/10">
                <div class="mb-8">
                    <h2 class="text-xl sm:text-3xl font-bold text-white flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-[#45a29e] rounded-full"></span>
                        Similar Actors
                    </h2>
                    <p class="text-gray-400 text-xs sm:text-sm mt-1 ml-4">Actors who frequently play in the same genres.</p>
                </div>

                <div class="flex gap-8 max-w-[90%] overflow-hidden mx-auto overflow-x-auto scrollbar-hide">
                    @forelse($similarActors as $similar)
                    <x-movie.actor-card
                        :actor_id="$similar->tmdb_actor_id"
                        :image_url="$similar->image_url"
                        :name="$similar->name"/>
                    @empty
                        <p class="text-gray-500 italic">No similar actors found based on genre intersection.</p>
                    @endforelse
                </div>

            </div>

        </div>
    </div>

    {{-- Styling untuk menyembunyikan scrollbar bawaan di deretan similar actors --}}
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</x-app-layout>