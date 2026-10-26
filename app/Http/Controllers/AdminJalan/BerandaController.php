<?php

namespace App\Http\Controllers\AdminJalan;

use App\Http\Controllers\Controller;
use App\Models\LaporanKeluhan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BerandaController extends Controller
{
    /**
     * Dashboard utama Admin Jalan.
     */
    public function indeks(Request $request)
    {
        // ─── 1. STATISTIK KARTU UTAMA ─────────────────────────────────────────
        $totalLaporan   = LaporanKeluhan::count();
        $selesai        = LaporanKeluhan::where('status', 'selesai')->count();
        $dalamProses    = LaporanKeluhan::whereIn('status', ['diverifikasi', 'dalam_perbaikan'])->count();
        $menunggu       = LaporanKeluhan::where('status', 'menunggu')->count();
        $diverifikasi   = LaporanKeluhan::where('status', 'diverifikasi')->count();
        $dalamPerbaikan = LaporanKeluhan::where('status', 'dalam_perbaikan')->count();
        $ditolak        = LaporanKeluhan::where('status', 'ditolak')->count();

        // Hitung Perbandingan Laporan (Bulan Ini vs Bulan Lalu)
        $bulanIni  = Carbon::now()->month;
        $tahunIni  = Carbon::now()->year;
        $bulanLalu = Carbon::now()->subMonth()->month;
        $tahunLalu = Carbon::now()->subMonth()->year;

        $laporanBulanIni = LaporanKeluhan::whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->count();
        $laporanBulanLalu = LaporanKeluhan::whereMonth('created_at', $bulanLalu)
            ->whereYear('created_at', $tahunLalu)
            ->count();

        // Kalkulasi Persentase Pertumbuhan & Selisih
        $selisihTotal = $laporanBulanIni - $laporanBulanLalu;
        $persenTotal  = $laporanBulanLalu > 0
            ? round(($selisihTotal / $laporanBulanLalu) * 100)
            : ($laporanBulanIni > 0 ? 100 : 0);

        // Kalkulasi Rasio Penyelesaian (%)
        $rasioSelesai = $totalLaporan > 0 ? round(($selesai / $totalLaporan) * 100) : 0;

        $statistik = [
            'total'           => $totalLaporan,
            'total_laporan'   => $totalLaporan,
            'selisih_total'   => $selisihTotal,
            'persen_total'    => $persenTotal,
            'selesai'         => $selesai,
            'rasio_selesai'   => $rasioSelesai,
            'dalam_proses'    => $dalamProses,
            'menunggu'        => $menunggu,
            'laporan_terbaru' => $menunggu,
            'diverifikasi'    => $diverifikasi,
            'dalam_perbaikan' => $dalamPerbaikan,
            'ditolak'         => $ditolak,
        ];

        // ─── 2. DATA PETA SEBARAN (GIS LEAFLET) ──────────────────────────────
        $sebaran_laporan = LaporanKeluhan::with('pelapor:id,nama_lengkap,foto_profil')
            ->whereNotNull('lokasi_gps')
            ->where('lokasi_gps', '!=', '')
            ->get([
                'id',
                'id_laporan',
                'id_pelapor',
                'judul_laporan',
                'jenis_kerusakan',
                'tingkat_kerusakan',
                'deskripsi_laporan',
                'lokasi_gps',
                'alamat_map',
                'foto_bukti',
                'status',
                'created_at',
            ]);

        // ─── 3. LAPORAN TERBARU (5 TERAKHIR) ─────────────────────────────────
        $laporanTerbaru = LaporanKeluhan::with('pelapor')
            ->latest()
            ->take(5)
            ->get();

        // ─── 4. STATISTIK PER JENIS KERUSAKAN ────────────────────────────────
        $perJenis = LaporanKeluhan::selectRaw('jenis_kerusakan, COUNT(*) as total')
            ->groupBy('jenis_kerusakan')
            ->pluck('total', 'jenis_kerusakan');

        return view('admin_jalan.beranda', compact(
            'statistik',
            'sebaran_laporan',
            'laporanTerbaru',
            'perJenis'
        ));
    }
}

