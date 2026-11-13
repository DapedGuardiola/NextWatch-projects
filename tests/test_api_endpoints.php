<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Http\Request;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================================================\n";
echo "   NEXTWATCH 30 ENDPOINTS HTTP API VERIFICATION SUITE\n";
echo "========================================================================\n\n";

function callApi($app, string $method, string $uri, array $data = []) {
    $server = [
        'HTTP_ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'application/json',
    ];
    $content = !empty($data) && in_array($method, ['POST', 'PUT', 'DELETE']) ? json_encode($data) : null;
    $parameters = !empty($data) && $method === 'GET' ? $data : [];

    $request = Request::create($uri, $method, $parameters, [], [], $server, $content);
    $response = $app->handle($request);
    $statusCode = $response->getStatusCode();
    $body = json_decode($response->getContent(), true);

    return [
        'status_code' => $statusCode,
        'body' => $body,
        'raw' => $response->getContent()
    ];
}

$testUserId = 4;
$commentIdCreated = null;

$endpointsToTest = [
    // -------------------------------------------------------------------------
    // 1. Movie Endpoints (8 Tools)
    // -------------------------------------------------------------------------
    "GET /api/movies/search" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/movies/search', ['query' => 'Inception', 'limit' => 2]);
        assert($res['status_code'] === 200 && $res['body']['status'] === 'success');
        return "HTTP 200 | Count: " . count($res['body']['data']);
    },
    "GET /api/movies/top-rated" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/movies/top-rated', ['limit' => 2]);
        assert($res['status_code'] === 200 && $res['body']['status'] === 'success');
        return "HTTP 200 | Count: " . count($res['body']['data']);
    },
    "GET /api/movies/compare" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/movies/compare', ['movie1' => 'Inception', 'movie2' => 'Interstellar']);
        assert($res['status_code'] === 200 && isset($res['body']['data']['comparison']));
        return "HTTP 200 | Compared: " . $res['body']['data']['movie_1']['title'] . " vs " . $res['body']['data']['movie_2']['title'];
    },
    "GET /api/movies/decade" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/movies/decade', ['decade' => '2010s', 'limit' => 2]);
        assert($res['status_code'] === 200 && isset($res['body']['data']['movies']));
        return "HTTP 200 | Era: " . $res['body']['data']['decade'];
    },
    "GET /api/movies/mood" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/movies/mood', ['mood' => 'mind_bending', 'limit' => 2]);
        assert($res['status_code'] === 200 && isset($res['body']['data']['recommendations']));
        return "HTTP 200 | Mood: " . $res['body']['data']['mood'];
    },
    "GET /api/movies/{id}/similar" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/movies/Inception/similar', ['limit' => 2]);
        assert($res['status_code'] === 200 && isset($res['body']['data']['movies']));
        return "HTTP 200 | Similar count: " . $res['body']['data']['similar_count'];
    },
    "GET /api/movies/{id}/discussions" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/movies/Inception/discussions');
        assert($res['status_code'] === 200 && isset($res['body']['data']['discussions']));
        return "HTTP 200 | Comments: " . $res['body']['data']['comments_count'];
    },
    "GET /api/movies/{id}" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/movies/Inception');
        assert($res['status_code'] === 200 && $res['body']['data']['title'] === 'Inception');
        return "HTTP 200 | Title: " . $res['body']['data']['title'];
    },

    // -------------------------------------------------------------------------
    // 2. Actor Endpoints (3 Tools)
    // -------------------------------------------------------------------------
    "GET /api/actors/search" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/actors/search', ['name' => 'Leonardo', 'limit' => 2]);
        assert($res['status_code'] === 200 && $res['body']['status'] === 'success');
        return "HTTP 200 | Count: " . count($res['body']['data']);
    },
    "GET /api/actors/{id}/movies" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/actors/Leonardo%20DiCaprio/movies', ['limit' => 2]);
        assert($res['status_code'] === 200 && isset($res['body']['data']['movies']));
        return "HTTP 200 | Actor: " . $res['body']['data']['actor']['name'] . ", Movies: " . count($res['body']['data']['movies']);
    },
    "GET /api/actors/{id}/similar" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/actors/Leonardo%20DiCaprio/similar', ['limit' => 2]);
        assert($res['status_code'] === 200 && isset($res['body']['data']['similar_actors']));
        return "HTTP 200 | Similar actors: " . $res['body']['data']['count'];
    },

    // -------------------------------------------------------------------------
    // 3. Director Endpoints (2 Tools)
    // -------------------------------------------------------------------------
    "GET /api/directors/search" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/directors/search', ['name' => 'Nolan', 'limit' => 1]);
        assert($res['status_code'] === 200 && $res['body']['status'] === 'success');
        return "HTTP 200 | Director: " . $res['body']['data'][0]['name'];
    },
    "GET /api/directors/{id}/movies" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/directors/Christopher%20Nolan/movies', ['limit' => 2]);
        assert($res['status_code'] === 200 && isset($res['body']['data']['movies']));
        return "HTTP 200 | Director: " . $res['body']['data']['director']['name'] . ", Movies: " . count($res['body']['data']['movies']);
    },

    // -------------------------------------------------------------------------
    // 4. Recommendation SPK Endpoints (2 Tools)
    // -------------------------------------------------------------------------
    "GET /api/recommendations/saw" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/recommendations/saw', ['limit' => 2]);
        assert($res['status_code'] === 200 && isset($res['body']['data']['recommendations']));
        return "HTTP 200 | Alg: " . $res['body']['data']['algorithm'];
    },
    "GET /api/recommendations/edas" => function() use ($app) {
        $res = callApi($app, 'GET', '/api/recommendations/edas', ['genre' => 'Action', 'limit' => 2]);
        assert($res['status_code'] === 200 && isset($res['body']['data']['recommendations']));
        return "HTTP 200 | Alg: " . $res['body']['data']['algorithm'];
    },

    // -------------------------------------------------------------------------
    // 5. User Watchlist Endpoints (3 Tools)
    // -------------------------------------------------------------------------
    "POST /api/user/watchlist" => function() use ($app, $testUserId) {
        $res = callApi($app, 'POST', '/api/user/watchlist', ['movie' => 'Inception', 'user_id' => $testUserId]);
        assert($res['status_code'] === 200 && $res['body']['status'] === 'success');
        return "HTTP 200 | Action: " . $res['body']['action'];
    },
    "GET /api/user/watchlist" => function() use ($app, $testUserId) {
        $res = callApi($app, 'GET', '/api/user/watchlist', ['user_id' => $testUserId]);
        assert($res['status_code'] === 200 && isset($res['body']['data']['movies']));
        return "HTTP 200 | Total in Watchlist: " . $res['body']['data']['watchlist_count'];
    },
    "DELETE /api/user/watchlist" => function() use ($app, $testUserId) {
        $res = callApi($app, 'DELETE', '/api/user/watchlist', ['movie' => 'Inception', 'user_id' => $testUserId]);
        assert($res['status_code'] === 200 && $res['body']['status'] === 'success');
        return "HTTP 200 | Action: " . $res['body']['action'];
    },

    // -------------------------------------------------------------------------
    // 6. User Favorites Endpoints (3 Tools)
    // -------------------------------------------------------------------------
    "POST /api/user/favorites" => function() use ($app, $testUserId) {
        $res = callApi($app, 'POST', '/api/user/favorites', ['movie' => 'Interstellar', 'user_id' => $testUserId]);
        assert($res['status_code'] === 200 && $res['body']['status'] === 'success');
        return "HTTP 200 | Action: " . $res['body']['action'];
    },
    "GET /api/user/favorites" => function() use ($app, $testUserId) {
        $res = callApi($app, 'GET', '/api/user/favorites', ['user_id' => $testUserId]);
        assert($res['status_code'] === 200 && isset($res['body']['data']['movies']));
        return "HTTP 200 | Total in Favorites: " . $res['body']['data']['favorites_count'];
    },
    "DELETE /api/user/favorites" => function() use ($app, $testUserId) {
        $res = callApi($app, 'DELETE', '/api/user/favorites', ['movie' => 'Interstellar', 'user_id' => $testUserId]);
        assert($res['status_code'] === 200 && $res['body']['status'] === 'success');
        return "HTTP 200 | Action: " . $res['body']['action'];
    },

    // -------------------------------------------------------------------------
    // 7. User Genre Preferences Endpoints (4 Tools)
    // -------------------------------------------------------------------------
    "POST /api/user/genres" => function() use ($app, $testUserId) {
        $res = callApi($app, 'POST', '/api/user/genres', ['genre' => 'Science Fiction', 'user_id' => $testUserId]);
        assert($res['status_code'] === 200 && $res['body']['status'] === 'success');
        return "HTTP 200 | Added genre: " . $res['body']['genre']['name'];
    },
    "GET /api/user/genres" => function() use ($app, $testUserId) {
        $res = callApi($app, 'GET', '/api/user/genres', ['user_id' => $testUserId]);
        assert($res['status_code'] === 200 && isset($res['body']['data']['genres']));
        return "HTTP 200 | Total genres: " . $res['body']['data']['count'];
    },
    "DELETE /api/user/genres" => function() use ($app, $testUserId) {
        $res = callApi($app, 'DELETE', '/api/user/genres', ['genre' => 'Science Fiction', 'user_id' => $testUserId]);
        assert($res['status_code'] === 200 && $res['body']['status'] === 'success');
        return "HTTP 200 | Action: " . $res['body']['action'];
    },
    "PUT /api/user/genres" => function() use ($app, $testUserId) {
        $res = callApi($app, 'PUT', '/api/user/genres', ['genres' => ['Action', 'Comedy'], 'user_id' => $testUserId]);
        assert($res['status_code'] === 200 && $res['body']['status'] === 'success');
        return "HTTP 200 | Bulk set count: " . $res['body']['count'];
    },

    // -------------------------------------------------------------------------
    // 8. User Actor Preferences Endpoints (3 Tools)
    // -------------------------------------------------------------------------
    "POST /api/user/actors" => function() use ($app, $testUserId) {
        $res = callApi($app, 'POST', '/api/user/actors', ['actor' => 'Leonardo DiCaprio', 'user_id' => $testUserId]);
        assert($res['status_code'] === 200 && $res['body']['status'] === 'success');
        return "HTTP 200 | Added actor: " . $res['body']['actor']['name'];
    },
    "GET /api/user/actors" => function() use ($app, $testUserId) {
        $res = callApi($app, 'GET', '/api/user/actors', ['user_id' => $testUserId]);
        assert($res['status_code'] === 200 && isset($res['body']['data']['actors']));
        return "HTTP 200 | Total favorite actors: " . $res['body']['data']['count'];
    },
    "DELETE /api/user/actors" => function() use ($app, $testUserId) {
        $res = callApi($app, 'DELETE', '/api/user/actors', ['actor' => 'Leonardo DiCaprio', 'user_id' => $testUserId]);
        assert($res['status_code'] === 200 && $res['body']['status'] === 'success');
        return "HTTP 200 | Action: " . $res['body']['action'];
    },

    // -------------------------------------------------------------------------
    // 9. User Comments Endpoints (2 Tools)
    // -------------------------------------------------------------------------
    "POST /api/user/comments" => function() use ($app, $testUserId, &$commentIdCreated) {
        $res = callApi($app, 'POST', '/api/user/comments', [
            'movie' => 'Inception',
            'content' => 'Review API test skripsi!',
            'user_id' => $testUserId
        ]);
        assert($res['status_code'] === 201 && $res['body']['status'] === 'success');
        $commentIdCreated = $res['body']['comment']['id'];
        return "HTTP 201 | Created comment ID: #" . $commentIdCreated;
    },
    "DELETE /api/user/comments" => function() use ($app, $testUserId, &$commentIdCreated) {
        assert(!empty($commentIdCreated), "Comment ID must be set from previous test");
        $res = callApi($app, 'DELETE', '/api/user/comments', [
            'comment_id' => $commentIdCreated,
            'user_id' => $testUserId
        ]);
        assert($res['status_code'] === 200 && $res['body']['status'] === 'success');
        return "HTTP 200 | Deleted comment ID: #" . $commentIdCreated;
    },
];

$passed = 0;
$failed = 0;

foreach ($endpointsToTest as $endpointName => $testFn) {
    try {
        $t0 = microtime(true);
        $detail = $testFn();
        $ms = round((microtime(true) - $t0) * 1000, 2);
        echo "  [PASS] {$endpointName}\n";
        echo "         -> {$detail} ({$ms} ms)\n\n";
        $passed++;
    } catch (\Throwable $e) {
        echo "  [FAIL] {$endpointName}\n";
        echo "         -> Error: " . $e->getMessage() . "\n\n";
        $failed++;
    }
}

echo "========================================================================\n";
echo "RESULT: " . ($passed + $failed) . " Endpoints | {$passed} PASSED | {$failed} FAILED\n";
echo "========================================================================\n";

