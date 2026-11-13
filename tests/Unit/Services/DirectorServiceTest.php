<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Api\DirectorService;

class DirectorServiceTest extends TestCase
{
    protected DirectorService $directorService;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'mysql']);
        $this->directorService = app(DirectorService::class);
    }

    /**
     * Test findDirectorByName function
     * Input: 'Christopher Nolan'
     * Expected: Array with director details and known_for_movies list
     */
    public function test_find_director_by_name(): void
    {
        $input = 'Christopher Nolan';
        $results = $this->directorService->findDirectorByName($input, 1);

        $this->assertIsArray($results);
        $this->assertNotEmpty($results);
        $this->assertEquals('Christopher Nolan', $results[0]['name']);
        $this->assertArrayHasKey('known_for_movies', $results[0]);
    }

    /**
     * Test getMoviesByDirector function
     * Input: 'Christopher Nolan'
     * Expected: Array with director profile and list of directed movies
     */
    public function test_get_movies_by_director(): void
    {
        $input = 'Christopher Nolan';
        $result = $this->directorService->getMoviesByDirector($input, 5);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('director', $result);
        $this->assertArrayHasKey('movies', $result);
        $this->assertGreaterThan(0, $result['movies_count']);
        $this->assertEquals('Christopher Nolan', $result['director']['name']);
    }
}

