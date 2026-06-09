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
        html, body { height: 100%; overflow: hidden; background: #070709; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #fff;
        }

        .font-syne { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 3px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 99px; }

        /* Progress line */
        .progress-line {
            height: 1px;
            background: rgba(255,255,255,0.06);
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
        }
        .progress-fill {
            height: 100%;
            background: #fff;
            transition: width 0.6s cubic-bezier(0.65, 0, 0.35, 1);
        }

        /* Ambient bg blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
        }

        /* Step section */
        .step-section {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 80px 48px 48px;
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

        /* Step indicator */
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

        /* Genre pill */
        .genre-pill {
            padding: 10px 20px;
            border-radius: 99px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.04);
            color: rgba(255,255,255,0.55);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
            white-space: nowrap;
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

        /* Movie card */
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
        .movie-chip-img {
            width: 64px;
            height: 96px;
            object-fit: cover;
            border-radius: 10px;
            display: block;
            border: 1px solid rgba(255,255,255,0.08);
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

        /* Search dropdown */
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
        .search-item:hover { background: rgba(255,255,255,0.05); }
        .search-item img {
            width: 32px; height: 48px;
            object-fit: cover;
            border-radius: 6px;
            flex-shrink: 0;
        }

        /* Buttons */
        .btn-primary {
            padding: 14px 36px;
            background: #fff;
            color: #070709;
            border: none;
            border-radius: 99px;
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            letter-spacing: 0.01em;
        }
        .btn-primary:hover {
            background: rgba(255,255,255,0.9);
            transform: translateY(-1px);
        }
        .btn-primary:disabled {
            background: rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.3);
            cursor: not-allowed;
            transform: none;
        }
        .btn-ghost {
            padding: 14px 24px;
            background: transparent;
            color: rgba(255,255,255,0.35);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 99px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-ghost:hover {
            color: rgba(255,255,255,0.7);
            border-color: rgba(255,255,255,0.2);
        }

        /* Search input */
        .search-input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            padding: 14px 16px 14px 44px;
            font-size: 14px;
            color: #fff;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s ease, background 0.2s ease;
            outline: none;
        }
        .search-input::placeholder { color: rgba(255,255,255,0.25); }
        .search-input:focus {
            border-color: rgba(255,255,255,0.25);
            background: rgba(255,255,255,0.07);
        }

        /* Divider line in step 1 */
        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: rgba(255,255,255,0.4);
        }
        .feature-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            flex-shrink: 0;
        }
    </style>
</head>
<body>

<div x-data="onboarding()" class="relative h-screen overflow-hidden">

    {{-- PROGRESS --}}
    <div class="progress-line">
        <div class="progress-fill" :style="'width:' + ((step / 3) * 100) + '%'"></div>
    </div>

    {{-- AMBIENT BACKGROUND --}}
    <div class="blob" style="width:600px;height:600px;top:-200px;left:-100px;background:rgba(88,80,236,0.08)"></div>
    <div class="blob" style="width:500px;height:500px;bottom:-150px;right:-100px;background:rgba(236,80,120,0.06)"></div>
    <div class="blob" style="width:400px;height:400px;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(255,255,255,0.02)"></div>

    {{-- STEP DOTS --}}
    <div style="position:fixed;bottom:36px;left:50%;transform:translateX(-50%);display:flex;gap:8px;z-index:100;">
        <template x-for="i in 3" :key="i">
            <div class="step-dot" :class="step >= i ? 'active' : ''"></div>
        </template>
    </div>

    <form method="POST" action="{{ route('personalization.store') }}" style="height:100%">
        @csrf

        {{-- ========== STEP 1: INTRO ========== --}}
        <div class="step-section" :class="step !== 1 ? (step > 1 ? 'prev' : 'hidden') : ''">
            <div style="max-width:560px;width:100%;text-align:center;">

                {{-- Logo mark --}}
                <div style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 32px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="1.5">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>

                <h1 class="font-syne" style="font-size:clamp(1.8rem,3.5vw,2.8rem);font-weight:800;line-height:1.1;letter-spacing:-0.03em;margin-bottom:16px;">
                    Temukan film yang
                    <span style="color:rgba(255,255,255,0.35)">benar-benar untukmu</span>
                </h1>

                <p style="font-size:14px;color:rgba(255,255,255,0.4);line-height:1.7;max-width:380px;margin:0 auto 28px;">
                    Data yang anda berikan akan membantu sistem kami menemukan rekomendasi yang tepat untuk anda.
                </p>

                {{-- Features --}}
                <div style="display:flex;flex-direction:column;gap:8px;max-width:280px;margin:0 auto 32px;text-align:left;">
                    @foreach(['Pilih genre favoritmu', 'Pilih film yang kamu suka', 'Rekomendasi langsung aktif'] as $idx => $f)
                    <div class="feature-item">
                        <div class="feature-dot"></div>
                        <span>{{ $f }}</span>
                        <span style="margin-left:auto;font-size:11px;color:rgba(255,255,255,0.2)">{{ $idx + 1 }}/3</span>
                    </div>
                    @endforeach
                </div>

                <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                    <button type="button" class="btn-primary" @click="step = 2">
                        Mulai →
                    </button>
                    <span style="font-size:12px;color:rgba(255,255,255,0.2)">kurang dari 1 menit</span>
                </div>

            </div>
        </div>

        {{-- ========== STEP 2: GENRE ========== --}}
        <div class="step-section" :class="step !== 2 ? (step > 2 ? 'prev' : 'hidden') : ''">
            <div style="max-width:720px;width:100%;text-align:center;">

                <p style="font-size:11px;letter-spacing:0.12em;text-transform:uppercase;color:rgba(255,255,255,0.25);margin-bottom:16px;">Langkah 1 dari 2</p>

                <h2 class="font-syne" style="font-size:clamp(2rem,4vw,3rem);font-weight:800;letter-spacing:-0.03em;margin-bottom:8px;">
                    Genre favorit
                </h2>
                <p style="font-size:14px;color:rgba(255,255,255,0.35);margin-bottom:32px;">
                    Pilih 4 genre favoritmu
                    <span x-show="selectedGenres.length > 0" x-text="' · ' + selectedGenres.length + '/4'" style="color:rgba(255,255,255,0.6)" x-cloak></span>
                </p>

                {{-- Genre pills --}}
                <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:8px;margin-bottom:40px;">
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

                <div style="display:flex;align-items:center;justify-content:center;gap:10px;">
                    <button type="button" class="btn-ghost" @click="step = 1">← Kembali</button>
                    <button type="button" class="btn-primary"
                        :disabled="selectedGenres.length !== 4"
                        @click="nextToMovies()">
                        Lanjut →
                    </button>
                </div>

            </div>
        </div>

        {{-- ========== STEP 3: FILM ========== --}}
        <div class="step-section" :class="step !== 3 ? (step > 3 ? 'prev' : 'hidden') : ''">
            <div style="max-width:680px;width:100%;text-align:center;">

                <p style="font-size:11px;letter-spacing:0.12em;text-transform:uppercase;color:rgba(255,255,255,0.25);margin-bottom:16px;">Langkah 2 dari 2</p>

                <h2 class="font-syne" style="font-size:clamp(2rem,4vw,3rem);font-weight:800;letter-spacing:-0.03em;margin-bottom:8px;">
                    Film favorit
                </h2>
                <p style="font-size:14px;color:rgba(255,255,255,0.35);margin-bottom:24px;">
                    Pilih 3 film favoritmu
                    <span x-show="selectedMovies.length > 0" x-text="' · ' + selectedMovies.length + '/3'" style="color:rgba(255,255,255,0.6)" x-cloak></span>
                </p>

                {{-- Search --}}
                <div style="position:relative;width:100%;max-width:440px;margin:0 auto 24px;">
                    <svg style="position:absolute;left:16px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:rgba(255,255,255,0.25);pointer-events:none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input type="text" class="search-input"
                        x-model="search"
                        @input.debounce.250ms="fetchSearch()"
                        @keydown.escape="searchResults = []"
                        :disabled="selectedMovies.length >= 3"
                        :placeholder="selectedMovies.length >= 3 ? 'Sudah memilih 3 film ✓' : 'Cari film...'"
                        :class="selectedMovies.length >= 3 ? 'opacity-30 cursor-not-allowed' : ''">

                    {{-- Dropdown --}}
                    <div class="search-dropdown" x-show="search.length > 1 && searchResults.length > 0" @click.outside="searchResults = []" x-cloak>
                        <template x-for="movie in searchResults" :key="movie.id">
                            <div class="search-item" @mousedown.prevent @click.stop="selectMovie(movie)">
                                <img :src="movie.poster_url" :alt="movie.title">
                                <div style="text-align:left;min-width:0;flex:1">
                                    <p style="font-size:13px;font-weight:500;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" x-text="movie.title"></p>
                                    <p style="font-size:11px;color:rgba(255,255,255,0.3);margin-top:2px">⭐ <span x-text="movie.rating"></span></p>
                                </div>
                                <span style="font-size:11px;color:rgba(255,255,255,0.25);flex-shrink:0">Pilih</span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Selected movies - rendered dari Alpine state --}}
                <div style="min-height:112px;display:flex;flex-wrap:wrap;justify-content:center;gap:10px;margin-bottom:28px;max-height:140px;overflow-y:auto;padding:8px 8px 4px;">

                    <template x-for="movie in selectedMoviesData" :key="movie.id">
                        <div class="movie-chip" @click="removeMovie(movie.id)">
                            <img class="movie-chip-img" :src="movie.poster_url" :alt="movie.title">
                            <div class="movie-chip-remove">✕</div>
                            <input type="hidden" name="movie_ids[]" :value="movie.id">
                        </div>
                    </template>

                    {{-- Empty state --}}
                    <div x-show="selectedMoviesData.length === 0" x-cloak
                        style="width:100%;display:flex;align-items:center;justify-content:center;height:96px;border:1px dashed rgba(255,255,255,0.08);border-radius:16px;gap:8px;">
                        <svg style="width:16px;height:16px;color:rgba(255,255,255,0.15)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span style="font-size:13px;color:rgba(255,255,255,0.2)">Film yang kamu pilih akan muncul di sini</span>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:center;gap:10px;">
                    <button type="button" class="btn-ghost" @click="step = 2">← Kembali</button>
                    <button type="submit" class="btn-primary" :disabled="selectedMovies.length !== 3">Selesai</button>
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
        selectedMovies: [],       // array of ids (string)
        selectedMoviesData: [],   // array of {id, title, poster_url, rating}

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
            if (this.selectedMovies.length >= 3) return; // max 3
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
                if (this.selectedGenres.length >= 4) return; // max 4
                this.selectedGenres.push(id);
            } else {
                this.selectedGenres.splice(i, 1);
            }
        },

        toggleMovie(id) {
            const s = String(id);
            const i = this.selectedMovies.indexOf(s);
            i === -1 ? this.selectedMovies.push(s) : this.selectedMovies.splice(i, 1);
        },

        nextToMovies() {
            if (this.selectedGenres.length === 4) this.step = 3;
        }
    }
}
</script>
</body>
</html>