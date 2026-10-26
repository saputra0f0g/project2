<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * SIGAP Simplification Migration
     * ─────────────────────────────────────────────────────────────────────────
     * Menyederhanakan skema database agar hanya berfokus pada pelaporan
     * infrastruktur JALAN dengan 2 role: masyarakat & admin_jalan.
     *
     * Perubahan:
     *   1. Tabel users       → ubah enum `peran`
     *   2. Tabel laporan_keluhan → tambah kolom, ubah enum status
     *   3. Drop tabel penugasan_pekerja, bukti_progres_pekerja (jika ada)
     */
    public function up(): void
    {
        // ─── 1. Perbarui enum `peran` di tabel users ────────────────────────
        // Hanya 2 role: masyarakat & admin_jalan
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN peran
            ENUM('masyarakat', 'admin_jalan')
            NOT NULL DEFAULT 'masyarakat'
        ");

        // ─── 2. Perbarui tabel laporan_keluhan ──────────────────────────────
        Schema::table('laporan_keluhan', function (Blueprint $table) {
            // Tambah kolom baru yang relevan untuk laporan jalan
            if (!Schema::hasColumn('laporan_keluhan', 'judul_laporan')) {
                $table->string('judul_laporan', 255)->nullable()->after('id_pelapor');
            }

            if (!Schema::hasColumn('laporan_keluhan', 'jenis_kerusakan')) {
                // Jenis kerusakan jalan yang umum
                $table->enum('jenis_kerusakan', [
                    'jalan_berlubang',
                    'jalan_retak',
                    'jalan_amblas',
                    'jalan_banjir',
                    'lampu_jalan_mati',
                    'trotoar_rusak',
                    'rambu_rusak',
                    'drainase_tersumbat',
                    'lainnya',
                ])->default('jalan_berlubang')->after('judul_laporan');
            }

            if (!Schema::hasColumn('laporan_keluhan', 'tingkat_kerusakan')) {
                $table->enum('tingkat_kerusakan', ['ringan', 'sedang', 'berat'])
                      ->default('sedang')
                      ->after('jenis_kerusakan');
            }

            if (!Schema::hasColumn('laporan_keluhan', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable()->after('alasan_penolakan');
            }

            // Jadikan id_bidang_tujuan nullable (tidak lagi wajib)
            $table->unsignedBigInteger('id_bidang_tujuan')->nullable()->change();

            // Jadikan foto_bukti nullable (bisa dilaporkan tanpa foto)
            $table->string('foto_bukti')->nullable()->change();

            // Jadikan kategori_bidang nullable dengan default 'Jalan'
            if (Schema::hasColumn('laporan_keluhan', 'kategori_bidang')) {
                $table->string('kategori_bidang', 100)->nullable()->default('Jalan')->change();
            }
        });

        // Perbarui enum status laporan → 5 status baru yang sederhana
        DB::statement("
            ALTER TABLE laporan_keluhan
            MODIFY COLUMN status
            ENUM('menunggu', 'diverifikasi', 'dalam_perbaikan', 'selesai', 'ditolak')
            NOT NULL DEFAULT 'menunggu'
        ");

        // Migrasi data status lama → status baru
        DB::statement("UPDATE laporan_keluhan SET status = 'menunggu'       WHERE status IN ('pending', 'dikembalikan')");
        DB::statement("UPDATE laporan_keluhan SET status = 'diverifikasi'   WHERE status IN ('diteruskan', 'menunggu_validasi')");
        DB::statement("UPDATE laporan_keluhan SET status = 'dalam_perbaikan' WHERE status IN ('proses', 'ditunda', 'terkendala', 'revisi')");
        // 'selesai' dan 'ditolak' tetap sama

        // Isi judul_laporan dari deskripsi_laporan jika kosong
        DB::statement("
            UPDATE laporan_keluhan
            SET judul_laporan = SUBSTRING_INDEX(deskripsi_laporan, ' — ', 1)
            WHERE judul_laporan IS NULL
        ");

        // Isi jenis_kerusakan = 'lainnya' jika belum ada
        DB::statement("
            UPDATE laporan_keluhan
            SET jenis_kerusakan = 'lainnya'
            WHERE jenis_kerusakan IS NULL
        ");

        // ─── 3. Drop tabel yang tidak lagi dipakai ──────────────────────────
        Schema::dropIfExists('bukti_progres_pekerja');
        Schema::dropIfExists('penugasan_pekerja');
    }

    /**
     * Rollback — kembalikan ke kondisi sebelumnya.
     */
    public function down(): void
    {
        // Kembalikan enum peran
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN peran
            ENUM('masyarakat', 'admin_bidang', 'admin_universal', 'pekerja_bidang')
            NOT NULL DEFAULT 'masyarakat'
        ");

        // Kembalikan enum status laporan
        DB::statement("
            ALTER TABLE laporan_keluhan
            MODIFY COLUMN status
            ENUM('pending','diteruskan','proses','selesai','ditolak','terkendala','dikembalikan','ditunda','menunggu_validasi','revisi')
            NOT NULL DEFAULT 'pending'
        ");

        // Hapus kolom baru yang ditambahkan
        Schema::table('laporan_keluhan', function (Blueprint $table) {
            $table->dropColumn([
                'judul_laporan',
                'jenis_kerusakan',
                'tingkat_kerusakan',
                'catatan_admin',
            ]);
        });

        // CATATAN: Tabel penugasan_pekerja & bukti_progres_pekerja tidak
        // dikembalikan di rollback ini karena data tidak bisa dipulihkan.
        // Restore manual dari backup jika diperlukan.
    }
};
