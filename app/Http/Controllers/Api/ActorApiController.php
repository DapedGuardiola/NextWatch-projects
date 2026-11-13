<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\ActorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ActorApiController extends Controller
{
    public function __construct(
        protected ActorService $actorService
    ) {}

    /**
     * GET /api/actors/search?name=...
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

        $actors = $this->actorService->findActorByName($name, $limit);

        return response()->json([
            'status' => 'success',
            'count'  => count($actors),
            'data'   => $actors,
        ]);
    }

    /**
     * GET /api/actors/{identifier}/movies
     */
    public function movies(string $identifier, Request $request): JsonResponse
    {
        $limit = (int)$request->query('limit', 10);
        $result = $this->actorService->getMoviesByActor($identifier, $limit);

        if (!$result) {
            return response()->json([
                'status'  => 'error',
                'message' => "Aktor '{$identifier}' tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $result,
        ]);
    }

    /**
     * GET /api/actors/{identifier}/similar
     */
    public function similar(string $identifier, Request $request): JsonResponse
    {
        $limit = (int)$request->query('limit', 5);
        $result = $this->actorService->getSimilarActors($identifier, $limit);

        if (!$result) {
            return response()->json([
                'status'  => 'error',
                'message' => "Aktor '{$identifier}' tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $result,
        ]);
    }
}

