<x-app-layout>
    <div class="min-h-screen relative bg-[#212121] pb-10">
        {{-- Backdrop Koleksi sebagai Background (Ditambahkan opacity agar tulisan & poster film lebih kontras) --}}
        <img src="{{ $collectionData->backdrop_url }}" alt="{{ $collectionData->name }}" class="absolute inset-0 object-cover object-center w-full h-full overflow-hidden opacity-30 pointer-events-none">
        
        {{-- Overlay Gradient & Wadah Konten yang Mendukung Fitur Scroll Halaman --}}
        <div class="relative min-h-screen bg-gradient-to-b from-transparent via-[#212121]/40 via-25% via-[#212121]/80 via-50% to-[#212121] to-100% pt-12">
            
            {{-- Bagian Teks Informasi Koleksi (Sesuai Jobdesk Menampilkan Overview) --}}
            <div class="ml-20 max-w-[80%] mb-4">
                <div class="font-bold text-4xl text-white tracking-tight">{{ $collectionData->name }}</div>
                
                {{-- Menampilkan Teks Deskripsi Ringkas / Overview Koleksi Film --}}
                @if($collectionData->overview)
                    <p class="text-gray-300 mt-4 text-sm leading-relaxed max-w-3xl bg-black/40 border border-white/5 p-5 rounded-2xl backdrop-blur-md">
                        {{ $collectionData->overview }}
                    </p>
                @endif
                
                <div class="font-bold text-xl text-white mt-10">Movies in {{ $collectionData->name }} Collection</div>
            </div>

            {{-- Grid Tampilan Daftar Film Waralaba --}}
            <div class="grid grid-cols-5 max-w-[90%] place-items-center gap-8 mt-6 mx-auto mb-10">
                @foreach($collectionData->movies as $movie)
                <x-movie.movie-modal
                    :poster="$movie->poster_url"
                    :title="$movie->title"
                    :tmdb_movie_id="$movie->tmdb_movie_id"
                    :year="$movie->release_date ? date('Y', strtotime($movie->release_date)) : null"
                    :rating="$movie->rating ?? null"
                    :overview="$movie->overview ?? null"
                    :genres="$movie->genres->pluck('genre.name')->filter()->toArray() ?? []"
                    :duration="$movie->runtime ?? null" />
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>