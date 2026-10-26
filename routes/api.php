<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SIGAP Mobile Backend
|--------------------------------------------------------------------------
| Fokus: Pelaporan Infrastruktur JALAN
|
| Role:
|   masyarakat : pengguna mobile yang melapor
|   admin_jalan: admin dashboard (login melalui web session)
|
| Base URL: http://[IP_SERVER]/api/...
|
| Tabel yang digunakan:
|   users           : id, nama_lengkap, username, email, nomor_hp,
|                     peran (masyarakat|admin_jalan), status_akun, foto_profil
|   laporan_keluhan : id_laporan, id_pelapor, judul_laporan, jenis_kerusakan,
|                     deskripsi_laporan, lokasi_gps,
|                     alamat_map, foto_bukti, status, catatan_admin, alasan_penolakan
*/

// ─── CORS PREFLIGHT ───────────────────────────────────────────────────────────
// HARUS ada sebelum grup middleware agar preflight tidak kena 401
Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept');
})->where('any', '.*');

// ─── PUBLIC (tidak butuh token) ───────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);       // Daftar (masyarakat saja)
    Route::post('/login', [AuthController::class, 'login']);          // Login mobile (masyarakat)
});

// ─── PROTECTED (butuh Bearer token Sanctum) ───────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // ── Auth ─────────────────────────────────────────────────────────────────
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // ── User / Profil ─────────────────────────────────────────────────────────
    Route::prefix('user')->group(function () {
        Route::get('/profil', [UserController::class, 'getProfil']);   // GET profil saya
        Route::put('/profil', [UserController::class, 'updateProfil']); // Update nama & HP
        Route::post('/foto-profil', [UserController::class, 'uploadFoto']);  // Upload foto profil
        Route::put('/password', [UserController::class, 'gantiPassword']); // Ganti password
    });

    // ── Statistik (semua role, filter by peran di dalam controller) ───────────
    Route::get('/statistik', [LaporanController::class, 'statistik']);

    // ─────────────────────────────────────────────────────────────────────────
    // ENDPOINT MASYARAKAT
    // Masyarakat: submit, lihat histori, lihat detail, hapus laporan sendiri
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('laporan')->group(function () {
        Route::get('/', [LaporanController::class, 'index']);   // Histori laporan saya
        Route::post('/', [LaporanController::class, 'store']);   // Kirim laporan baru
        Route::get('/{id}', [LaporanController::class, 'show']);    // Detail laporan (own or admin)
        Route::delete('/{id}', [LaporanController::class, 'destroy']); // Hapus laporan (status=menunggu)
    });

    // ─────────────────────────────────────────────────────────────────────────
    // ENDPOINT ADMIN JALAN
    // Admin Jalan: lihat semua laporan, update status
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('admin/laporan')->group(function () {
        Route::get('/', [LaporanController::class, 'indexAdmin']);   // Semua laporan masuk
        Route::get('/{id}', [LaporanController::class, 'show']);         // Detail laporan
        Route::patch('/{id}/status', [LaporanController::class, 'updateStatus']); // Update status
    });
});
