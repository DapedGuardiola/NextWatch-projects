<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\MovieService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MovieApiController extends Controller
{
    public function __construct(
        protected MovieService $movieService
    ) {}

    /**
     * GET /api/movies/search
     */
    public function search(Request $request): JsonResponse
    {
        $params = [
            'query'        => $request->query('query') ?? $request->query('q'),
            'genre'        => $request->query('genre'),
            'min_rating'   => $request->query('min_rating'),
            'release_year' => $request->query('year') ?? $request->query('release_year'),
            'limit'        => $request->query('limit', 10),
        ];

        $results = $this->movieService->searchMovies($params);

        return response()->json([
            'status' => 'success',
            'count'  => count($results),
            'data'   => $results,
        ]);
    }

    /**
     * GET /api/movies/top-rated
     */
    public function topRated(Request $request): JsonResponse
    {
        $limit     = (int)$request->query('limit', 10);
        $genre     = $request->query('genre');
        $minRating = (float)$request->query('min_rating', 7.5);

        $results = $this->movieService->getTopRatedMovies($limit, $genre, $minRating);

        return response()->json([
            'status' => 'success',
            'count'  => count($results),
            'data'   => $results,
        ]);
    }

    /**
     * GET /api/movies/compare?movie1=...&movie2=...
     */
    public function compare(Request $request): JsonResponse
    {
        $m1 = $request->query('movie1') ?? $request->query('m1');
        $m2 = $request->query('movie2') ?? $request->query('m2');

        if (empty($m1) || empty($m2)) {
            return response()->json([
                'status'  => 'error',
                'message' => "Parameter 'movie1' dan 'movie2' wajib diisi untuk perbandingan film.",
            ], 400);
        }

        $comparison = $this->movieService->compareMovies($m1, $m2);

        if (!$comparison || (isset($comparison['status']) && $comparison['status'] === 'error')) {
            return response()->json([
                'status'  => 'error',
                'message' => $comparison['message'] ?? 'Gagal membandingkan kedua film.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $comparison,
        ]);
    }

    /**
     * GET /api/movies/decade?decade=1990s
     */
    public function byDecade(Request $request): JsonResponse
    {
        $decade    = $request->query('decade', '2010s');
        $genre     = $request->query('genre');
        $minRating = (float)$request->query('min_rating', 6.5);
        $limit     = (int)$request->query('limit', 10);

        $result = $this->movieService->getMoviesByDecade($decade, $genre, $minRating, $limit);

        return response()->json([
            'status' => 'success',
            'data'   => $result,
        ]);
    }

    /**
     * GET /api/movies/mood?mood=mind_bending
     */
    public function byMood(Request $request): JsonResponse
    {
        $mood  = $request->query('mood', 'happy');
        $limit = (int)$request->query('limit', 10);

        $result = $this->movieService->recommendByMood($mood, $limit);

        return response()->json([
            'status' => 'success',
            'data'   => $result,
        ]);
    }

    /**
     * GET /api/movies/{identifier}/similar
     */
    public function similar(string $identifier, Request $request): JsonResponse
    {
        $limit = (int)$request->query('limit', 8);
        $result = $this->movieService->getSimilarMovies($identifier, $limit);

        if (!$result) {
            return response()->json([
                'status'  => 'error',
                'message' => "Film '{$identifier}' tidak ditemukan untuk pencarian film serupa.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $result,
        ]);
    }

    /**
     * GET /api/movies/{identifier}/discussions
     */
    public function discussions(string $identifier, Request $request): JsonResponse
    {
        $limit = (int)$request->query('limit', 10);
        $result = $this->movieService->getMovieDiscussions($identifier, $limit);

        if (!$result) {
            return response()->json([
                'status'  => 'error',
                'message' => "Film '{$identifier}' tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $result,
        ]);
    }

    /**
     * GET /api/movies/{id_or_title}
     */
    public function detail(string $identifier): JsonResponse
    {
        $movie = $this->movieService->getMovieDetail($identifier);

        if (!$movie) {
            return response()->json([
                'status'  => 'error',
                'message' => "Movie '{$identifier}' tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $movie,
        ]);
    }
}
