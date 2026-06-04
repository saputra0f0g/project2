<?php

namespace App\Http\Controllers\AdminUniversal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanKeluhan; // Pastikan nama modelmu ini benar
use App\Models\Bidang;
use Carbon\Carbon;

class StatistikController extends Controller
{
    public function indeks(Request $request)
    {
        // Tangkap filter tahun dari dropdown, jika kosong gunakan tahun ini
        $tahun = $request->tahun ?? date('Y');

        // =========================================================
        // 1. DATA NYATA UNTUK 4 KARTU STATISTIK ATAS
        // =========================================================
        $total_laporan = LaporanKeluhan::whereYear('created_at', $tahun)->count();
        $selesai = LaporanKeluhan::whereYear('created_at', $tahun)->where('status', 'selesai')->count();
        $dalam_proses = LaporanKeluhan::whereYear('created_at', $tahun)->whereNotIn('status', ['pending', 'dikembalikan', 'selesai', 'ditolak'])->count();
        $menunggu = LaporanKeluhan::whereYear('created_at', $tahun)->whereIn('status', ['pending', 'dikembalikan'])->count();

        // Logika Pintar untuk membandingkan dengan bulan lalu (Tren)
        $bulan_ini = Carbon::now()->month;
        $laporan_bulan_ini = LaporanKeluhan::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan_ini)->count();
        $laporan_bulan_lalu = LaporanKeluhan::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan_ini - 1)->count();

        $selisih_total = $laporan_bulan_ini - $laporan_bulan_lalu;
        $persen_total = $laporan_bulan_lalu > 0 ? round(($selisih_total / $laporan_bulan_lalu) * 100) : ($laporan_bulan_ini > 0 ? 100 : 0);
        $rasio_selesai = $total_laporan > 0 ? round(($selesai / $total_laporan) * 100) : 0;

        $statistik = [
            'total_laporan'   => $total_laporan,
            'selesai'         => $selesai,
            'dalam_proses'    => $dalam_proses,
            'laporan_terbaru' => $menunggu,
            'selisih_total'   => $selisih_total,
            'persen_total'    => $persen_total,
            'rasio_selesai'   => $rasio_selesai,
        ];

        // =========================================================
        // 2. DATA NYATA UNTUK GRAFIK BAR (PER BULAN)
        // =========================================================
        $grafik_bulan = [];
        $max_bulan = 0;
        $nama_bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($i = 1; $i <= 12; $i++) {
            $total_bulan = LaporanKeluhan::whereYear('created_at', $tahun)->whereMonth('created_at', $i)->count();
            $grafik_bulan[] = [
                'nama'  => $nama_bulan[$i - 1],
                'total' => $total_bulan
            ];
            // Mencari nilai tertinggi agar tinggi visual grafik dinamis
            if ($total_bulan > $max_bulan) {
                $max_bulan = $total_bulan;
            }
        }

        // =========================================================
        // 3. DATA NYATA UNTUK GRAFIK DONUT (PER BIDANG)
        // =========================================================
        $semua_bidang = Bidang::all();
        $grafik_bidang = [];
        $warna_palette = ['#1E3A8A', '#FBBF24', '#10B981', '#EF4444', '#8B5CF6', '#14B8A6']; // Warna khas PUPR/Tailwind
        $start_deg = 0;

        foreach ($semua_bidang as $index => $b) {
            // Hitung laporan berdasarkan bidang
            $total_per_bidang = LaporanKeluhan::whereYear('created_at', $tahun)
                                    ->where('kategori_bidang', $b->nama_bidang)
                                    ->count();

            if ($total_per_bidang > 0 && $total_laporan > 0) {
                $persen = round(($total_per_bidang / $total_laporan) * 100);
                $derajat = ($persen / 100) * 360;
                $end_deg = $start_deg + $derajat;

                $grafik_bidang[] = [
                    'nama'      => $b->nama_bidang,
                    'total'     => $total_per_bidang,
                    'persen'    => $persen,
                    'warna'     => $warna_palette[$index % count($warna_palette)], // Rotasi warna
                    'start_deg' => $start_deg,
                    'end_deg'   => $end_deg
                ];

                $start_deg = $end_deg; // Sambung derajat untuk bidang berikutnya
            }
        }

        // 4. Kirim ke Tampilan Statistik
        return view('admin_universal.statistik.index', compact('statistik', 'grafik_bulan', 'max_bulan', 'grafik_bidang', 'tahun'));
    }
}
