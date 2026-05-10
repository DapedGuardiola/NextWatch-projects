<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\MovieGenre;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    public function getMovie()
    {
        $movies = Movie::select([
            'tmdb_movie_id',
            'title',
            DB::raw('YEAR(release_date)as year'),
            'rating',
            'overview',
            'runtime',
            'poster_path',
        ])->with('genres.genre:map_id,name')
            ->orderBy('rating', 'Desc')
            ->limit(10)
            ->get();

        log::info('Data Movie berhasil diambil', ['movies' => $movies]);


        return $movies;
    }
    // ->map(function ($movie) {
            // $movie->poster_path = 'https://image.tmdb.org/t/p/w500/' . $movie->poster_path;
            // return $movie;
    public function getPopularMovie()
    {
        $popular = Movie::orderBy('popularity', 'desc')->first();
        return $popular;
    }

    // $movies = collect([
    //     ['title' => 'Extraction',   'poster_path' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=500'],
    //     ['title' => 'The Dark Knight', 'poster_path' => 'https://images.unsplash.com/photo-1531259683007-016a7b628fc3?w=500'],
    //     ['title' => 'Interstellar', 'poster_path' => 'https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?w=500'],
    //     ['title' => 'John Wick',    'poster_path' => 'https://images.unsplash.com/photo-1509347528160-9a9e33742cdb?w=500'],
    //     ['title' => 'Avengers',     'poster_path' => 'https://images.unsplash.com/photo-1635805737707-575885ab0820?w=500'],
    //     ['title' => 'Inception',    'poster_path' => 'https://images.unsplash.com/photo-1500462918059-b1a0cb512f1d?w=500'],
    //     ['title' => 'Mad Max',      'poster_path' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500'],
    //     ['title' => 'Dune',         'poster_path' => 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=500'],
    // ]);


    public function getMoviesByGenre()
    {
        $moviesByGenre = [
            'Action' => [
                ['title' => 'Extraction',       'poster_path' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=500'],
                ['title' => 'The Dark Knight',  'poster_path' => 'https://images.unsplash.com/photo-1531259683007-016a7b628fc3?w=500'],
                ['title' => 'John Wick',        'poster_path' => 'https://images.unsplash.com/photo-1509347528160-9a9e33742cdb?w=500'],
                ['title' => 'Mad Max',          'poster_path' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500'],
                ['title' => 'Mission Impossible', 'poster_path' => 'https://images.unsplash.com/photo-1522748906645-95d8adfd52c7?w=500'],
                ['title' => 'Top Gun',          'poster_path' => 'https://images.unsplash.com/photo-1464037866556-6812c9d1c72e?w=500'],
                ['title' => 'Die Hard',         'poster_path' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=500'],
                ['title' => 'Speed',            'poster_path' => 'https://images.unsplash.com/photo-1489824904134-891ab64532f1?w=500'],
                ['title' => 'Gladiator',        'poster_path' => 'https://images.unsplash.com/photo-1561731216-c3a4d99437d5?w=500'],
                ['title' => 'The Raid',         'poster_path' => 'https://images.unsplash.com/photo-1574267432553-4b4628081c31?w=500'],
            ],
            'Comedy' => [
                ['title' => 'The Hangover',     'poster_path' => 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=500'],
                ['title' => 'Superbad',         'poster_path' => 'https://images.unsplash.com/photo-1542204165-65bf26472b9b?w=500'],
                ['title' => 'Step Brothers',    'poster_path' => 'https://images.unsplash.com/photo-1496275068113-fff8c90750d1?w=500'],
                ['title' => 'Bridesmaids',      'poster_path' => 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=500'],
                ['title' => 'Home Alone',       'poster_path' => 'https://images.unsplash.com/photo-1512389142860-9c449e58a543?w=500'],
                ['title' => 'Elf',              'poster_path' => 'https://images.unsplash.com/photo-1482517967863-00e15c9b44be?w=500'],
                ['title' => 'Anchorman',        'poster_path' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=500'],
                ['title' => 'Dumb and Dumber',  'poster_path' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=500'],
                ['title' => 'Tropic Thunder',   'poster_path' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=500'],
                ['title' => 'Game Night',       'poster_path' => 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?w=500'],
            ],
            'Romance' => [
                ['title' => 'Titanic',          'poster_path' => 'https://images.unsplash.com/photo-1505118380757-91f5f5632de0?w=500'],
                ['title' => 'The Notebook',     'poster_path' => 'https://images.unsplash.com/photo-1474552226712-ac0f0961a954?w=500'],
                ['title' => 'La La Land',       'poster_path' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=500'],
                ['title' => 'Crazy Rich Asians', 'poster_path' => 'https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?w=500'],
                ['title' => 'About Time',       'poster_path' => 'https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?w=500'],
                ['title' => 'Pride & Prejudice', 'poster_path' => 'https://images.unsplash.com/photo-1533777324565-a040eb52facd?w=500'],
                ['title' => 'Before Sunrise',   'poster_path' => 'https://images.unsplash.com/photo-1502175353174-a7a70e73b362?w=500'],
                ['title' => 'A Walk to Remember', 'poster_path' => 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?w=500'],
                ['title' => 'Me Before You',    'poster_path' => 'https://images.unsplash.com/photo-1516726817505-f5ed825624d8?w=500'],
                ['title' => 'Hitch',            'poster_path' => 'https://images.unsplash.com/photo-1533777324565-a040eb52facd?w=500'],
            ],
            'Thriller' => [
                ['title' => 'Gone Girl',        'poster_path' => 'https://images.unsplash.com/photo-1509248961158-e54f6934749c?w=500'],
                ['title' => 'Parasite',         'poster_path' => 'https://images.unsplash.com/photo-1536599018102-9f803c140fc1?w=500'],
                ['title' => 'Shutter Island',   'poster_path' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?w=500'],
                ['title' => 'Se7en',            'poster_path' => 'https://images.unsplash.com/photo-1500462918059-b1a0cb512f1d?w=500'],
                ['title' => 'Prisoners',        'poster_path' => 'https://images.unsplash.com/photo-1470252649378-9c29740c9fa8?w=500'],
                ['title' => 'Zodiac',           'poster_path' => 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=500'],
                ['title' => 'Silence of Lambs', 'poster_path' => 'https://images.unsplash.com/photo-1542204165-65bf26472b9b?w=500'],
                ['title' => 'No Country for Old Men', 'poster_path' => 'https://images.unsplash.com/photo-1464983953574-0892a716854b?w=500'],
                ['title' => 'Knives Out',       'poster_path' => 'https://images.unsplash.com/photo-1509347528160-9a9e33742cdb?w=500'],
                ['title' => 'The Girl with the Dragon Tattoo', 'poster_path' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=500'],
            ],
            'Sci-Fi' => [
                ['title' => 'Interstellar',     'poster_path' => 'https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?w=500'],
                ['title' => 'Avengers',         'poster_path' => 'https://images.unsplash.com/photo-1635805737707-575885ab0820?w=500'],
                ['title' => 'Inception',        'poster_path' => 'https://images.unsplash.com/photo-1500462918059-b1a0cb512f1d?w=500'],
                ['title' => 'Dune',             'poster_path' => 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=500'],
                ['title' => 'The Matrix',       'poster_path' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=500'],
                ['title' => 'Blade Runner 2049', 'poster_path' => 'https://images.unsplash.com/photo-1493514789931-586cb221d7a7?w=500'],
                ['title' => 'Arrival',          'poster_path' => 'https://images.unsplash.com/photo-1462332420958-a05d1e002413?w=500'],
                ['title' => 'Ex Machina',       'poster_path' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=500'],
                ['title' => 'Gravity',          'poster_path' => 'https://images.unsplash.com/photo-1454789548928-9efd52dc4031?w=500'],
                ['title' => 'The Martian',      'poster_path' => 'https://images.unsplash.com/photo-1541185933-ef5d8ed016c2?w=500'],
            ],
            'Horror' => [
                ['title' => 'The Conjuring',    'poster_path' => 'https://images.unsplash.com/photo-1605806616949-1e87b487fc2f?w=500'],
                ['title' => 'Hereditary',       'poster_path' => 'https://images.unsplash.com/photo-1509248961158-e54f6934749c?w=500'],
                ['title' => 'Get Out',          'poster_path' => 'https://images.unsplash.com/photo-1504701954957-2010ec3bcec1?w=500'],
                ['title' => 'It',               'poster_path' => 'https://images.unsplash.com/photo-1489824904134-891ab64532f1?w=500'],
                ['title' => 'A Quiet Place',    'poster_path' => 'https://images.unsplash.com/photo-1478720568477-152d9b164e26?w=500'],
                ['title' => 'Midsommar',        'poster_path' => 'https://images.unsplash.com/photo-1470252649378-9c29740c9fa8?w=500'],
                ['title' => 'The Shining',      'poster_path' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=500'],
                ['title' => 'Sinister',         'poster_path' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?w=500'],
                ['title' => 'Us',               'poster_path' => 'https://images.unsplash.com/photo-1551269901-5c5e14c25df7?w=500'],
                ['title' => 'The Babadook',     'poster_path' => 'https://images.unsplash.com/photo-1533777324565-a040eb52facd?w=500'],
            ],
        ];
        return $moviesByGenre;
    }

    public function getPopularMovies()
    {
        $popularMovies = [
            ['title' => 'Extraction',       'views' => 100000, 'poster_path' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=500'],
            ['title' => 'The Dark Knight',  'views' => 95000, 'poster_path' => 'https://images.unsplash.com/photo-1531259683007-016a7b628fc3?w=500'],
            ['title' => 'Interstellar',     'views' => 90000, 'poster_path' => 'https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?w=500'],
            ['title' => 'John Wick',        'views' => 85000, 'poster_path' => 'https://images.unsplash.com/photo-1509347528160-9a9e33742cdb?w=500'],
            ['title' => 'Avengers',         'views' => 80000, 'poster_path' => 'https://images.unsplash.com/photo-1635805737707-575885ab0820?w=500'],
            ['title' => 'Inception',        'views' => 75000, 'poster_path' => 'https://images.unsplash.com/photo-1500462918059-b1a0cb512f1d?w=500'],
            ['title' => 'Mad Max',          'views' => 70000, 'poster_path' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500'],
            ['title' => 'Dune',             'views' => 65000, 'poster_path' => 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=500'],
        ];
        return $popularMovies;
    }
}
