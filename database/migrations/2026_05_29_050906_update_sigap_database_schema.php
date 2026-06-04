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
        // 1. Modifikasi Tabel laporan_keluhan
        // Menggunakan raw DB statement untuk memodifikasi ENUM untuk menghindari issue doctrine/dbal
        DB::statement("ALTER TABLE laporan_keluhan MODIFY COLUMN status ENUM('pending', 'diteruskan', 'proses', 'selesai', 'ditolak', 'terkendala', 'dikembalikan') DEFAULT 'pending'");

        // 2. Modifikasi Tabel penugasan_pekerja
        // Menambahkan kolom-kolom baru
        Schema::table('penugasan_pekerja', function (Blueprint $table) {
            $table->enum('status_validitas_survei', ['valid', 'tidak_valid'])->nullable()->after('id_pekerja');
            $table->text('deskripsi_temuan_survei')->nullable()->after('status_validitas_survei');
            $table->text('rekomendasi_survei')->nullable()->after('deskripsi_temuan_survei');
            $table->text('alasan_penundaan')->nullable()->after('instruksi_tambahan');
            $table->text('catatan_revisi')->nullable()->after('alasan_penundaan');
        });

        // Mengubah nilai ENUM untuk kolom status_tugas
        DB::statement("ALTER TABLE penugasan_pekerja MODIFY COLUMN status_tugas ENUM('ditugaskan', 'survei_selesai', 'ditunda', 'dikerjakan', 'menunggu_review', 'revisi', 'selesai', 'terkendala') DEFAULT 'ditugaskan'");

        // 3. Buat Tabel Baru bukti_progres_pekerja
        Schema::create('bukti_progres_pekerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_penugasan');
            $table->string('file_path');
            $table->enum('tipe_file', ['foto', 'video'])->default('foto');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Aturan Foreign Key (ON DELETE CASCADE)
            $table->foreign('id_penugasan')
                  ->references('id')->on('penugasan_pekerja')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Hapus Tabel Baru bukti_progres_pekerja
        Schema::dropIfExists('bukti_progres_pekerja');

        // 2. Rollback Tabel penugasan_pekerja (Hapus kolom yang baru ditambahkan)
        Schema::table('penugasan_pekerja', function (Blueprint $table) {
            $table->dropColumn([
                'status_validitas_survei',
                'deskripsi_temuan_survei',
                'rekomendasi_survei',
                'alasan_penundaan',
                'catatan_revisi'
            ]);
        });
        
        // Kembalikan ENUM status_tugas ke nilai sebelumnya
        // CATATAN: Silakan sesuaikan ENUM di bawah ini dengan nilai yang ada SEBELUM migration ini dijalankan
        DB::statement("ALTER TABLE penugasan_pekerja MODIFY COLUMN status_tugas ENUM('ditugaskan', 'dikerjakan', 'selesai') DEFAULT 'ditugaskan'");

        // 3. Rollback Tabel laporan_keluhan
        // Kembalikan ENUM status ke nilai sebelumnya
        // CATATAN: Silakan sesuaikan ENUM di bawah ini dengan nilai yang ada SEBELUM migration ini dijalankan
        DB::statement("ALTER TABLE laporan_keluhan MODIFY COLUMN status ENUM('pending', 'proses', 'selesai') DEFAULT 'pending'");
    }
};
