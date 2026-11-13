<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MovieApiController;
use App\Http\Controllers\Api\ActorApiController;
use App\Http\Controllers\Api\DirectorApiController;
use App\Http\Controllers\Api\RecommendationApiController;
use App\Http\Controllers\Api\UserActionApiController;

/*
|--------------------------------------------------------------------------
| Internal API Routes for AI Orchestrator / MCP Tools (Modular)
|--------------------------------------------------------------------------
*/

Route::prefix('movies')->group(function () {
    Route::get('/search', [MovieApiController::class, 'search']);
    Route::get('/top-rated', [MovieApiController::class, 'topRated']);
    Route::get('/compare', [MovieApiController::class, 'compare']);
    Route::get('/decade', [MovieApiController::class, 'byDecade']);
    Route::get('/mood', [MovieApiController::class, 'byMood']);
    Route::get('/{identifier}/similar', [MovieApiController::class, 'similar']);
    Route::get('/{identifier}/discussions', [MovieApiController::class, 'discussions']);
    Route::get('/{identifier}', [MovieApiController::class, 'detail']);
});

Route::prefix('actors')->group(function () {
    Route::get('/search', [ActorApiController::class, 'search']);
    Route::get('/{identifier}/movies', [ActorApiController::class, 'movies']);
    Route::get('/{identifier}/similar', [ActorApiController::class, 'similar']);
});

Route::prefix('directors')->group(function () {
    Route::get('/search', [DirectorApiController::class, 'search']);
    Route::get('/{identifier}/movies', [DirectorApiController::class, 'movies']);
});

Route::prefix('recommendations')->group(function () {
    Route::get('/saw', [RecommendationApiController::class, 'saw']);
    Route::get('/edas', [RecommendationApiController::class, 'edas']);
});

Route::prefix('user')->group(function () {
    // Watchlist
    Route::get('/watchlist', [UserActionApiController::class, 'getWatchlist']);
    Route::post('/watchlist', [UserActionApiController::class, 'addToWatchlist']);
    Route::delete('/watchlist', [UserActionApiController::class, 'removeFromWatchlist']);

    // Favorites
    Route::get('/favorites', [UserActionApiController::class, 'getFavorites']);
    Route::post('/favorites', [UserActionApiController::class, 'addToFavorites']);
    Route::delete('/favorites', [UserActionApiController::class, 'removeFromFavorites']);

    // Genres
    Route::get('/genres', [UserActionApiController::class, 'getFavoriteGenres']);
    Route::post('/genres', [UserActionApiController::class, 'addFavoriteGenre']);
    Route::delete('/genres', [UserActionApiController::class, 'removeFavoriteGenre']);
    Route::put('/genres', [UserActionApiController::class, 'setFavoriteGenres']);

    // Actors
    Route::get('/actors', [UserActionApiController::class, 'getFavoriteActors']);
    Route::post('/actors', [UserActionApiController::class, 'addFavoriteActor']);
    Route::delete('/actors', [UserActionApiController::class, 'removeFavoriteActor']);

    // Comments
    Route::post('/comments', [UserActionApiController::class, 'postComment']);
    Route::delete('/comments', [UserActionApiController::class, 'deleteComment']);
});
