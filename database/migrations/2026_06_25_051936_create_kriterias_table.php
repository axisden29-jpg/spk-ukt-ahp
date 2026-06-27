<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('kode', 60)->unique();
            $table->text('deskripsi')->nullable();
            $table->tinyInteger('skala_min')->default(1);
            $table->tinyInteger('skala_max')->default(5);
            $table->smallInteger('urutan')->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriterias');
    }
};