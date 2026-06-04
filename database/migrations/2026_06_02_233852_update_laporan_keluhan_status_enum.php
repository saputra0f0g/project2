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
        // Ubah ENUM status laporan_keluhan ke versi final yang komprehensif
        DB::statement("ALTER TABLE laporan_keluhan MODIFY COLUMN status ENUM('pending', 'diteruskan', 'dikembalikan', 'menunggu_validasi', 'ditolak', 'ditunda', 'proses', 'terkendala', 'revisi', 'selesai') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke ENUM status sebelum migration ini
        DB::statement("ALTER TABLE laporan_keluhan MODIFY COLUMN status ENUM('pending', 'diteruskan', 'proses', 'selesai', 'ditolak', 'terkendala', 'dikembalikan') DEFAULT 'pending'");
    }
};
