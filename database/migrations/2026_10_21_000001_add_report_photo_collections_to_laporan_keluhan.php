<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_keluhan', function (Blueprint $table) {
            $table->json('fotos_bukti')->nullable();
            $table->json('foto_penanganan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('laporan_keluhan', function (Blueprint $table) {
            $table->dropColumn(['fotos_bukti', 'foto_penanganan']);
        });
    }
};
