<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\DirectorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DirectorApiController extends Controller
{
    public function __construct(
        protected DirectorService $directorService
    ) {}

    /**
     * GET /api/directors/search?name=...
     */
    public function search(Request $request): JsonResponse
    {
        $name  = $request->query('name') ?? $request->query('q', '');
        $limit = (int)$request->query('limit', 5);

        if (empty($name)) {
            return response()->json([
                'status'  => 'error',
                'message' => "Parameter 'name' harus disertakan.",
            ], 400);
        }

        $directors = $this->directorService->findDirectorByName($name, $limit);

        return response()->json([
            'status' => 'success',
            'count'  => count($directors),
            'data'   => $directors,
        ]);
    }

    /**
     * GET /api/directors/{identifier}/movies
     */
    public function movies(string $identifier, Request $request): JsonResponse
    {
        $limit = (int)$request->query('limit', 10);
        $result = $this->directorService->getMoviesByDirector($identifier, $limit);

        if (!$result) {
            return response()->json([
                'status'  => 'error',
                'message' => "Sutradara (director) '{$identifier}' tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $result,
        ]);
    }
}

