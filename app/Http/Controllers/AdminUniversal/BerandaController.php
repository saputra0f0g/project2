<?php

namespace App\Http\Controllers\AdminUniversal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LaporanKeluhan;
use Carbon\Carbon; // WAJIB DITAMBAHKAN untuk manipulasi waktu

class BerandaController extends Controller
{
    public function indeks(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));

        // === LOGIKA STATISTIK KARTU ATAS (Dinamis) ===
        $total_laporan = DB::table('laporan_keluhan')->count();
        $selesai = DB::table('laporan_keluhan')->where('status', 'selesai')->count();
        $dalam_proses = DB::table('laporan_keluhan')->whereNotIn('status', ['pending', 'dikembalikan', 'selesai', 'ditolak'])->count();
        $laporan_terbaru = DB::table('laporan_keluhan')->whereIn('status', ['pending', 'dikembalikan'])->count();

        // Hitung Perbandingan Laporan (Bulan Ini vs Bulan Lalu)
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;
        $bulanLalu = Carbon::now()->subMonth()->month;
        $tahunLalu = Carbon::now()->subMonth()->year;

        $laporanBulanIni = DB::table('laporan_keluhan')->whereMonth('created_at', $bulanIni)->whereYear('created_at', $tahunIni)->count();
        $laporanBulanLalu = DB::table('laporan_keluhan')->whereMonth('created_at', $bulanLalu)->whereYear('created_at', $tahunLalu)->count();

        // Kalkulasi Persentase Pertumbuhan & Selisih
        $selisihTotal = $laporanBulanIni - $laporanBulanLalu;
        $persenTotal = $laporanBulanLalu > 0 ? round(($selisihTotal / $laporanBulanLalu) * 100) : ($laporanBulanIni > 0 ? 100 : 0);

        // Kalkulasi Rasio Penyelesaian (%)
        $rasioSelesai = $total_laporan > 0 ? round(($selesai / $total_laporan) * 100) : 0;

        $statistik = [
            'total_laporan'   => $total_laporan,
            'selisih_total'   => $selisihTotal,
            'persen_total'    => $persenTotal,
            'selesai'         => $selesai,
            'rasio_selesai'   => $rasioSelesai,
            'dalam_proses'    => $dalam_proses,
            'laporan_terbaru' => $laporan_terbaru,
        ];

        // === LOGIKA DATA PETA ===
        $sebaran_laporan = LaporanKeluhan::whereNotNull('lokasi_gps')
                                         ->select('id_laporan', 'lokasi_gps', 'status', 'kategori_bidang')
                                         ->get();

        // === LOGIKA GRAFIK BAR (Bulan) ===
        $laporanPerBulanRaw = DB::table('laporan_keluhan')
            ->select(DB::raw('MONTH(created_at) as bulan'), DB::raw('count(*) as total'))
            ->whereYear('created_at', $tahun)
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $grafik_bulan = [];
        $max_bulan = 0;
        $nama_bulan = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGS', 'SEP', 'OKT', 'NOV', 'DES'];

        for ($i = 1; $i <= 12; $i++) {
            $total = $laporanPerBulanRaw[$i] ?? 0;
            $grafik_bulan[] = ['nama' => $nama_bulan[$i - 1], 'total' => $total];
            if ($total > $max_bulan) $max_bulan = $total;
        }

        // === LOGIKA GRAFIK DONUT (Bidang) ===
        $laporanPerBidangRaw = DB::table('laporan_keluhan')
            ->select('kategori_bidang', DB::raw('count(*) as total'))
            ->groupBy('kategori_bidang')
            ->get();

        $grafik_bidang = [];
        $total_semua = DB::table('laporan_keluhan')->count() ?: 1;
        $warna_bidang = ['#2563EB', '#FACC15', '#EF4444', '#10B981', '#8B5CF6', '#F97316'];
        $current_deg = 0;

        foreach ($laporanPerBidangRaw as $index => $data) {
            $persentase = round(($data->total / $total_semua) * 100);
            $deg = ($persentase / 100) * 360;

            $grafik_bidang[] = [
                'nama' => $data->kategori_bidang ?: 'Lainnya',
                'total' => $data->total,
                'persen' => $persentase,
                'warna' => $warna_bidang[$index % count($warna_bidang)],
                'start_deg' => $current_deg,
                'end_deg' => $current_deg + $deg
            ];
            $current_deg += $deg;
        }

        return view('admin_universal.beranda', compact(
            'statistik', 'sebaran_laporan', 'grafik_bulan', 'max_bulan', 'grafik_bidang', 'tahun'
        ));
    }

    // Menampilkan Peta Sebaran Wilayah (Command Center)
    public function peta()
    {
        // Ambil semua data laporan yang memiliki koordinat GPS
        // Jika belum ada model LaporanKeluhan di atas, pastikan use App\Models\LaporanKeluhan; ditambahkan
        $semua_laporan = \App\Models\LaporanKeluhan::whereNotNull('lokasi_gps')->get();

        return view('admin_universal.peta_wilayah', compact('semua_laporan'));
    }

    // Menampilkan Halaman Pusat Bantuan (Satu Halaman Penuh)
    public function bantuan()
    {
        return view('admin_universal.bantuan');
    }

}
