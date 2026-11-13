<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Api\UserActionService;

class UserActionServiceTest extends TestCase
{
    protected UserActionService $userActionService;
    protected int $testUserId = 1;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'mysql']);
        $this->userActionService = app(UserActionService::class);
    }

    /**
     * Test Watchlist Actions: Add, Get, Remove
     * Input: movieIdentifier = 'Inception'
     * Expected: Added to watchlist, found in list, and successfully removed
     */
    public function test_watchlist_lifecycle(): void
    {
        $movie = 'Inception';

        // 1. Add
        $addRes = $this->userActionService->addToWatchlist($movie, $this->testUserId);
        $this->assertEquals('success', $addRes['status']);
        $this->assertEquals('added_to_watchlist', $addRes['action']);

        // 2. Get
        $getRes = $this->userActionService->getUserWatchlist($this->testUserId);
        $this->assertGreaterThan(0, $getRes['watchlist_count']);
        $titles = array_column($getRes['movies'], 'title');
        $this->assertContains('Inception', $titles);

        // 3. Remove
        $remRes = $this->userActionService->removeFromWatchlist($movie, $this->testUserId);
        $this->assertEquals('success', $remRes['status']);
        $this->assertEquals('removed_from_watchlist', $remRes['action']);
    }

    /**
     * Test Favorites Actions: Add, Get, Remove
     * Input: movieIdentifier = 'Interstellar'
     * Expected: Added to favorites, found in list, and successfully removed
     */
    public function test_favorites_lifecycle(): void
    {
        $movie = 'Interstellar';

        // 1. Add
        $addRes = $this->userActionService->addToFavorites($movie, $this->testUserId);
        $this->assertEquals('success', $addRes['status']);
        $this->assertEquals('added_to_favorites', $addRes['action']);

        // 2. Get
        $getRes = $this->userActionService->getUserFavorites($this->testUserId);
        $this->assertGreaterThan(0, $getRes['favorites_count']);

        // 3. Remove
        $remRes = $this->userActionService->removeFromFavorites($movie, $this->testUserId);
        $this->assertEquals('success', $remRes['status']);
        $this->assertEquals('removed_from_favorites', $remRes['action']);
    }

    /**
     * Test Genre Preference Actions: Add, Get, Remove, Set
     * Input: genre = 'Sci-Fi'
     * Expected: Added, fetched, removed, bulk set
     */
    public function test_genre_preferences_lifecycle(): void
    {
        $genre = 'Science Fiction';

        // 1. Add
        $addRes = $this->userActionService->addFavoriteGenre($genre, $this->testUserId);
        $this->assertEquals('success', $addRes['status']);

        // 2. Get
        $getRes = $this->userActionService->getUserFavoriteGenres($this->testUserId);
        $this->assertGreaterThan(0, $getRes['count']);

        // 3. Remove
        $remRes = $this->userActionService->removeFavoriteGenre($genre, $this->testUserId);
        $this->assertEquals('success', $remRes['status']);

        // 4. Set bulk
        $setRes = $this->userActionService->setFavoriteGenres(['Action', 'Comedy', 'Adventure'], $this->testUserId);
        $this->assertEquals('success', $setRes['status']);
        $this->assertCount(3, $setRes['genres']);
    }

    /**
     * Test Actor Preference Actions: Add, Get, Remove
     * Input: actor = 'Leonardo DiCaprio'
     * Expected: Added, fetched, removed
     */
    public function test_actor_preferences_lifecycle(): void
    {
        $actor = 'Leonardo DiCaprio';

        // 1. Add
        $addRes = $this->userActionService->addFavoriteActor($actor, $this->testUserId);
        $this->assertEquals('success', $addRes['status']);

        // 2. Get
        $getRes = $this->userActionService->getUserFavoriteActors($this->testUserId);
        $this->assertGreaterThan(0, $getRes['count']);

        // 3. Remove
        $remRes = $this->userActionService->removeFavoriteActor($actor, $this->testUserId);
        $this->assertEquals('success', $remRes['status']);
    }

    /**
     * Test Comment Actions: Post, Delete
     * Input: movie = 'Inception', content = 'Film masterpiece favorit saya!'
     * Expected: Comment created with ID, and then deleted
     */
    public function test_comment_lifecycle(): void
    {
        $movie = 'Inception';
        $content = 'Film masterpiece favorit saya dari masa ke masa!';

        // 1. Post
        $postRes = $this->userActionService->postMovieComment($movie, $content, $this->testUserId);
        $this->assertEquals('success', $postRes['status']);
        $this->assertArrayHasKey('id', $postRes['comment']);
        $commentId = $postRes['comment']['id'];

        // 2. Delete
        $delRes = $this->userActionService->deleteMovieComment($commentId, $this->testUserId);
        $this->assertEquals('success', $delRes['status']);
        $this->assertEquals('deleted_comment', $delRes['action']);
    }
}

