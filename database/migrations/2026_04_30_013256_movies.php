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
        
        Schema::create("movies", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("tmdb_movie_id")->unique();
            $table->unsignedBigInteger("tmdb_collection_id")->nullable();
            $table->string("title");
            $table->text("overview")->nullable();
            $table->string("poster_path")->nullable();
            $table->string("backdrop_path")->nullable();
            $table->string("trailer_key")->nullable();
            $table->double("popularity")->nullable();
            $table->date("release_date")->nullable();
            $table->integer("runtime")->nullable();
            $table->integer("trailer_size")->nullable();
            $table->string("tagline")->nullable();
            $table->decimal("rating",3,1)->nullable();
            $table->integer("rating_count")->nullable();
            $table->char("original_language",2)->nullable();
            $table->string('status')->default('released');
            $table->boolean('adult')->default(false);
            $table->foreign('tmdb_collection_id')->references('tmdb_collection_id')->on('collections');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
