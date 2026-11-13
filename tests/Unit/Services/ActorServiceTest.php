<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Api\ActorService;

class ActorServiceTest extends TestCase
{
    protected ActorService $actorService;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'mysql']);
        $this->actorService = app(ActorService::class);
    }

    /**
     * Test findActorByName function
     * Input: 'Leonardo DiCaprio'
     * Expected: Array with actor details and known_for_movies list
     */
    public function test_find_actor_by_name(): void
    {
        $input = 'Leonardo DiCaprio';
        $results = $this->actorService->findActorByName($input, 1);

        $this->assertIsArray($results);
        $this->assertNotEmpty($results);
        $this->assertEquals('Leonardo DiCaprio', $results[0]['name']);
        $this->assertArrayHasKey('known_for_movies', $results[0]);
        $this->assertNotEmpty($results[0]['known_for_movies']);
    }

    /**
     * Test getMoviesByActor function
     * Input: 'Leonardo DiCaprio'
     * Expected: Array with actor profile and list of movies starred by actor
     */
    public function test_get_movies_by_actor(): void
    {
        $input = 'Leonardo DiCaprio';
        $result = $this->actorService->getMoviesByActor($input, 5);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('actor', $result);
        $this->assertArrayHasKey('movies', $result);
        $this->assertGreaterThan(0, $result['movies_count']);
        $this->assertEquals('Leonardo DiCaprio', $result['actor']['name']);
    }

    /**
     * Test getSimilarActors function
     * Input: 'Leonardo DiCaprio'
     * Expected: Array with reference_actor and list of similar_actors
     */
    public function test_get_similar_actors(): void
    {
        $input = 'Leonardo DiCaprio';
        $result = $this->actorService->getSimilarActors($input, 3);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('reference_actor', $result);
        $this->assertArrayHasKey('similar_actors', $result);
        $this->assertEquals('Leonardo DiCaprio', $result['reference_actor']['name']);
    }
}

