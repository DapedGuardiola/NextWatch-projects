<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Api\MovieService;

class MovieServiceTest extends TestCase
{
    protected MovieService $movieService;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'mysql']);
        $this->movieService = app(MovieService::class);
    }

    /**
     * Test searchMovies function
     * Input: ['query' => 'Inception', 'limit' => 3]
     * Expected: Array of movies containing id, title, rating, genres
     */
    public function test_search_movies_with_query(): void
    {
        $input = [
            'query' => 'Inception',
            'limit' => 3,
        ];

        $results = $this->movieService->searchMovies($input);

        $this->assertIsArray($results);
        $this->assertNotEmpty($results);
        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArrayHasKey('title', $results[0]);
        $this->assertArrayHasKey('rating', $results[0]);
        $this->assertArrayHasKey('genres', $results[0]);
        $this->assertStringContainsStringIgnoringCase('Inception', $results[0]['title']);
    }

    /**
     * Test getMovieDetail function
     * Input: 'Inception'
     * Expected: Detailed array including directors, top_cast, genres, rating
     */
    public function test_get_movie_detail(): void
    {
        $input = 'Inception';
        $result = $this->movieService->getMovieDetail($input);

        $this->assertNotNull($result);
        $this->assertEquals('Inception', $result['title']);
        $this->assertArrayHasKey('directors', $result);
        $this->assertArrayHasKey('top_cast', $result);
        $this->assertArrayHasKey('genres', $result);
        $this->assertGreaterThan(0, $result['rating']);
    }

    /**
     * Test compareMovies function
     * Input: 'Inception', 'Interstellar'
     * Expected: Comparison array with movie_1, movie_2, and comparison details
     */
    public function test_compare_movies(): void
    {
        $input1 = 'Inception';
        $input2 = 'Interstellar';

        $result = $this->movieService->compareMovies($input1, $input2);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('movie_1', $result);
        $this->assertArrayHasKey('movie_2', $result);
        $this->assertArrayHasKey('comparison', $result);
        $this->assertArrayHasKey('higher_rated', $result['comparison']);
        $this->assertArrayHasKey('shared_genres', $result['comparison']);
    }

    /**
     * Test getMoviesByDecade function
     * Input: '2010s', genre: 'Sci-Fi'
     * Expected: Array with decade key and list of movies within 2010-2019
     */
    public function test_get_movies_by_decade(): void
    {
        $decade = '2010s';
        $result = $this->movieService->getMoviesByDecade($decade, 'Sci-Fi', 7.0, 5);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('decade', $result);
        $this->assertArrayHasKey('movies', $result);
        $this->assertStringContainsString('2010s', $result['decade']);
    }

    /**
     * Test recommendByMood function
     * Input: 'mind_bending'
     * Expected: Array with target_genres containing Sci-Fi / Mystery and movie list
     */
    public function test_recommend_by_mood(): void
    {
        $mood = 'mind_bending';
        $result = $this->movieService->recommendByMood($mood, 5);

        $this->assertIsArray($result);
        $this->assertEquals($mood, $result['mood']);
        $this->assertArrayHasKey('target_genres', $result);
        $this->assertArrayHasKey('recommendations', $result);
    }

    /**
     * Test getSimilarMovies function
     * Input: 'Inception'
     * Expected: Array with reference_movie and similar movies list
     */
    public function test_get_similar_movies(): void
    {
        $input = 'Inception';
        $result = $this->movieService->getSimilarMovies($input, 5);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('reference_movie', $result);
        $this->assertArrayHasKey('movies', $result);
        $this->assertGreaterThan(0, $result['similar_count']);
    }

    /**
     * Test getMovieDiscussions function
     * Input: 'Inception'
     * Expected: Array with movie info, comments_count, and discussions list
     */
    public function test_get_movie_discussions(): void
    {
        $input = 'Inception';
        $result = $this->movieService->getMovieDiscussions($input, 5);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('movie', $result);
        $this->assertArrayHasKey('discussions', $result);
    }
}

