<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {

            // rename body -> content
            $table->renameColumn('body', 'content');

            // self relation reply
            $table->unsignedBigInteger('reply_id')
                ->nullable()
                ->after('movie_id');

            // tagged user
            $table->unsignedBigInteger('tagged_user_id')
                ->nullable()
                ->after('reply_id');

            // foreign keys
            $table->foreign('reply_id')
                ->references('id')
                ->on('comments')
                ->onDelete('cascade');

            $table->foreign('tagged_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {

            $table->dropForeign(['reply_id']);
            $table->dropForeign(['tagged_user_id']);

            $table->dropColumn([
                'reply_id',
                'tagged_user_id'
            ]);

            $table->renameColumn('content', 'body');
        });
    }
};