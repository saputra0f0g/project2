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
        $sebaran_laporan = LaporanKeluhan::with('pelapor') // Pastikan 'pelapor' di-load
                            ->where('kategori_bidang', $namaBidangAdmin)
                            ->whereIn('status', ['diteruskan', 'proses', 'selesai'])
                            ->get(['id', 'id_laporan', 'lokasi_gps', 'status', 'kategori_bidang', 'deskripsi_laporan', 'id_pelapor']);

        // 4. Ambil Data Tim Pekerja (Pekerja Bidang + UPTD) untuk ditampilkan di sidebar peta
        $tim_pekerja = \App\Models\User::whereIn('peran', ['pekerja_bidang', 'pekerja_uptd', 'pekerja', 'pekerja_lapangan'])
                        ->where('status_akun', 'aktif')
                        ->take(6) // Ambil 6 pekerja saja agar kotak tidak terlalu panjang
                        ->get();

        // Pastikan variabel $tim_pekerja ikut dikirim di dalam compact
        return view('admin_bidang.beranda.indeks', compact(
            'bidang', 'total_laporan', 'laporan_mendesak', 'laporan_proses', 'laporan_selesai', 'sebaran_laporan', 'tim_pekerja'
        ));

        // 5. Kirim semua data ke tampilan indeks.blade.php
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
