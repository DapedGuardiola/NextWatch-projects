<?php

use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profileUI', [ProfileController::class, 'index'])->name('profile.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');

    Route::get('/settings', [ProfileController::class, 'settings'])->name('profile.settings');

    Route::put('/settings/update', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');

    Route::get('/discover', [DiscoverController::class, 'index'])->name('dashboard.discover');
    Route::get('/discover/results', [DiscoverController::class, 'results'])->name('discover.results');
    Route::get('/top_charted', [DashboardController::class, 'topCharted'])->name('dashboard.topCharted');

    Route::get('/actor/detail/{id}',[DashboardController::class,'getActorMovie'])->name('actor.detail');

    Route::get('/movie/detail/{id}',function($id){
    $movie = \App\Models\Movie::where('tmdb_movie_id',$id)->first();
    $comments = [];
    $similarMovies = \App\Models\Movie::take(8)->get();
    return view('pages.movie-detail', compact(
        'movie',
        'comments',
        'similarMovies'
    ));
    })->name('movie.detail');
    Route::get('/movie-test', function () {
    $movie = \App\Models\Movie::first();
    $comments = [];
    $similarMovies = \App\Models\Movie::take(8)->get();
    return view('pages.movie-detail', compact(
        'movie',
        'comments',
        'similarMovies'
    ));
    })->middleware('auth');

});

require __DIR__ . '/auth.php';