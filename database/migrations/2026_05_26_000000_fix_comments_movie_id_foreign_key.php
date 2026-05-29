<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['movie_id']);
        });

        // Add new foreign key that references tmdb_movie_id
        DB::statement('
            ALTER TABLE comments 
            ADD CONSTRAINT comments_movie_id_foreign 
            FOREIGN KEY (movie_id) 
            REFERENCES movies(tmdb_movie_id) 
            ON DELETE CASCADE
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['movie_id']);
        });

        // Restore the original foreign key
        DB::statement('
            ALTER TABLE comments 
            ADD CONSTRAINT comments_movie_id_foreign 
            FOREIGN KEY (movie_id) 
            REFERENCES movies(id) 
            ON DELETE CASCADE
        ');
    }
};
