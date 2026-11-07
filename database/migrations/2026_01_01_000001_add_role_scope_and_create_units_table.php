<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('operator')->after('password');
            $table->string('uid')->nullable()->after('role');
            $table->string('up3')->nullable()->after('uid');
            $table->string('ulp')->nullable()->after('up3');
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis', ['up3', 'ulp']);
            $table->string('kode')->nullable();
            $table->string('parent_up3')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'uid', 'up3', 'ulp']);
        });
    }
};
