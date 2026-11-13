<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RecommendationApiController extends Controller
{
    public function __construct(
        protected RecommendationService $recommendationService
    ) {}

    /**
     * GET /api/recommendations/saw
     */
    public function saw(Request $request): JsonResponse
    {
        $limit       = (int)$request->query('limit', 10);
        $userVector  = $request->query('vector') ? json_decode($request->query('vector'), true) : null;

        $result = $this->recommendationService->getPersonalizedSaw($userVector, $limit);

        return response()->json([
            'status' => 'success',
            'data'   => $result,
        ]);
    }

    /**
     * GET /api/recommendations/edas
     */
    public function edas(Request $request): JsonResponse
    {
        $limit = (int)$request->query('limit', 10);
        $genre = $request->query('genre');

        $result = $this->recommendationService->getAllTimeBestEdas($genre, $limit);

        return response()->json([
            'status' => 'success',
            'data'   => $result,
        ]);
    }
}

