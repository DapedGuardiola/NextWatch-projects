<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable(); // Kolom avatar
            $table->string('gender')->nullable(); // Kolom gender
            $table->date('dob')->nullable();      // Kolom date of birth
            $table->text('bio')->nullable();      // Kolom bio
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus semua kolom sekaligus jika migrasi di-rollback
            $table->dropColumn(['avatar', 'gender', 'dob', 'bio']);
        });
    }
};