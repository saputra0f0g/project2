<?php

namespace App\Http\Controllers\AdminBidang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LaporanKeluhan;

class BerandaController extends Controller
{
    public function indeks()
    {
        $user = Auth::user();

        // 1. Ambil Data Bidang dari relasi User
        $bidang = $user->bidang;
        $namaBidangAdmin = $bidang->nama_bidang ?? '';

        // 2. Hitung Statistik (Hanya untuk bidang si Admin ini)

        // Total Laporan Masuk (Status: Diteruskan, Proses, Selesai)
        $total_laporan = LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)
                            ->whereIn('status', ['diteruskan', 'proses', 'selesai'])
                            ->count();

        // Laporan Mendesak / Menunggu Validasi (Status: Diteruskan)
        $laporan_mendesak = LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)
                            ->where('status', 'diteruskan')
                            ->count();

        // Pekerjaan Berjalan (Status: Proses)
        $laporan_proses = LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)
                            ->where('status', 'proses')
                            ->count();

        // Pekerjaan Selesai (Status: Selesai)
        $laporan_selesai = LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)
                            ->where('status', 'selesai')
                            ->count();

        // 3. Ambil Data untuk Pemetaan (Peta Leaflet)
        $sebaran_laporan = LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)
                            ->whereIn('status', ['diteruskan', 'proses', 'selesai'])
                            ->get(['id_laporan', 'lokasi_gps', 'status', 'kategori_bidang']);

        // 4. Kirim semua data ke tampilan indeks.blade.php
        return view('admin_bidang.beranda.indeks', compact(
            'bidang',
            'total_laporan',
            'laporan_mendesak',
            'laporan_proses',
            'laporan_selesai',
            'sebaran_laporan'
        ));
    }
}
