<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Sigap-Mobile Backend
|--------------------------------------------------------------------------
| Semua route di sini diakses via:  http://IP_LAPTOP/api/...
|
| Kolom DB yang digunakan (sesuai skema Sigap-Web):
|   users           : nama_lengkap, username, email, nomor_hp, peran, foto_profil
|   laporan_keluhan : id_laporan, id_pelapor, kategori_bidang, deskripsi_laporan,
|                     lokasi_gps, alamat_map, foto_bukti, status, id_bidang_tujuan
*/

// ─── PUBLIC (tidak butuh token) ───────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register',        [AuthController::class, 'register']);
    Route::post('/login',           [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/verify-otp',      [AuthController::class, 'verifyOtp']);
    Route::post('/reset-password',  [AuthController::class, 'resetPassword']);
});

// ─── CORS PREFLIGHT ─────────────────────────────────────────────────────────
// Browser mengirim OPTIONS sebelum setiap cross-origin request (POST/PUT/PATCH).
// Route ini HARUS ada sebelum grup auth agar preflight tidak kena middleware 401.
Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin',  '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept');
})->where('any', '.*');

// ─── PROTECTED (butuh Bearer token dari Sanctum) ─────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // User / Profil
    Route::prefix('user')->group(function () {
        Route::get('/profil',       [UserController::class, 'getProfil']);
        Route::put('/profil',       [UserController::class, 'updateProfil']);
        Route::post('/foto-profil', [UserController::class, 'uploadFoto']);
        Route::put('/password',     [UserController::class, 'gantiPassword']);
    });

    // Laporan
    Route::prefix('laporan')->group(function () {
        Route::get('/statistik/pegawai', [LaporanController::class, 'statistikPegawai']); // HARUS sebelum /{id}
        Route::get('/statistik',     [LaporanController::class, 'statistik']);   // HARUS sebelum /{id}
        Route::get('/semua',         [LaporanController::class, 'indexSemua']); // Khusus pegawai
        Route::get('/',              [LaporanController::class, 'index']);       // Milik saya
        Route::post('/',             [LaporanController::class, 'store']);       // Buat baru
        Route::get('/{id}',          [LaporanController::class, 'show']);
        Route::patch('/{id}/status', [LaporanController::class, 'updateStatus']); // Khusus pegawai (tanpa foto)
        Route::post('/{id}/progres-pegawai', [LaporanController::class, 'updateProgresPegawai']); // Khusus pegawai (dengan foto progres, POST for multipart)
        Route::delete('/{id}',       [LaporanController::class, 'destroy']);
    });

    // Daftar pegawai (khusus admin)
    Route::get('/pegawai', [UserController::class, 'getDaftarPegawai']);
});
