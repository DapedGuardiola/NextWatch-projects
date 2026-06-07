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
        Schema::create('user_tastes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->json('preferred_actors');
            $table->json('preferred_directors');
            $table->json('preferred_era');
            $table->decimal('preferred_normalized_rating');
            $table->decimal('preferred_normalized_popularity');

            $table->integer('persona_version')->default(1);
            $table->integer('activity_since_last_eval')->default(0);
            $table->boolean('persona_ready')->default(false);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tastes');
    }
};
