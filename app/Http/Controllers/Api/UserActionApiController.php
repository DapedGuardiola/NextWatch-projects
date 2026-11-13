<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\UserActionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserActionApiController extends Controller
{
    public function __construct(
        protected UserActionService $userActionService
    ) {}

    protected function getUserId(Request $request): int
    {
        return (int)($request->input('user_id') ?? $request->query('user_id', 4));
    }

    // =========================================================================
    // 1. WATCHLIST
    // =========================================================================

    public function getWatchlist(Request $request): JsonResponse
    {
        $userId = $this->getUserId($request);
        $limit  = (int)$request->query('limit', 20);

        $result = $this->userActionService->getUserWatchlist($userId, $limit);
        return response()->json(['status' => 'success', 'data' => $result]);
    }

    public function addToWatchlist(Request $request): JsonResponse
    {
        $movie  = $request->input('movie') ?? $request->input('identifier');
        $userId = $this->getUserId($request);

        if (empty($movie)) {
            return response()->json(['status' => 'error', 'message' => "Parameter 'movie' wajib diisi."], 400);
        }

        $result = $this->userActionService->addToWatchlist($movie, $userId);
        return response()->json($result, ($result['status'] === 'success') ? 200 : 404);
    }

    public function removeFromWatchlist(Request $request): JsonResponse
    {
        $movie  = $request->input('movie') ?? $request->query('movie') ?? $request->input('identifier') ?? $request->query('identifier');
        $userId = $this->getUserId($request);

        if (empty($movie)) {
            return response()->json(['status' => 'error', 'message' => "Parameter 'movie' wajib diisi."], 400);
        }

        $result = $this->userActionService->removeFromWatchlist($movie, $userId);
        return response()->json($result, ($result['status'] === 'success') ? 200 : 404);
    }

    // =========================================================================
    // 2. FAVORITES
    // =========================================================================

    public function getFavorites(Request $request): JsonResponse
    {
        $userId = $this->getUserId($request);
        $limit  = (int)$request->query('limit', 20);

        $result = $this->userActionService->getUserFavorites($userId, $limit);
        return response()->json(['status' => 'success', 'data' => $result]);
    }

    public function addToFavorites(Request $request): JsonResponse
    {
        $movie  = $request->input('movie') ?? $request->input('identifier');
        $userId = $this->getUserId($request);

        if (empty($movie)) {
            return response()->json(['status' => 'error', 'message' => "Parameter 'movie' wajib diisi."], 400);
        }

        $result = $this->userActionService->addToFavorites($movie, $userId);
        return response()->json($result, ($result['status'] === 'success') ? 200 : 404);
    }

    public function removeFromFavorites(Request $request): JsonResponse
    {
        $movie  = $request->input('movie') ?? $request->query('movie') ?? $request->input('identifier') ?? $request->query('identifier');
        $userId = $this->getUserId($request);

        if (empty($movie)) {
            return response()->json(['status' => 'error', 'message' => "Parameter 'movie' wajib diisi."], 400);
        }

        $result = $this->userActionService->removeFromFavorites($movie, $userId);
        return response()->json($result, ($result['status'] === 'success') ? 200 : 404);
    }

    // =========================================================================
    // 3. GENRES
    // =========================================================================

    public function getFavoriteGenres(Request $request): JsonResponse
    {
        $userId = $this->getUserId($request);
        $result = $this->userActionService->getUserFavoriteGenres($userId);
        return response()->json(['status' => 'success', 'data' => $result]);
    }

    public function addFavoriteGenre(Request $request): JsonResponse
    {
        $genre  = $request->input('genre') ?? $request->input('genre_name');
        $userId = $this->getUserId($request);

        if (empty($genre)) {
            return response()->json(['status' => 'error', 'message' => "Parameter 'genre' wajib diisi."], 400);
        }

        $result = $this->userActionService->addFavoriteGenre($genre, $userId);
        return response()->json($result, ($result['status'] === 'success') ? 200 : 404);
    }

    public function removeFavoriteGenre(Request $request): JsonResponse
    {
        $genre  = $request->input('genre') ?? $request->query('genre') ?? $request->input('genre_name') ?? $request->query('genre_name');
        $userId = $this->getUserId($request);

        if (empty($genre)) {
            return response()->json(['status' => 'error', 'message' => "Parameter 'genre' wajib diisi."], 400);
        }

        $result = $this->userActionService->removeFavoriteGenre($genre, $userId);
        return response()->json($result, ($result['status'] === 'success') ? 200 : 404);
    }

    public function setFavoriteGenres(Request $request): JsonResponse
    {
        $genres = $request->input('genres');
        $userId = $this->getUserId($request);

        if (is_string($genres)) {
            $genres = array_map('trim', explode(',', $genres));
        }

        if (empty($genres) || !is_array($genres)) {
            return response()->json(['status' => 'error', 'message' => "Parameter 'genres' harus berupa array atau daftar koma."], 400);
        }

        $result = $this->userActionService->setFavoriteGenres($genres, $userId);
        return response()->json($result, ($result['status'] === 'success') ? 200 : 404);
    }

    // =========================================================================
    // 4. ACTORS
    // =========================================================================

    public function getFavoriteActors(Request $request): JsonResponse
    {
        $userId = $this->getUserId($request);
        $result = $this->userActionService->getUserFavoriteActors($userId);
        return response()->json(['status' => 'success', 'data' => $result]);
    }

    public function addFavoriteActor(Request $request): JsonResponse
    {
        $actor  = $request->input('actor') ?? $request->input('identifier');
        $userId = $this->getUserId($request);

        if (empty($actor)) {
            return response()->json(['status' => 'error', 'message' => "Parameter 'actor' wajib diisi."], 400);
        }

        $result = $this->userActionService->addFavoriteActor($actor, $userId);
        return response()->json($result, ($result['status'] === 'success') ? 200 : 404);
    }

    public function removeFavoriteActor(Request $request): JsonResponse
    {
        $actor  = $request->input('actor') ?? $request->query('actor') ?? $request->input('identifier') ?? $request->query('identifier');
        $userId = $this->getUserId($request);

        if (empty($actor)) {
            return response()->json(['status' => 'error', 'message' => "Parameter 'actor' wajib diisi."], 400);
        }

        $result = $this->userActionService->removeFavoriteActor($actor, $userId);
        return response()->json($result, ($result['status'] === 'success') ? 200 : 404);
    }

    // =========================================================================
    // 5. COMMENTS
    // =========================================================================

    public function postComment(Request $request): JsonResponse
    {
        $movie   = $request->input('movie') ?? $request->input('identifier');
        $content = $request->input('content') ?? $request->input('comment');
        $userId  = $this->getUserId($request);

        if (empty($movie) || empty($content)) {
            return response()->json([
                'status'  => 'error',
                'message' => "Parameter 'movie' dan 'content' wajib disertakan.",
            ], 400);
        }

        $result = $this->userActionService->postMovieComment($movie, $content, $userId);
        return response()->json($result, ($result['status'] === 'success') ? 201 : 404);
    }

    public function deleteComment(Request $request): JsonResponse
    {
        $commentId = (int)($request->input('comment_id') ?? $request->query('comment_id'));
        $userId    = $this->getUserId($request);

        if (empty($commentId)) {
            return response()->json(['status' => 'error', 'message' => "Parameter 'comment_id' wajib disertakan."], 400);
        }

        $result = $this->userActionService->deleteMovieComment($commentId, $userId);
        return response()->json($result, ($result['status'] === 'success') ? 200 : 404);
    }
}

