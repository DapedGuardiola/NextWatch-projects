<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Api\RecommendationService;

class RecommendationServiceTest extends TestCase
{
    protected RecommendationService $recommendationService;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'mysql']);
        $this->recommendationService = app(RecommendationService::class);
    }

    /**
     * Test getPersonalizedSaw function
     * Input: userVector = null, limit = 5
     * Expected: Array with algorithm 'SAW' and recommendations list
     */
    public function test_get_personalized_saw(): void
    {
        $result = $this->recommendationService->getPersonalizedSaw(null, 5);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('algorithm', $result);
        $this->assertArrayHasKey('recommendations', $result);
        $this->assertStringContainsString('SAW', $result['algorithm']);
        $this->assertNotEmpty($result['recommendations']);
    }

    /**
     * Test getAllTimeBestEdas function
     * Input: genre = 'Action', limit = 5
     * Expected: Array with algorithm 'EDAS', genre_filter, and recommendations list
     */
    public function test_get_all_time_best_edas(): void
    {
        $result = $this->recommendationService->getAllTimeBestEdas('Action', 5);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('algorithm', $result);
        $this->assertArrayHasKey('recommendations', $result);
        $this->assertStringContainsString('EDAS', $result['algorithm']);
        $this->assertNotEmpty($result['recommendations']);
    }
}

