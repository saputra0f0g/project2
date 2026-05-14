<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutentikasiController;
use App\Http\Controllers\AdminUniversal\BerandaController;
use App\Http\Controllers\AdminUniversal\BidangController;
use App\Http\Controllers\AdminUniversal\PenggunaController;
use App\Http\Controllers\AdminUniversal\LaporanController as LaporanUniversal;
use App\Http\Controllers\AdminUniversal\StatistikController;
use App\Http\Controllers\AdminUniversal\ProfilController;

use App\Http\Controllers\AdminBidang\BerandaController as BerandaBidang;
use App\Http\Controllers\AdminBidang\LaporanController as LaporanBidangController;

use App\Http\Controllers\Pekerja\TugasController as TugasPekerja;


Route::get('/', [AutentikasiController::class, 'tampilLogin'])->name('login');
Route::post('/login/proses', [AutentikasiController::class, 'prosesLogin'])->name('login.proses');


Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AutentikasiController::class, 'logout'])->name('logout');

    // ==========================================
    // RUTE ADMIN UNIVERSAL (PUSAT)
    // ==========================================
    Route::prefix('admin-universal')->name('admin_universal.')->group(function () {

        // Beranda
        Route::get('/beranda', [BerandaController::class, 'indeks'])->name('beranda');
        Route::get('/bantuan', [BerandaController::class, 'bantuan'])->name('bantuan');

        // Kelola Bidang
        Route::get('/bidang', [BidangController::class, 'index'])->name('bidang');
        Route::get('/bidang/edit/{id}', [BidangController::class, 'edit'])->name('bidang.edit');
        Route::post('/bidang/simpan', [BidangController::class, 'simpan'])->name('bidang.simpan');
        Route::put('/bidang/perbarui/{id}', [BidangController::class, 'perbarui'])->name('bidang.perbarui');
        Route::delete('/bidang/hapus/{id}', [BidangController::class, 'hapus'])->name('bidang.hapus');
        Route::post('/bidang/simpan', [BidangController::class, 'simpan'])->name('bidang.simpan');
        Route::put('/bidang/perbarui/{id}', [BidangController::class, 'perbarui'])->name('bidang.perbarui');
        Route::delete('/bidang/hapus/{id}', [BidangController::class, 'hapus'])->name('bidang.hapus');
        Route::get('/bidang/export', [BidangController::class, 'exportCsv'])->name('bidang.export'); // Perbaikan nama route

        // Beranda & Peta Pusat
        Route::get('/peta-wilayah', [BerandaController::class, 'peta'])->name('peta');

        // Kelola Pengguna
        Route::get('/pengguna', [PenggunaController::class, 'indeks'])->name('pengguna');
        Route::post('/pengguna/simpan', [PenggunaController::class, 'simpan'])->name('pengguna.simpan');
        Route::delete('/pengguna/hapus/{id}', [PenggunaController::class, 'hapus'])->name('pengguna.hapus');

        // Kelola Laporan
        Route::get('/laporan', [LaporanUniversal::class, 'indeks'])->name('laporan');
        Route::post('/laporan/simpan-manual', [LaporanUniversal::class, 'simpan'])->name('laporan.simpan');

        // Detail Laporan & Aksi Status
        Route::get('/laporan/detail/{id}', [LaporanUniversal::class, 'detail'])->name('laporan.detail');
        Route::post('/laporan/update-status/{id}', [LaporanUniversal::class, 'updateStatus'])->name('laporan.update_status');

        // Ekspor & Cetak
        Route::get('/laporan/cetak-detail/{id}', [LaporanUniversal::class, 'cetakDetailPdf'])->name('laporan.cetak_detail');
        Route::get('/laporan/ekspor-csv', [LaporanUniversal::class, 'ekspor'])->name('laporan.ekspor');
        Route::get('/laporan/ekspor-pdf', [LaporanUniversal::class, 'eksporPdf'])->name('laporan.ekspor_pdf');

        // (Abaikan jika disposisi dan tolak sudah digabung ke updateStatus, biarkan jika masih terpisah)
        Route::post('/laporan/disposisi/{id}', [LaporanUniversal::class, 'disposisi'])->name('laporan.disposisi');
        Route::post('/laporan/tolak/{id}', [LaporanUniversal::class, 'tolak'])->name('laporan.tolak');

        // ==========================================
        // Profil Admin Universal
        // ==========================================
        Route::get('/profil', [ProfilController::class, 'indeks'])->name('profil');
        Route::post('/profil/foto', [ProfilController::class, 'updateFoto'])->name('profil.foto');
        Route::delete('/profil/log', [ProfilController::class, 'hapusLog'])->name('profil.log.hapus');

        // 2 Rute Utama untuk fitur baru kita:
        Route::put('/profil/update', [ProfilController::class, 'updateProfil'])->name('profil.update');
        Route::delete('/profil/foto/hapus', [ProfilController::class, 'hapusFoto'])->name('profil.foto.hapus');

        // Sistem Notifikasi
        Route::get('/notifikasi/baca-semua', [\App\Http\Controllers\AdminUniversal\NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca_semua');
        Route::get('/notifikasi/{id}/klik', [\App\Http\Controllers\AdminUniversal\NotifikasiController::class, 'klik'])->name('notifikasi.klik');
        Route::delete('/notifikasi/{id}/hapus', [\App\Http\Controllers\AdminUniversal\NotifikasiController::class, 'hapus'])->name('notifikasi.hapus');
        Route::delete('/notifikasi/hapus-semua', [\App\Http\Controllers\AdminUniversal\NotifikasiController::class, 'hapusSemua'])->name('notifikasi.hapus_semua');
        // Tambahkan di web.php (kelompok rute admin_universal)
        Route::get('/statistik', [StatistikController::class, 'indeks'])->name('statistik');
    });


    // ==========================================
    // RUTE ADMIN BIDANG
    // ==========================================
    Route::prefix('admin-bidang')->name('admin_bidang.')->group(function () {
        Route::get('/beranda', [BerandaBidang::class, 'indeks'])->name('beranda');

        Route::get('/laporan', [LaporanBidangController::class, 'indeks'])->name('laporan');
        Route::get('/laporan/detail/{id}', [LaporanBidangController::class, 'detail'])->name('laporan.detail');
        Route::post('/laporan/tugaskan/{id}', [LaporanBidangController::class, 'tugaskan'])->name('laporan.tugaskan');

        Route::get('/laporan/ekspor-excel', [LaporanBidangController::class, 'eksporExcel'])->name('laporan.ekspor_excel');
        Route::get('/laporan/ekspor-pdf', [LaporanBidangController::class, 'eksporPdf'])->name('laporan.ekspor_pdf');
    });


    // ==========================================
    // RUTE PEKERJA LAPANGAN / UPTD
    // ==========================================


    //==============================================================================================================
    // Rute Uji Coba Notifikasi (Bisa dihapus nanti jika sisi Masyarakat sudah dibuat)
    Route::get('/test-notif', function() {
        // Cari satu user yang jabatannya admin_universal
        $admin = \App\Models\User::where('peran', 'admin_universal')->first();

        if ($admin) {
            \App\Models\Notifikasi::create([
                'user_id' => $admin->id,
                'judul'   => 'Laporan Baru: Uji Coba Sistem 🔴',
                'pesan'   => 'Ini adalah simulasi laporan masuk dari masyarakat untuk mengetes fitur notifikasi.',
                'tautan'  => route('admin_universal.beranda') // Arahkan ke beranda dulu
            ]);
            return "Simulasi berhasil! 1 Notifikasi baru telah dikirim ke Admin Universal. Silakan kembali ke Dashboard admin lalu Refresh halamannya.";
        }

        return "Gagal: Akun dengan peran admin_universal tidak ditemukan di database."; // testing = http://127.0.0.1:8000/test-notif
    });
});
