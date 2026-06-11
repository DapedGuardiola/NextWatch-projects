<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personalisasi — NextWatch</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        [x-cloak] { display: none !important; }
        html, body { height: 100%; overflow: hidden; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        ::-webkit-scrollbar { width: 3px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 99px; }

        .progress-fill {
            height: 100%;
            background: #fff;
            transition: width 0.6s cubic-bezier(0.65, 0, 0.35, 1);
        }

        .step-section {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.4s ease, transform 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .step-section.hidden {
            opacity: 0;
            transform: translateX(40px);
            pointer-events: none;
        }
        .step-section.prev {
            opacity: 0;
            transform: translateX(-40px);
            pointer-events: none;
        }

        .step-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            transition: all 0.4s ease;
        }
        .step-dot.active {
            width: 24px;
            border-radius: 99px;
            background: #fff;
        }

        .genre-pill {
            padding: 8px 14px;
            border-radius: 99px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.04);
            color: rgba(255,255,255,0.55);
            font-size: 10px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
            white-space: nowrap;
        }
        @media (min-width: 640px) {
            .genre-pill {
                padding: 9px 18px;
                font-size: 13px;
            }
        }
        .genre-pill.disabled {
            opacity: 0.2;
            cursor: not-allowed;
            pointer-events: none;
        }
        .genre-pill:hover {
            background: rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.85);
            border-color: rgba(255,255,255,0.2);
        }
        .genre-pill.selected {
            background: #fff;
            color: #070709;
            border-color: #fff;
            font-weight: 600;
        }

        .movie-chip {
            position: relative;
            cursor: pointer;
            transition: transform 0.2s ease;
            animation: chipIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .movie-chip:hover { transform: translateY(-2px); }
        @keyframes chipIn {
            from { transform: scale(0.85); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .movie-chip-remove {
            position: absolute;
            top: -5px; right: -5px;
            width: 18px; height: 18px;
            background: #ff3b30;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 9px;
            font-weight: 700;
            color: #fff;
            opacity: 0;
            transition: opacity 0.15s ease;
            z-index: 10;
            box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        }
        .movie-chip:hover .movie-chip-remove { opacity: 1; }
        /* Always show remove on touch devices */
        @media (hover: none) {
            .movie-chip-remove { opacity: 1; }
        }

        .search-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0; right: 0;
            background: #111115;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            overflow: hidden;
            z-index: 50;
            max-height: 220px;
            overflow-y: auto;
        }
        .search-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .search-item:hover, .search-item:active { background: rgba(255,255,255,0.05); }
    </style>
</head>
<body class="bg-[#070709] text-white h-screen overflow-hidden">

<div x-data="onboarding()" class="relative h-full overflow-hidden">

    {{-- PROGRESS LINE --}}
    <div class="fixed top-0 left-0 right-0 h-px bg-white/[0.06] z-50">
        <div class="progress-fill" :style="'width:' + ((step / 3) * 100) + '%'"></div>
    </div>

    {{-- AMBIENT BLOBS --}}
    <div class="absolute -top-48 -left-24 w-[500px] h-[500px] sm:w-[600px] sm:h-[600px] rounded-full pointer-events-none"
         style="background:rgba(88,80,236,0.08);filter:blur(120px)"></div>
    <div class="absolute -bottom-36 -right-24 w-[400px] h-[400px] sm:w-[500px] sm:h-[500px] rounded-full pointer-events-none"
         style="background:rgba(236,80,120,0.06);filter:blur(120px)"></div>

    {{-- STEP DOTS --}}
    <div class="fixed bottom-6 sm:bottom-9 left-1/2 -translate-x-1/2 flex gap-2 z-50">
        <template x-for="i in 3" :key="i">
            <div class="step-dot" :class="step >= i ? 'active' : ''"></div>
        </template>
    </div>

    <form method="POST" action="{{ route('personalization.store') }}" class="h-full">
        @csrf

        {{-- ========== STEP 1: INTRO ========== --}}
        <div class="step-section px-6 pt-16 pb-16 sm:px-12 sm:pt-20 sm:pb-12"
             :class="step !== 1 ? (step > 1 ? 'prev' : 'hidden') : ''">
            <div class="w-full max-w-[480px] text-center">

                {{-- Logo mark --}}
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-[14px] bg-white/[0.07] border border-white/10
                            flex items-center justify-center mx-auto mb-7 sm:mb-8">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="rgba(255,255,255,0.7)" stroke-width="1.5" class="sm:w-[22px] sm:h-[22px]">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>

                <h1 class="text-[1.75rem] sm:text-[2.5rem] md:text-[2.8rem] font-extrabold leading-[1.1] tracking-[-0.03em] mb-4">
                    Temukan film yang
                    <span class="text-white/35">benar-benar untukmu</span>
                </h1>

                <p class="text-[13px] sm:text-sm text-white/40 leading-relaxed max-w-[320px] sm:max-w-[380px] mx-auto mb-7 sm:mb-8">
                    Data yang anda berikan akan membantu sistem kami menemukan rekomendasi yang tepat untuk anda.
                </p>

                {{-- Feature list --}}
                <div class="flex flex-col gap-2 max-w-[260px] sm:max-w-[280px] mx-auto mb-8 text-left">
                    @foreach(['Pilih genre favoritmu', 'Pilih film yang kamu suka', 'Rekomendasi langsung aktif'] as $idx => $f)
                    <div class="flex items-center gap-[10px] text-[13px] text-white/40">
                        <div class="w-[5px] h-[5px] rounded-full bg-white/25 flex-shrink-0"></div>
                        <span>{{ $f }}</span>
                        <span class="ml-auto text-[11px] text-white/20">{{ $idx + 1 }}/3</span>
                    </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-center gap-3">
                    <button type="button"
                            class="px-8 py-3.5 sm:px-9 bg-white text-[#070709] rounded-full text-[14px] sm:text-[15px]
                                   font-bold cursor-pointer transition-all hover:bg-white/90 active:scale-95"
                            @click="step = 2">
                        Mulai →
                    </button>
                    <span class="text-[11px] text-white/20 hidden sm:inline">kurang dari 1 menit</span>
                </div>
                <p class="mt-3 text-[11px] text-white/20 sm:hidden">kurang dari 1 menit</p>

            </div>
        </div>

        {{-- ========== STEP 2: GENRE ========== --}}
        <div class="step-section px-5 pt-16 pb-20 sm:px-12 sm:pt-20 sm:pb-12"
             :class="step !== 2 ? (step > 2 ? 'prev' : 'hidden') : ''">
            <div class="w-full max-w-[680px] text-center flex flex-col h-full justify-center">

                <p class="text-[10px] sm:text-[11px] tracking-[0.12em] uppercase text-white/25 mb-3 sm:mb-4">
                    Langkah 1 dari 2
                </p>

                <h2 class="text-[2rem] sm:text-[2.5rem] md:text-[3rem] font-extrabold tracking-[-0.03em] mb-2">
                    Genre favorit
                </h2>
                <p class="text-[13px] sm:text-sm text-white/35 mb-6 sm:mb-8">
                    Pilih 4 genre favoritmu
                    <span x-show="selectedGenres.length > 0"
                          x-text="' · ' + selectedGenres.length + '/4'"
                          class="text-white/60" x-cloak></span>
                </p>

                {{-- Genre pills --}}
                <div class="flex flex-wrap justify-center gap-2 mb-8 sm:mb-10
                            max-h-[calc(100vh-340px)] sm:max-h-none overflow-y-auto px-1">
                    @foreach($genres as $genre)
                    <button type="button"
                        class="genre-pill"
                        :class="selectedGenres.includes({{ $genre->id }})
                            ? 'selected'
                            : (selectedGenres.length >= 4 ? 'disabled' : '')"
                        @click="toggleGenre({{ $genre->id }})">
                        {{ $genre->name }}
                    </button>
                    <input type="hidden" name="genre_ids[]" :value="{{ $genre->id }}"
                        x-bind:disabled="!selectedGenres.includes({{ $genre->id }})">
                    @endforeach
                </div>

                <div class="flex items-center justify-center gap-2.5">
                    <button type="button"
                            class="px-5 py-3.5 bg-transparent text-white/35 border border-white/10 rounded-full
                                   text-[13px] sm:text-sm font-medium transition-all hover:text-white/70
                                   hover:border-white/20 active:scale-95"
                            @click="step = 1">← Kembali</button>
                    <button type="button"
                            class="px-7 py-3.5 sm:px-9 bg-white text-[#070709] rounded-full text-[14px] sm:text-[15px]
                                   font-bold cursor-pointer transition-all hover:bg-white/90 active:scale-95
                                   disabled:bg-white/15 disabled:text-white/30 disabled:cursor-not-allowed
                                   disabled:transform-none"
                            :disabled="selectedGenres.length !== 4"
                            @click="nextToMovies()">
                        Lanjut →
                    </button>
                </div>

            </div>
        </div>

        {{-- ========== STEP 3: FILM ========== --}}
        <div class="step-section px-5 pt-16 pb-20 sm:px-12 sm:pt-20 sm:pb-12"
             :class="step !== 3 ? (step > 3 ? 'prev' : 'hidden') : ''">
            <div class="w-full max-w-[640px] text-center">

                <p class="text-[10px] sm:text-[11px] tracking-[0.12em] uppercase text-white/25 mb-3 sm:mb-4">
                    Langkah 2 dari 2
                </p>

                <h2 class="text-[2rem] sm:text-[2.5rem] md:text-[3rem] font-extrabold tracking-[-0.03em] mb-2">
                    Film favorit
                </h2>
                <p class="text-[13px] sm:text-sm text-white/35 mb-5 sm:mb-6">
                    Pilih 3 film favoritmu
                    <span x-show="selectedMovies.length > 0"
                          x-text="' · ' + selectedMovies.length + '/3'"
                          class="text-white/60" x-cloak></span>
                </p>

                {{-- Search --}}
                <div class="relative w-full max-w-[440px] mx-auto mb-5 sm:mb-6">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-white/25 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input type="text"
                        class="w-full bg-white/[0.05] border border-white/10 rounded-2xl
                               py-3.5 pl-11 pr-4 text-[13px] sm:text-sm text-white
                               placeholder:text-white/25 outline-none
                               focus:border-white/25 focus:bg-white/[0.07]
                               disabled:opacity-30 disabled:cursor-not-allowed
                               transition-all duration-200"
                        x-model="search"
                        @input.debounce.250ms="fetchSearch()"
                        @keydown.escape="searchResults = []"
                        :disabled="selectedMovies.length >= 3"
                        :placeholder="selectedMovies.length >= 3 ? 'Sudah memilih 3 film ✓' : 'Cari film...'">

                    {{-- Dropdown --}}
                    <div class="search-dropdown" x-show="search.length > 1 && searchResults.length > 0"
                         @click.outside="searchResults = []" x-cloak>
                        <template x-for="movie in searchResults" :key="movie.id">
                            <div class="search-item" @mousedown.prevent @click.stop="selectMovie(movie)">
                                <img :src="movie.poster_url" :alt="movie.title"
                                     class="w-8 h-12 object-cover rounded-[6px] flex-shrink-0">
                                <div class="text-left min-w-0 flex-1">
                                    <p class="text-[13px] font-medium text-white truncate" x-text="movie.title"></p>
                                    <p class="text-[11px] text-white/30 mt-0.5">⭐ <span x-text="movie.rating"></span></p>
                                </div>
                                <span class="text-[11px] text-white/25 flex-shrink-0">Pilih</span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Selected movies --}}
                <div class="flex flex-wrap justify-center gap-2.5 mb-6 sm:mb-7
                            min-h-[104px] max-h-[120px] sm:max-h-[140px] overflow-y-auto px-2 py-1">

                    <template x-for="movie in selectedMoviesData" :key="movie.id">
                        <div class="movie-chip" @click="removeMovie(movie.id)">
                            <img :src="movie.poster_url" :alt="movie.title"
                                 class="w-14 h-20 sm:w-16 sm:h-24 object-cover rounded-[10px] block
                                        border border-white/[0.08]">
                            <div class="movie-chip-remove">✕</div>
                            <input type="hidden" name="movie_ids[]" :value="movie.id">
                        </div>
                    </template>

                    {{-- Empty state --}}
                    <div x-show="selectedMoviesData.length === 0" x-cloak
                         class="w-full flex items-center justify-center h-24 gap-2
                                border border-dashed border-white/[0.08] rounded-2xl">
                        <svg class="w-4 h-4 text-white/15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="text-[12px] sm:text-[13px] text-white/20">Film pilihanmu muncul di sini</span>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-2.5">
                    <button type="button"
                            class="px-5 py-3.5 bg-transparent text-white/35 border border-white/10 rounded-full
                                   text-[13px] sm:text-sm font-medium transition-all hover:text-white/70
                                   hover:border-white/20 active:scale-95"
                            @click="step = 2">← Kembali</button>
                    <button type="submit"
                            class="px-7 py-3.5 sm:px-9 bg-white text-[#070709] rounded-full text-[14px] sm:text-[15px]
                                   font-bold cursor-pointer transition-all hover:bg-white/90 active:scale-95
                                   disabled:bg-white/15 disabled:text-white/30 disabled:cursor-not-allowed
                                   disabled:transform-none"
                            :disabled="selectedMovies.length !== 3">Selesai</button>
                </div>

            </div>
        </div>

    </form>
</div>

<script>
function onboarding() {
    return {
        step: 1,
        search: '',
        searchResults: [],
        selectedGenres: [],
        selectedMovies: [],
        selectedMoviesData: [],

        async fetchSearch() {
            if (this.search.length < 2) { this.searchResults = []; return; }
            try {
                const res = await fetch(`/search/live?q=${encodeURIComponent(this.search)}`);
                const json = await res.json();
                this.searchResults = (json.movies || []).slice(0, 6);
            } catch(e) { this.searchResults = []; }
        },

        selectMovie(movie) {
            const id = String(movie.id);
            if (this.selectedMovies.includes(id)) return;
            if (this.selectedMovies.length >= 3) return;
            this.selectedMovies.push(id);
            this.selectedMoviesData.push({
                id: id,
                title: movie.title,
                poster_url: movie.poster_url,
                rating: movie.rating,
            });
            this.search = '';
            this.searchResults = [];
        },

        removeMovie(id) {
            const s = String(id);
            this.selectedMovies = this.selectedMovies.filter(m => m !== s);
            this.selectedMoviesData = this.selectedMoviesData.filter(m => m.id !== s);
        },

        isMovieSelected(id) {
            return this.selectedMovies.includes(String(id));
        },

        toggleGenre(id) {
            const i = this.selectedGenres.indexOf(id);
            if (i === -1) {
                if (this.selectedGenres.length >= 4) return;
                this.selectedGenres.push(id);
            } else {
                this.selectedGenres.splice(i, 1);
            }
        },

        nextToMovies() {
            if (this.selectedGenres.length === 4) this.step = 3;
        }
    }
}
</script>
</body>
</html>