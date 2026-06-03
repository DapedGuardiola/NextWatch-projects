<?php

use App\Http\Controllers\PersonalizationController;
use App\Http\Controllers\LandingController;
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
use App\Http\Controllers\CollectionController;
use App\Services\LogActivityService;
use App\Services\DetailService;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

// Tinggal test di route sementara
Route::get('/test-redis', function () {
    Cache::put('test', 'Redis jalan! ✅', 60);
    return Cache::get('test');
});
Route::get('/test-job', function () {
    dispatch(function () {
        Log::info('Worker jalan! ✅');
    });
    return 'Job dispatched!';
});

Route::get('/', [LandingController::class, 'index']);

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/login', function () {
    $popularMovie  = \App\Models\Movie::orderBy('popularity', 'desc')->first();
    $moviesByGenre = [];
    $actors        = collect();
    return view('landing', compact('popularMovie', 'moviesByGenre', 'actors'));
})->middleware('guest')->name('login');

Route::get('/register', function () {
    $popularMovie  = \App\Models\Movie::orderBy('popularity', 'desc')->first();
    $moviesByGenre = [];
    $actors        = collect();
    return view('landing', compact('popularMovie', 'moviesByGenre', 'actors'));
})->middleware('guest')->name('register');

Route::middleware('auth')->group(function () {
   
    Route::get('/test-function', [DiscoverController::class,'testFunction']);

    Route::get('/persona-loading', function () {
        return view('pages.loading-persona');
    })->name('persona-loading');
    
    Route::get('/persona-status', function () {
        return response()->json([
            'ready' => (bool) auth()->user()->persona_ready
        ]);
    });

    Route::get('/personalization', [PersonalizationController::class, 'index'])->name('personalization.index');
    Route::post('/personalization', [PersonalizationController::class, 'store'])->name('personalization.store');

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

    // Watchlist & Favorites
    Route::post('/watchlist/{movie}', [WatchlistController::class, 'store'])->name('watchlist.store');
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
    Route::delete('/watchlist/{movie}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');

    Route::post('/favorite/{movie}', [FavoriteController::class, 'store'])->name('favorite.store');
    Route::delete('/favorite/{movie}', [FavoriteController::class, 'destroy'])->name('favorite.destroy');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    
    Route::get('/movie/detail/{id}', [DetailController::class, 'index'])->name('movie.detail');
    Route::get('/collection/{id}', [CollectionController::class, 'show'])->name('collection.detail');
    Route::get('/actor/{id}', [DashboardController::class, 'getActorMovie'])->name('actor.detail');
    

    // Search
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::get('/search/live', [SearchController::class, 'live'])->name('search.live');

    // Comments
    Route::post('/movie/comment', [CommentController::class, 'store'])->name('movie.comment');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('movie.comment.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('movie.comment.destroy');

    // Persona & Genres
    Route::get('/profile/persona', [ProfileController::class, 'persona'])->name('profile.persona');
    Route::post('/profile/persona/update', [ProfileController::class, 'updatePersona'])->name('profile.persona.update');
    Route::post('/profile/persona/genres', [ProfileController::class, 'updateGenres'])->name('profile.persona.genres');
    Route::delete('/profile/persona/genres/{genre}', [ProfileController::class, 'destroyGenre'])->name('profile.persona.genres.destroy');

    // Log Activity
    Route::post('/log-activity', [LogActivityService::class, 'click'])->name('click-movie');
});

require __DIR__ . '/auth.php';