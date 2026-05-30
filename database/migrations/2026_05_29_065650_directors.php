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
        Schema::create("directors",function(Blueprint $table){
            $table->increments("id");
            $table->unsignedBigInteger("tmdb_director_id")->unique();
            $table->string("name");
            $table->string("image_path")->nullable();
            $table->string("place_of_birth")->nullable();
            $table->double("popularity")->nullable();
            $table->text("biography")->nullable();
            $table->date("birthday")->nullable();
            $table->date("deathday")->nullable();
            $table->integer("gender")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directors');
    }
};
