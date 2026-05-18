<?php

use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
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
    Route::get('/top_charted', [DashboardController::class, 'topCharted'])->name('dashboard.topCharted');

    Route::post('/movie/{movie}/comment', [CommentController::class, 'store'])
    ->name('comments.store');
    
    Route::get('/movie/detail/{id}', function ($id) {
    $movie = \App\Models\Movie::where('tmdb_movie_id', $id)->firstOrFail();
    $comments = $movie->comments()
        ->with('user')
        ->latest()
        ->get();
    $similarMovies = \App\Models\Movie::where('id', '!=', $movie->id)
        ->take(8)
        ->get();
    return view('pages.movie-detail', compact(
        'movie',
        'comments',
        'similarMovies'
    ));
    })->name('movie.detail');

    Route::get('/movie/detail/{id}', function ($id) {
    $movie = \App\Models\Movie::where(
        'tmdb_movie_id',
        $id
    )->firstOrFail();
    $comments = $movie->comments()->latest()->get();
    $similarMovies = \App\Models\Movie::where(
        'tmdb_movie_id',
        '!=',
        $movie->tmdb_movie_id
    )
    ->take(8)
    ->get();
    return view('pages.movie-detail', compact(
        'movie',
        'comments',
        'similarMovies'
    ));
    })->name('movie.detail');

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

    Route::post('/movies/{movie}/comments', [CommentController::class, 'store'])
    ->name('comments.store');

require __DIR__ . '/auth.php';