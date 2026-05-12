<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FlaskService
{
    protected string $baseUrl;
    public function __construct()
    {
        $this->baseUrl = config('services.flask.url');
    }
    public function getRanked(array $movies): array
    {
        $response = Http::post('{$baseUrl}/saw/rank', ['movies' => $movies]);
        if ($response->failed()) {
            throw new \Exception('API Error: ' . $response->status());
        }
        return $response->json('ranked_id');
    }
}
