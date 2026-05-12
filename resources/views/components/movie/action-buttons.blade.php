<div class="flex flex-col sm:flex-row gap-3 md:gap-4 pt-6">

    <!-- Watchlist -->
    <button class="group relative px-6 md:px-8 py-3 md:py-4 rounded-lg font-semibold text-base
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

            Watchlist

        </span>

        <div class="absolute inset-0 bg-gradient-to-r from-cyan-400/0 via-white/10 to-cyan-400/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

    </button>

    <!-- Favorite -->
    <button class="group relative px-6 md:px-8 py-3 md:py-4 rounded-lg font-semibold text-base
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

        <div class="absolute inset-0 bg-gradient-to-r from-purple-400/0 via-white/10 to-purple-400/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

    </button>

    <!-- Share -->
    <button onclick="copyShareLink()"
        class="group relative w-14 h-14 rounded-2xl
        bg-white/5 hover:bg-white/10
        border border-white/10 hover:border-cyan-400/40
        flex items-center justify-center
        transition duration-300
        hover:scale-105
        hover:shadow-[0_0_25px_rgba(34,211,238,0.25)]">

        <svg xmlns="http://www.w3.org/2000/svg"
            class="w-6 h-6 text-white group-hover:text-cyan-300 transition"
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

    </button>

</div>