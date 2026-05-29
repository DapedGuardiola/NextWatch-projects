<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('movie_id');
            $table->boolean('is_persona')->nullable()->default(false);
            $table->timestamps();

            $table->unique([
                'user_id',
                'movie_id'
            ]);

            $table->foreign('movie_id')->references('tmdb_movie_id')->on('movies');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};