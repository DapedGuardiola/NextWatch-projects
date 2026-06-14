<?php

namespace App\Services;

use App\Models\UserTaste;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\use;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class FlaskService
{
    protected string $baseUrl;
    public function __construct()
    {
        $this->baseUrl = config('services.flask.url', 'http://localhost:5000');
    }
    public function getRanked(array $movies): array
    {
        $response = Http::post('{$baseUrl}/saw/rank', ['movies' => $movies]);
        if ($response->failed()) {
            throw new \Exception('API Error: ' . $response->status());
        }
        return $response->json('ranked_id');
    }
    public function getDiscoverTest(array $user_vector, array $movies): array
    {
        $response = Http::timeout(15)->post("{$this->baseUrl}/saw/discoverTest", [
            'user_vector' => $user_vector,
            'movie_ids'   => $movies,
        ]);

        // Tambahkan ini sementara untuk debug
        Log::debug('Flask raw response', [
            'status' => $response->status(),
            'body'   => $response->body(), // <-- lihat ini di log
        ]);

        if ($response->failed()) {
            throw new \Exception('Flask API Error: ' . $response->status());
        }

        $data = $response->json();

        if (!isset($data['ranked_id'])) {
            Log::error('Flask response missing ranked_id', ['response' => $data]);
            return [];
        }

        return $data['ranked_id'];
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

    public function getBestMoviesByGenre(array $movies, string $genreName = ''): array
    {
        $response = Http::post("{$this->baseUrl}/edas/bestbygenre", [
            'movies'     => $movies,
            'genre_name' => $genreName,   // tambahan
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
    public function computeNewTaste(array $userGenres, array $favoriteMovieData)
    {
        // Log::info('Flask ServicefavoriteMovieData : ' . json_encode($favoriteMovieData));
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/compute/new-taste", [
            'userGenres' => $userGenres,
            'favoriteMovieData' => $favoriteMovieData,
        ]);
        $userNewTastes = $response->json('userNewTastes');
        // Log::info('userNewTastes flask: ' . json_encode($userNewTastes));
        if ($response->failed()) {
            throw new \Exception('Flask API Error: ' . $response->status());
        }
        // Log::info('Flask Service Response : ' . json_encode($response));
        return $response;
    }
    public function computeRecommendation(Collection $userGenres, array $userTastes, array $movies)
    {
        Log::info('userGenresForRecommendation: ' . json_encode($userGenres));
        Log::info('userTastesForRecommendation: ' . json_encode($userTastes));
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/compute/new-recommendation", [
            'userGenres' => $userGenres,
            'userTastes' => $userTastes,
            'movies' => $movies,
        ]);
        $recomendation_ids = $response->json('recommendation_ids');
        Log::info('userRecommendation: ' . json_encode($recomendation_ids));
        if ($response->failed()) {
            throw new \Exception('Flask API Error: ' . $response->status());
        }
        Log::info('Flask Service Response : ' . json_encode($response));
        return $recomendation_ids;
    }
    public function computeReevalTastes(array $userTastes, array $userGenres, array $userLog, array $movies)
    {
        Log::info('userGenresForRecommendation: ' . json_encode($userGenres));
        Log::info('userTastesForRecommendation: ' . json_encode($userTastes));
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/compute/recompute-tastes", [
            'userGenres' => $userGenres,
            'userLog' => $userLog,
            'userTastes' => $userTastes,
            'movies' => $movies,
        ]);
        $userNewTastes = $response->json('userNewTastes');
        Log::info('userRecommendation: ' . json_encode($response));
        if ($response->failed()) {
            throw new \Exception('Flask API Error: ' . $response->status());
        }
        Log::info('Flask Service Response : ' . json_encode($response));
        return $userNewTastes;
    }
    public function getIntersectGenres(array $moviesData): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/compute/intersect-genres", [
                'movies' => $moviesData
            ]);

            if ($response->successful()) {
                return $response->json()['valid_movie_ids'] ?? [];
            }
            return [];
        } catch (\Exception $e) {
            Log::error("Flask Error (Intersect): " . $e->getMessage());
            return [];
        }
    }
}
