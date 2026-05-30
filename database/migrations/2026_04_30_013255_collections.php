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
        Schema::create("collections",function(Blueprint$table){
            $table->bigIncrements("id");
            $table->unsignedBigInteger("tmdb_collection_id")->unique();
            $table->string("name");
            $table->text("overview")->nullable();
            $table->string("poster_path")->nullable();
            $table->string("backdrop_path")->nullable();
            $table->char("original_language",2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
