<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================================================\n";
echo "   NEXTWATCH FULL API SERVICE TEST SUITE (GET & ACTION MUTATIONS)\n";
echo "========================================================================\n\n";

$movieService = app(\App\Services\Api\MovieService::class);
$actorService = app(\App\Services\Api\ActorService::class);
$directorService = app(\App\Services\Api\DirectorService::class);
$recService = app(\App\Services\Api\RecommendationService::class);
$userActionService = app(\App\Services\Api\UserActionService::class);

$testUserId = 4;

$tests = [
    // -------------------------------------------------------------------------
    // 1. MovieService Tests (GET)
    // -------------------------------------------------------------------------
    "MovieService::searchMovies" => [
        'input' => "['query' => 'Inception', 'limit' => 2]",
        'fn' => function() use ($movieService) {
            $res = $movieService->searchMovies(['query' => 'Inception', 'limit' => 2]);
            assert(!empty($res) && isset($res[0]['title']), "Expected at least 1 movie in results");
            return "Found " . count($res) . " movies (First: {$res[0]['title']}, Rating: {$res[0]['rating']})";
        }
    ],
    "MovieService::getMovieDetail" => [
        'input' => "'Inception'",
        'fn' => function() use ($movieService) {
            $res = $movieService->getMovieDetail('Inception');
            assert($res !== null && $res['title'] === 'Inception', "Expected exact title 'Inception'");
            return "Title: {$res['title']}, Genres: " . implode(', ', $res['genres']) . ", Cast: " . count($res['top_cast']);
        }
    ],
    "MovieService::compareMovies" => [
        'input' => "'Inception', 'Interstellar'",
        'fn' => function() use ($movieService) {
            $res = $movieService->compareMovies('Inception', 'Interstellar');
            assert(isset($res['comparison']), "Expected comparison object");
            return "{$res['movie_1']['title']} vs {$res['movie_2']['title']} (Winner: {$res['comparison']['higher_rated']})";
        }
    ],
    "MovieService::getMoviesByDecade" => [
        'input' => "'2010s', genre: null, minRating: 7.5, limit: 3",
        'fn' => function() use ($movieService) {
            $res = $movieService->getMoviesByDecade('2010s', null, 7.5, 3);
            assert(isset($res['decade']) && !empty($res['movies']), "Expected 2010s movies");
            return "Decade: {$res['decade']}, Count: {$res['count']}";
        }
    ],
    "MovieService::recommendByMood" => [
        'input' => "mood: 'mind_bending', limit: 3",
        'fn' => function() use ($movieService) {
            $res = $movieService->recommendByMood('mind_bending', 3);
            assert(!empty($res['recommendations']), "Expected recommendations for mind_bending");
            return "Target Genres: " . implode(', ', $res['target_genres']) . ", Count: " . count($res['recommendations']);
        }
    ],
    "MovieService::getSimilarMovies" => [
        'input' => "'Inception', limit: 3",
        'fn' => function() use ($movieService) {
            $res = $movieService->getSimilarMovies('Inception', 3);
            assert(!empty($res['movies']), "Expected similar movies");
            return "Reference: {$res['reference_movie']['title']}, Similar Count: " . count($res['movies']);
        }
    ],
    "MovieService::getMovieDiscussions" => [
        'input' => "'Inception', limit: 3",
        'fn' => function() use ($movieService) {
            $res = $movieService->getMovieDiscussions('Inception', 3);
            assert(isset($res['discussions']), "Expected discussions structure");
            return "Movie: {$res['movie']['title']}, Discussions: {$res['comments_count']}";
        }
    ],

    // -------------------------------------------------------------------------
    // 2. ActorService Tests (GET)
    // -------------------------------------------------------------------------
    "ActorService::findActorByName" => [
        'input' => "'Leonardo DiCaprio', limit: 1",
        'fn' => function() use ($actorService) {
            $res = $actorService->findActorByName('Leonardo DiCaprio', 1);
            assert(!empty($res) && $res[0]['name'] === 'Leonardo DiCaprio', "Expected actor Leonardo DiCaprio");
            return "Actor: {$res[0]['name']}, Known for: " . count($res[0]['known_for_movies']) . " films";
        }
    ],
    "ActorService::getMoviesByActor" => [
        'input' => "'Leonardo DiCaprio', limit: 3",
        'fn' => function() use ($actorService) {
            $res = $actorService->getMoviesByActor('Leonardo DiCaprio', 3);
            assert(!empty($res['movies']), "Expected starred movies list");
            return "Actor: {$res['actor']['name']}, Movies Count: " . count($res['movies']);
        }
    ],
    "ActorService::getSimilarActors" => [
        'input' => "'Leonardo DiCaprio', limit: 3",
        'fn' => function() use ($actorService) {
            $res = $actorService->getSimilarActors('Leonardo DiCaprio', 3);
            assert(isset($res['similar_actors']), "Expected similar actors array");
            return "Reference: {$res['reference_actor']['name']}, Similar Found: " . count($res['similar_actors']);
        }
    ],

    // -------------------------------------------------------------------------
    // 3. DirectorService Tests (GET)
    // -------------------------------------------------------------------------
    "DirectorService::findDirectorByName" => [
        'input' => "'Christopher Nolan', limit: 1",
        'fn' => function() use ($directorService) {
            $res = $directorService->findDirectorByName('Christopher Nolan', 1);
            assert(!empty($res) && $res[0]['name'] === 'Christopher Nolan', "Expected director Christopher Nolan");
            return "Director: {$res[0]['name']}, Known for: " . count($res[0]['known_for_movies']) . " films";
        }
    ],
    "DirectorService::getMoviesByDirector" => [
        'input' => "'Christopher Nolan', limit: 3",
        'fn' => function() use ($directorService) {
            $res = $directorService->getMoviesByDirector('Christopher Nolan', 3);
            assert(!empty($res['movies']), "Expected directed movies list");
            return "Director: {$res['director']['name']}, Movies Count: " . count($res['movies']);
        }
    ],

    // -------------------------------------------------------------------------
    // 4. RecommendationService Tests (SPK Algorithms)
    // -------------------------------------------------------------------------
    "RecommendationService::getPersonalizedSaw" => [
        'input' => "userVector: null, limit: 3",
        'fn' => function() use ($recService) {
            $res = $recService->getPersonalizedSaw(null, 3);
            assert(!empty($res['recommendations']), "Expected ranked movies from SAW");
            return "Algorithm: {$res['algorithm']}, Results: {$res['count']}";
        }
    ],
    "RecommendationService::getAllTimeBestEdas" => [
        'input' => "genre: 'Action', limit: 3",
        'fn' => function() use ($recService) {
            $res = $recService->getAllTimeBestEdas('Action', 3);
            assert(!empty($res['recommendations']), "Expected ranked movies from EDAS");
            return "Algorithm: {$res['algorithm']}, Results: {$res['count']}";
        }
    ],

    // -------------------------------------------------------------------------
    // 5. UserActionService Tests (SET / ADD / REMOVE MUTATIONS)
    // -------------------------------------------------------------------------
    "UserActionService::Watchlist (Add -> Get -> Remove)" => [
        'input' => "movie: 'Inception', userId: 1",
        'fn' => function() use ($userActionService, $testUserId) {
            $add = $userActionService->addToWatchlist('Inception', $testUserId);
            assert($add['status'] === 'success', "Add to watchlist failed");
            $get = $userActionService->getUserWatchlist($testUserId);
            assert($get['watchlist_count'] > 0, "Watchlist should not be empty");
            $rem = $userActionService->removeFromWatchlist('Inception', $testUserId);
            assert($rem['status'] === 'success', "Remove from watchlist failed");
            return "Watchlist lifecycle verified (Added, Checked: {$get['watchlist_count']} items, Removed)";
        }
    ],
    "UserActionService::Favorites (Add -> Get -> Remove)" => [
        'input' => "movie: 'Interstellar', userId: 1",
        'fn' => function() use ($userActionService, $testUserId) {
            $add = $userActionService->addToFavorites('Interstellar', $testUserId);
            assert($add['status'] === 'success', "Add to favorites failed");
            $get = $userActionService->getUserFavorites($testUserId);
            assert($get['favorites_count'] > 0, "Favorites should not be empty");
            $rem = $userActionService->removeFromFavorites('Interstellar', $testUserId);
            assert($rem['status'] === 'success', "Remove from favorites failed");
            return "Favorites lifecycle verified (Added, Checked: {$get['favorites_count']} items, Removed)";
        }
    ],
    "UserActionService::Genres (Add -> Get -> Remove -> Set)" => [
        'input' => "genre: 'Science Fiction', bulk: ['Action', 'Comedy'], userId: 1",
        'fn' => function() use ($userActionService, $testUserId) {
            $add = $userActionService->addFavoriteGenre('Science Fiction', $testUserId);
            assert($add['status'] === 'success', "Add genre failed");
            $get = $userActionService->getUserFavoriteGenres($testUserId);
            assert($get['count'] > 0, "Genres should not be empty");
            $rem = $userActionService->removeFavoriteGenre('Science Fiction', $testUserId);
            assert($rem['status'] === 'success', "Remove genre failed");
            $set = $userActionService->setFavoriteGenres(['Action', 'Comedy'], $testUserId);
            assert($set['status'] === 'success' && $set['count'] === 2, "Bulk set genres failed");
            return "Genre lifecycle verified (Added, Removed, Bulk Set: " . implode(', ', $set['genres']) . ")";
        }
    ],
    "UserActionService::Actors (Add -> Get -> Remove)" => [
        'input' => "actor: 'Leonardo DiCaprio', userId: 1",
        'fn' => function() use ($userActionService, $testUserId) {
            $add = $userActionService->addFavoriteActor('Leonardo DiCaprio', $testUserId);
            assert($add['status'] === 'success', "Add actor failed");
            $get = $userActionService->getUserFavoriteActors($testUserId);
            assert($get['count'] > 0, "Favorite actors should not be empty");
            $rem = $userActionService->removeFavoriteActor('Leonardo DiCaprio', $testUserId);
            assert($rem['status'] === 'success', "Remove actor failed");
            return "Actor preference verified (Added, Checked: {$get['count']} actors, Removed)";
        }
    ],
    "UserActionService::Comments (Post -> Delete)" => [
        'input' => "movie: 'Inception', content: 'Ulasan test skripsi...', userId: 1",
        'fn' => function() use ($userActionService, $testUserId) {
            $post = $userActionService->postMovieComment('Inception', 'Ulasan test skripsi sistem cerdas NextWatch!', $testUserId);
            assert($post['status'] === 'success' && isset($post['comment']['id']), "Post comment failed");
            $commentId = $post['comment']['id'];
            $del = $userActionService->deleteMovieComment($commentId, $testUserId);
            assert($del['status'] === 'success', "Delete comment failed");
            return "Comment lifecycle verified (Created ID #{$commentId}, Deleted successfully)";
        }
    ],
];

$passed = 0;
$failed = 0;

foreach ($tests as $name => $item) {
    try {
        $t0 = microtime(true);
        $detail = $item['fn']();
        $ms = round((microtime(true) - $t0) * 1000, 2);
        echo "  [PASS] {$name}\n";
        echo "         Input    : {$item['input']}\n";
        echo "         Expected : Passed assertions & proper response structure\n";
        echo "         Result   : {$detail} ({$ms} ms)\n\n";
        $passed++;
    } catch (\Throwable $e) {
        echo "  [FAIL] {$name}\n";
        echo "         Input    : {$item['input']}\n";
        echo "         Error    : " . $e->getMessage() . "\n\n";
        $failed++;
    }
}

echo "========================================================================\n";
echo "RESULT: " . ($passed + $failed) . " Total | {$passed} PASSED | {$failed} FAILED\n";
echo "========================================================================\n";
