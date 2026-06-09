@props(['genres' => [], 'languages' => []])

<div
    x-data="discoverModal()"
    x-on:open-discover.window="open()"
    x-on:keydown.escape.window="close()"
>
    {{-- Backdrop blur --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40"
        @click="close()"
        x-cloak
    ></div>

    {{-- Modal --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-cloak
    >
        <div class="bg-[#494949] rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-[#3f3f3f]">
                <div>
                    <h2 class="text-lg font-semibold text-white tracking-wide">Discover</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Find films by genre & language</p>
                </div>
                <button @click="close()"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Form Filter --}}
            <div class="p-6 space-y-5">

                {{-- Genre --}}
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Genre
                        <span x-show="selectedGenres.length >= 4" class="text-xs text-red-400 ml-2" x-cloak>
                        Max 4 genre
                    </span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($genres as $genre)
                            <button
                                type="button"
                                @click="toggleGenre({{ $genre['map_id'] }})"
                                :class="selectedGenres.includes({{ $genre['map_id'] }})
                                    ? 'bg-indigo-600 text-white border-indigo-600'
                                    : (selectedGenres.length >= 4
                                        ? 'bg-[#333333] text-[#5a5a5a] border-[#333333] cursor-not-allowed'
                                        : 'bg-[#5a5a5a] text-[#d4d4d8] border-[#3f3f3f] hover:border-indigo-400 hover:text-white')"
                                class="px-3 py-1 rounded-full text-sm border transition">
                                {{ $genre['name'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Language --}}
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Bahasa
                        <span x-show="selectedLanguages.length >= 1" class="text-xs text-red-400 ml-2" x-cloak>
                        Max 1 bahasa
                    </span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($languages as $lang)
                            <button
                                type="button"
                                @click="toggleLanguage('{{ $lang['code'] }}')"
                                :class="selectedLanguages.includes('{{ $lang['code'] }}')
                                    ? 'bg-indigo-600 text-white border-indigo-600'
                                    : (selectedLanguages.length >= 1
                                        ? 'bg-[#333333] text-[#5a5a5a] border-[#333333] cursor-not-allowed'
                                        : 'bg-[#5a5a5a] text-[#d4d4d8] border-[#3f3f3f] hover:border-indigo-400 hover:text-white')"
                                class="px-3 py-1 rounded-full text-sm border transition">
                                {{ $lang['name'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        type="button"
                        @click="selectedGenres = []; selectedLanguages = []"
                        class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">
                        Reset
                    </button>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button
                            type="button"
                            @click="selectedGenres = []; selectedLanguages = []"
                            class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">
                            Reset
                        </button>
                        <button
                            type="button"
                            @click="submitFilter()"
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                            Search
                        </button>
                    </div>
                </div>

            </div>

            {{-- Empty state --}}
            <div x-show="searched && results.length === 0" class="px-6 pb-6 text-center text-gray-400" x-cloak>
                Tidak ada film ditemukan 😔
            </div>

        </div>
    </div>
</div>

<script>
function discoverModal() {
    return {
        isOpen: false,
        loading: false,
        searched: false,
        selectedGenres: [],
        selectedLanguages: [],
        results: [],

        open() {
            this.isOpen = true;
            document.body.classList.add('overflow-hidden');
        },

        close() {
            this.isOpen = false;
            document.body.classList.remove('overflow-hidden');
        },

        toggleGenre(id) {
            if (this.selectedGenres.includes(id)) {
                this.selectedGenres = this.selectedGenres.filter(g => g !== id);
            } else {
                if (this.selectedGenres.length >= 4) return;
                this.selectedGenres.push(id);
            }
        },

        toggleLanguage(code) {
            if (this.selectedLanguages.includes(code)) {
                this.selectedLanguages = this.selectedLanguages.filter(l => l !== code);
            } else {
                if (this.selectedLanguages.length >= 1) return;
                this.selectedLanguages.push(code);
            }
        },

        submitFilter() {
            const params = new URLSearchParams();

            this.selectedGenres.forEach(g => params.append('genres[]', g));
            this.selectedLanguages.forEach(l => params.append('languages[]', l));

            window.location.href = `/discover/results?${params.toString()}`;
        },

        async fetchResults() {
            this.loading = true;
            this.searched = false;

            const params = new URLSearchParams({
                genres: this.selectedGenres,
                languages: this.selectedLanguages,
            }).toString();

            const response = await fetch(`/discover/filter?${params}`, {
                headers: { 'Accept': 'application/json' }
            });

            this.results = await response.json();
            this.loading = false;
            this.searched = true;
        }
    }
}
</script>