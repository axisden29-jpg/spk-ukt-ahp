<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kriterias', function (Blueprint $table) {
            $table->json('keterangan_skala')->nullable()->after('skala_max');
        });
    }

    public function down(): void
    {
        Schema::table('kriterias', function (Blueprint $table) {
            $table->dropColumn('keterangan_skala');
        });
    }
};