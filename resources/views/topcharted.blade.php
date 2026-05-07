<x-app-layout>
    <x-slot name="title">
        {{ __('Top Charted Movies') }}
    </x-slot>
    <div>
        <div class="mx-10 my-5">
            <h1>
                <p class="text-2xl font-bold">Top On Its Genre</p>
            </h1>
        </div>

        <div class="flex gap-4 px-10 w-[90%] mx-auto overflow-x-auto scrollbar-hide">
        @foreach($movies as $index => $movie)
            <x-movie.topmovies-modal>
                <x-slot name="poster">
                    {{ $movie['poster_path'] }}
                </x-slot>
                <x-slot name="title">
                    {{ $movie['title'] }}
                </x-slot>
                <x-slot name="rank">
                    {{ $index + 1 }}
                </x-slot>
            </x-movie.topmovies-modal>
        @endforeach
        </div>
    </div>

</x-app-layout>