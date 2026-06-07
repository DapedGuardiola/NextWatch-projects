<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('comment_id')->constrained()->cascadeOnDelete();
            $table->string('reason')->default('inappropriate'); // inappropriate | spam | hate_speech | other
            $table->text('note')->nullable();
            $table->timestamps();
 
            $table->unique(['user_id', 'comment_id']); // satu user hanya bisa report sekali per komentar
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('comment_reports');
    }
};