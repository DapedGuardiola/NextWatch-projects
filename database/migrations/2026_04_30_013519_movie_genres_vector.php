<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("movie_genre_vector",function(Blueprint $table){
            $table->increments("id")->unique();
            $table->unsignedBigInteger("tmdb_movie_id");
            $table->json("vector");
            $table->foreign('tmdb_movie_id')->references('tmdb_movie_id')->on('movies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_genres_vector');
    }
};
