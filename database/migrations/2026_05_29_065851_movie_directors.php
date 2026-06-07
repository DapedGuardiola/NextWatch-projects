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
        Schema::create("movie_directors", function (Blueprint $table) {
            $table->bigIncrements("id")->unique();
            $table->unsignedBigInteger("tmdb_movie_id");
            $table->unsignedBigInteger("tmdb_director_id");
            $table->foreign('tmdb_movie_id')->references('tmdb_movie_id')->on('movies');
            $table->foreign('tmdb_director_id')->references('tmdb_director_id')->on('directors');
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_directors');
    }
};
