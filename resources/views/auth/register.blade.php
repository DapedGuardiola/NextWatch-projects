<?php
use App\Models\Movie;
use App\Services\LandingService;
use App\Services\ActorService;

$landingService = app(LandingService::class);
$actorService   = app(ActorService::class);

$popularMovie  = Movie::orderBy('popularity', 'desc')->first();
$moviesByGenre = $landingService->getMoviesByGenre();
$actors        = $actorService->getActor();
$openModal     = 'register';
?>

@include('landing', compact('popularMovie', 'moviesByGenre', 'actors', 'openModal'))