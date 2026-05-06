<x-app-layout>
    <x-slot name="title">
        {{ __('Dashboard') }}
    </x-slot>
    <div class="h-screen relative">
        <img src="{{ asset('images/extraction.jpg') }}"
            class="w-full h-full object-cover" alt="hero">

        <!-- Konten di atas gambar -->
        <div class="absolute bottom-10 left-10">
            <h1 class="text-white text-5xl font-bold">Extraction</h1>
        </div>
    </div>
    <div>
    <div class="mx-10 my-5">
        <h1>
            <p class="text-2xl font-bold">Top On Its Genre</p>
        </h1>
    </div>

    <div class="flex gap-4 px-10 w-[90%] mx-auto overflow-x-auto scrollbar-hide">
    @foreach($movies as $movie)
    <x-movie.movie-modal>
        <x-slot name="poster">
        {{ $movie['poster_path'] }}
        </x-slot>
        <x-slot name="title">
        {{ $movie['title'] }}
        </x-slot>
    </x-movie.movie-modal>
    @endforeach
    </div>
    </div>
    

    <dv class="h-100">
        <p>a</p>
    </dv>


</x-app-layout>