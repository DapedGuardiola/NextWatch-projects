<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FlaskService
{
    protected string $baseUrl;
    public function __construct()
    {
        $this->baseUrl = config('services.flask.url','http://localhost:5000');
    }
    public function getRanked(array $movies): array
    {
        $response = Http::post('{$baseUrl}/saw/rank', ['movies' => $movies]);
        if ($response->failed()) {
            throw new \Exception('API Error: ' . $response->status());
        }
        return $response->json('ranked_id');
    }
    public function getDiscoverTest(array $user_vector,array $movies): array
    {
        $response = Http::post("{$this->baseUrl}/saw/discoverTest", [
            'user_vector' => $user_vector,
            'movies' => $movies,
        ]);

        if ($response->failed()) {
            throw new \Exception('Flask API Error: ' . $response->status());
        }

        return $response->json('ranked_id');
    }
    public function getDiscover(array $movies): array
    {
        $response = Http::post("{$this->baseUrl}/saw/discover", [
            'movies' => $movies,
        ]);

        if ($response->failed()) {
            throw new \Exception('Flask API Error: ' . $response->status());
        }

        return $response->json('ranked_id');
    }

    public function getAllTimeBest(array $movies): array
    {
        $response = Http::post("{$this->baseUrl}/edas/alltime", [
            'movies' => $movies,
        ]);

        if ($response->failed()) {
            throw new \Exception('Flask EDAS Alltime Error: ' . $response->status());
        }

        return $response->json('ranked_id');
    }

    public function getBestMoviesByGenre(array $movies): array
    {
        $response = Http::post("{$this->baseUrl}/edas/bestbygenre", [
            'movies' => $movies,
        ]);

        if ($response->failed()) {
            throw new \Exception('Flask EDAS Error: ' . $response->status());
        }

        return $response->json('ranked_id');
    }

    public function getSimilar(array $target_movie, array $movies)
    {
        $response = Http::post("{$this->baseUrl}/cbf/similar", [
            'target_movie' => $target_movie,
            'movies' => $movies,
        ]);

        if ($response->failed()) {
            throw new \Exception('Flask API Error: ' . $response->status());
        }

        // dd($response->json('similar_ids'));  
        return $response->json('similar_ids');
    }
}
