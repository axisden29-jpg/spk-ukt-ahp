<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matriks_ahps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kriteria_baris_id')->constrained('kriterias')->cascadeOnDelete();
            $table->foreignId('kriteria_kolom_id')->constrained('kriterias')->cascadeOnDelete();
            $table->decimal('nilai', 10, 6);
            $table->string('periode', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matriks_ahps');
    }
};