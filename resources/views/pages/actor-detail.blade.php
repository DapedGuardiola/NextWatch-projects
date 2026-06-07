<x-app-layout>
    <x-slot name="title">
        {{ $actorsData->name }} - Actor Profile
    </x-slot>

    <div class="min-h-screen relative bg-[#0b0c10] pb-20 font-sans">
        
        {{-- Latar Belakang Hero (Efek Glow Lembut) --}}
        <div class="absolute top-0 left-0 right-0 h-[60vh] bg-gradient-to-b from-[#1f2833]/40 to-[#0b0c10] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 pt-24 relative z-10">
            
            {{-- 1. HERO SECTION (FOTO & BIOGRAFI) --}}
            <div class="flex flex-col md:flex-row gap-10 mb-20">
                
                {{-- Kiri: Foto Aktor --}}
                <div class="w-full md:w-[320px] flex-shrink-0">
                    <div class="rounded-3xl overflow-hidden shadow-[0_0_40px_rgba(69,162,158,0.15)] border border-white/5 bg-[#141518] aspect-[2/3]">
                        <img src="{{ $actorsData->image_url }}" alt="{{ $actorsData->name }}" class="w-full h-full object-cover">
                    </div>
                </div>

                {{-- Kanan: Detail Data & Biografi --}}
                <div class="flex-grow pt-4">
                    <h1 class="text-5xl md:text-6xl font-black text-white tracking-tight mb-6">{{ $actorsData->name }}</h1>
                    
                    {{-- Data Pills (Metadata) --}}
                    <div class="flex flex-wrap gap-3 mb-8">
                        <span class="px-4 py-1.5 bg-[#1f2833] border border-white/10 text-[#66fcf1] text-sm font-semibold rounded-full shadow-sm">
                            Actor / Actress
                        </span>
                        {{-- Fallback Data jika belum ada di database --}}
                        <span class="px-4 py-1.5 bg-[#1f2833] border border-white/10 text-gray-300 text-sm font-semibold rounded-full shadow-sm">
                            Popularity: {{ number_format($actorsData->popularity ?? 85.5, 1) }}
                        </span>
                        @if($actorsData->birthday ?? false)
                        <span class="px-4 py-1.5 bg-[#1f2833] border border-white/10 text-gray-300 text-sm font-semibold rounded-full shadow-sm">
                            Born: {{ date('Y', strtotime($actorsData->birthday)) }}
                        </span>
                        @endif
                    </div>

                    {{-- Biografi --}}
                    <div class="mb-4">
                        <h3 class="text-[#45a29e] text-lg font-bold mb-3 italic">Biography</h3>
                        <p class="text-gray-300 leading-relaxed text-justify opacity-90 max-w-4xl">
                            {{ $actorsData->biography ?? 'Biografi terperinci untuk ' . $actorsData->name . ' belum tersedia di pangkalan data saat ini. Namun, ia dikenal atas berbagai perannya dalam industri perfilman internasional yang telah memenangkan hati banyak penonton.' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- 2. GRID FILM (MOVIES STARRED) --}}
            <div class="mb-20">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-[#66fcf1] rounded-full"></span>
                        Movies Starred By {{ $actorsData->name }}
                    </h2>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
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

            {{-- 3. GRID SIMILAR ACTORS (BERDASARKAN IRISAN GENRE) --}}
            <div class="pt-8 border-t border-white/10">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-[#45a29e] rounded-full"></span>
                        Similar Actors
                    </h2>
                    <p class="text-gray-400 text-sm mt-1 ml-4">Actors who frequently play in the same genres.</p>
                </div>

                <div class="flex gap-6 overflow-x-auto pb-6 scrollbar-hide">
                    @forelse($similarActors as $similar)
                        <a href="{{ route('actor.detail', $similar->tmdb_actor_id) }}" class="group flex flex-col items-center w-36 flex-shrink-0">
                            <div class="w-32 h-32 rounded-full overflow-hidden mb-3 border-2 border-transparent group-hover:border-[#66fcf1] transition-all duration-300 shadow-lg">
                                <img src="{{ $similar->image_url }}" alt="{{ $similar->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </div>
                            <h3 class="text-white text-sm font-semibold text-center group-hover:text-[#66fcf1] transition-colors line-clamp-2">{{ $similar->name }}</h3>
                        </a>
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