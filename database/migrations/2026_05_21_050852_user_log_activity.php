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
        Schema::create('user_log_activity',function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('tmdb_movie_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('type',['click','search','comment','favorite','watchlist','watch_trailer','click_actor']);
            $table->boolean('is_evaluated')->default(false);
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->foreign('tmdb_movie_id')->references('tmdb_movie_id')->on('movies');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userLogActivity');
    }
};
