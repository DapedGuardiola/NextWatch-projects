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
        Schema::create("normalized_movie", function (Blueprint $table) {
            $table->bigIncrements("id")->unique();
            $table->unsignedBigInteger("tmdb_movie_id");
            $table->double("n_rating")->nullable();
            $table->double("n_popularity")->nullable();
            $table->double("n_rating_count")->nullable();
            $table->foreign('tmdb_movie_id')->references('tmdb_movie_id')->on('movies');
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('normalized_movie');
    }
};
