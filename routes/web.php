<?php

use App\Http\Controllers\AdminJalan\BerandaController;
use App\Http\Controllers\AdminJalan\LaporanController as LaporanAdminJalan;
use App\Http\Controllers\AdminJalan\PushSubscriptionController;
use App\Http\Controllers\AutentikasiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — SIGAP Dashboard Admin Jalan
|--------------------------------------------------------------------------
| Hanya 1 role yang mengakses dashboard ini: Admin Jalan
| Masyarakat menggunakan aplikasi mobile (React Native).
*/

// ─── Halaman Publik ───────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('landing');
})->name('landing');

// ─── Login Dashboard (khusus Admin Jalan) ─────────────────────────────────────
Route::get('/login', [AutentikasiController::class, 'tampilLogin'])->name('login');
Route::post('/login/proses', [AutentikasiController::class, 'prosesLogin'])->name('login.proses');

// ─── Halaman Terautentikasi ───────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AutentikasiController::class, 'logout'])->name('logout');

    // Dashboard Admin Jalan — hanya peran admin_jalan
    Route::middleware(['admin_jalan'])->prefix('admin-jalan')->name('admin_jalan.')->group(function () {

        // ── Beranda / Dashboard ───────────────────────────────────────────────
        Route::get('/beranda', [BerandaController::class, 'indeks'])->name('beranda');
        Route::post('/push-subscriptions', [PushSubscriptionController::class, 'store'])->name('push_subscriptions.store');
        Route::delete('/push-subscriptions', [PushSubscriptionController::class, 'destroy'])->name('push_subscriptions.destroy');

        // ── Manajemen Laporan ─────────────────────────────────────────────────
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanAdminJalan::class, 'indeks'])->name('indeks');
            Route::get('/detail/{id}', [LaporanAdminJalan::class, 'detail'])->name('detail');
            Route::post('/update-status/{id}', [LaporanAdminJalan::class, 'updateStatus'])->name('update_status');
            Route::get('/ekspor-csv', [LaporanAdminJalan::class, 'eksporCsv'])->name('ekspor_csv');
        });
    });
});
