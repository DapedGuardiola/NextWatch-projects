<?php

use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TopChartedController;
use App\Services\LogActivityService;
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

    Route::get('/movie/detail/{id}', function ($id) {
    $user_id = auth()->id();
    $movie = \App\Models\Movie::where(
        'tmdb_movie_id',
        $id
    )->firstOrFail();
    $logActivityService = new LogActivityService();
    $logActivityService->click(['user_id'=>$user_id,'movie_id'=>$id]);
    $comments = $movie->comments()
        ->with('user')
        ->latest()
        ->get();

    $similarMovies = \App\Models\Movie::where(
        'tmdb_movie_id',
        '!=',
        $movie->tmdb_movie_id
    )
    ->take(8)
    ->get();

    $isInWatchlist = \App\Models\Watchlist::where(
        'user_id',
        auth()->id()
    )
    ->where(
        'movie_id',
        $movie->tmdb_movie_id
    )
    ->exists();

    $isFavorite = \App\Models\Favorite::where(
        'user_id',
        auth()->id()
    )
    ->where(
        'movie_id',
        $movie->tmdb_movie_id
    )
    ->exists();

    return view('pages.movie-detail', compact(
        'movie',
        'comments',
        'similarMovies',
        'isInWatchlist',
        'isFavorite'
    ));

    })->name('movie.detail');

    //Search

    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::get('/search/live', [SearchController::class, 'live'])->name('search.live');

    Route::post('/movie/comment', function (Request $request) {
    $request->validate([
        'movie_id' => 'required',
        'content' => 'required|string|max:1000',
    ]);
    Comment::create([
        'user_id' => auth()->id(),
        'movie_id' => $request->movie_id,
        'reply_id' => $request->reply_id,
        'tagged_user_id' => $request->tagged_user_id,
        'content' => $request->content,
    ]);
    return back();
    })->middleware('auth')->name('movie.comment');

    Route::post('/movie/{movie}/comment', [CommentController::class, 'store'])
    ->name('comments.store');

    // Route::get('/actor/{id}', function ($id) {
    // $actor = \App\Models\Actor::where('tmdb_actor_id', $id)->first();
    // if (!$actor) {
    //     abort(404);
    // }
    // return view('pages.actor-detail', compact('actor'));
    // })->name('actor.detail');
});

require __DIR__ . '/auth.php';