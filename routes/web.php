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

    // --- ALUR ROUTING EDIT PERSONA (TUGAS 1) ---
    Route::get('/profile/persona', [ProfileController::class, 'persona'])->name('profile.persona');
    Route::post('/profile/persona/update', [ProfileController::class, 'updatePersona'])->name('profile.persona.update');
    Route::post('/profile/persona/genres', [ProfileController::class, 'updateGenres'])->name('profile.persona.genres');
    Route::delete('/profile/persona/genres/{genre}', [ProfileController::class, 'destroyGenre'])->name('profile.persona.genres.destroy');

    Route::get('/discover', [DiscoverController::class, 'index'])->name('dashboard.discover');
    Route::get('/discover/results', [DiscoverController::class, 'results'])->name('discover.results');
    Route::get('/top_charted', [TopChartedController::class, 'index'])->name('dashboard.topCharted');

    //Movie Detail & Interaksi
    Route::post('/movie/{movie}/comment', [CommentController::class, 'store'])->name('comments.store');
    
    Route::post('/watchlist/{movie}', [WatchlistController::class, 'store'])->name('watchlist.store');
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
    Route::delete('/watchlist/{movie}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');

    Route::post('/favorite/{movie}', [FavoriteController::class, 'store'])->name('favorite.store');
    Route::delete('/favorite/{movie}', [FavoriteController::class, 'destroy'])->name('favorite.destroy');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    Route::get('/actor/{id}', function ($id) {
        $actorsData = \App\Models\Actor::with([
            'actormovies.movies.genres.genre'
        ])
        ->where('tmdb_actor_id', $id)
        ->firstOrFail();
        return view('pages.actor-detail', compact('actorsData'));
    })->name('actor.detail');

    Route::get('/movie/detail/{id}', [DetailController::class,'index'])->name('movie.detail');

    //Search Feature
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
});

require __DIR__ . '/auth.php';