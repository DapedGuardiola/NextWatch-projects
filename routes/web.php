<?php

use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\detailController as ControllersDetailController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\TopChartedController;
use App\Services\LogActivityService;
use App\Services\DetailService;
use App\Models\Comment;
use Illuminate\Http\Request;
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

    Route::get('/top_charted', [TopChartedController::class, 'index'])->name('dashboard.topCharted');

    //Movie Detail
    Route::post('/movie/{movie}/comment', [CommentController::class, 'store'])
    ->name('comments.store');

    Route::post('/watchlist/{movie}', [WatchlistController::class, 'store'])
    ->name('watchlist.store');

    Route::get('/watchlist', [WatchlistController::class, 'index'])
    ->name('watchlist.index');

    Route::post('/watchlist/{movie}', [WatchlistController::class, 'store'])
    ->name('watchlist.store');

    Route::delete('/watchlist/{movie}', [WatchlistController::class, 'destroy'])
    ->name('watchlist.destroy');

    Route::post('/favorite/{movie}', [FavoriteController::class, 'store'])
    ->name('favorite.store');

    Route::delete('/favorite/{movie}', [FavoriteController::class, 'destroy'])
        ->name('favorite.destroy');

    Route::get('/favorites', [FavoriteController::class, 'index'])
        ->name('favorites.index');

    Route::get('/actor/{id}', function ($id) {
    $actorsData = \App\Models\Actor::with([
        'actormovies.movies.genres.genre'
    ])
    ->where('tmdb_actor_id', $id)
    ->firstOrFail();
    return view('pages.actor-detail', compact('actorsData'));
    })->name('actor.detail');

    Route::get('/movie/detail/{id}', [DetailController::class,'index'])->name('movie.detail');

    //Search

    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::get('/search/live', [SearchController::class, 'live'])->name('search.live');

    Route::post('/movie/comment', [CommentController::class, 'store'])
        ->middleware('auth')
        ->name('movie.comment');
        
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('movie.comment.update')->middleware('auth');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('movie.comment.destroy')->middleware('auth');

    // Route::get('/actor/{id}', function ($id) {
    // $actor = \App\Models\Actor::where('tmdb_actor_id', $id)->first();
    // if (!$actor) {
    //     abort(404);
    // }
    // return view('pages.actor-detail', compact('actor'));
    // })->name('actor.detail');
    
    Route::get('/profile/persona', [ProfileController::class, 'persona'])->name('profile.persona');
    Route::post('/profile/persona/update', [ProfileController::class, 'updatePersona'])->name('profile.persona.update');
    Route::post('/profile/persona/genres', [ProfileController::class, 'updateGenres'])->name('profile.persona.genres');
    Route::delete('/profile/persona/genres/{genre}', [ProfileController::class, 'destroyGenre'])->name('profile.persona.genres.destroy');
});

require __DIR__ . '/auth.php';