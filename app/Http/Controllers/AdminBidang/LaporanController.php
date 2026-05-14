<?php

namespace App\Http\Controllers\AdminBidang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanKeluhan;
use App\Models\User;
use App\Models\PenugasanPekerja;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Wajib ditambahkan untuk fitur PDF

class LaporanController extends Controller
{
    /**
     * Tampil daftar laporan khusus bidang yang sedang login
     */
    public function indeks()
    {
        $user = Auth::user();
        $namaBidangAdmin = $user->bidang->nama_bidang ?? '';

        $laporan_masuk = LaporanKeluhan::with('pelapor')
            ->where('kategori_bidang', $namaBidangAdmin)
            ->whereIn('status', ['diteruskan', 'proses', 'selesai'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        $statistik = [
            'total'    => LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)->whereIn('status', ['diteruskan', 'proses', 'selesai'])->count(),
            'menunggu' => LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)->where('status', 'diteruskan')->count(),
            'proses'   => LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)->where('status', 'proses')->count(),
            'selesai'  => LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)->where('status', 'selesai')->count(),
        ];

        $sebaran_laporan = LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)
            ->whereIn('status', ['diteruskan', 'proses', 'selesai'])
            ->get(['id_laporan', 'kategori_bidang', 'lokasi_gps', 'status']);

        return view('admin_bidang.laporan.index', compact('laporan_masuk', 'statistik', 'sebaran_laporan'));
    }

    /**
     * Tampil detail laporan
     */
    public function detail($id)
    {
        $user = Auth::user();
        $namaBidangAdmin = $user->bidang->nama_bidang ?? '';

        $laporan = LaporanKeluhan::with('pelapor')
            ->where('kategori_bidang', $namaBidangAdmin)
            ->findOrFail($id);

        $pekerja = User::where('peran', 'pekerja_bidang')
                       ->where('id_bidang', $user->id_bidang)
                       ->where('status_akun', 'aktif')
                       ->get();

        return view('admin_bidang.laporan.detail', compact('laporan', 'pekerja'));
    }

    /**
     * Proses Menugaskan Pekerja
     */
    public function tugaskan(Request $request, $id)
    {
        $request->validate([
            'id_pekerja' => 'required|exists:users,id'
        ]);

        $user = Auth::user();
        $namaBidangAdmin = $user->bidang->nama_bidang ?? '';

        $laporan = LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)->findOrFail($id);

        PenugasanPekerja::create([
            'id_laporan' => $laporan->id,
            'id_admin_bidang' => $user->id,
            'id_pekerja' => $request->id_pekerja,
            'instruksi_tambahan' => $request->instruksi_tambahan,
            'status_tugas' => 'ditugaskan'
        ]);

        $laporan->update(['status' => 'proses']);

        return redirect()->route('admin_bidang.laporan')->with('sukses', 'Laporan berhasil diproses! Pekerja telah ditugaskan ke lokasi.');
    }

    /**
     * Ekspor Rekap ke Excel (CSV Terformat)
     */
    public function eksporExcel()
    {
        $namaBidangAdmin = Auth::user()->bidang->nama_bidang ?? '';
        $laporan = LaporanKeluhan::with('pelapor')
            ->where('kategori_bidang', $namaBidangAdmin)
            ->whereIn('status', ['diteruskan', 'proses', 'selesai'])
            ->orderBy('created_at', 'desc')
            ->get();

        $fileName = 'Rekap_Laporan_' . str_replace(' ', '_', $namaBidangAdmin) . '_' . date('Ymd') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID Laporan', 'Tanggal Masuk', 'Kategori', 'Pelapor', 'Lokasi', 'Status', 'Deskripsi'];

        $callback = function() use($laporan, $columns) {
            $file = fopen('php://output', 'w');

            // 1. Tambahkan BOM (Byte Order Mark) agar Excel membaca file sebagai UTF-8 dengan sempurna
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // 2. Tulis Header Tabel dengan penambahan delimiter titik koma (';')
            fputcsv($file, $columns, ';');

            foreach ($laporan as $row) {
                // 3. Tulis Isi Data, juga dengan delimiter titik koma (';')
                fputcsv($file, [
                    $row->id_laporan,
                    \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i'),
                    $row->kategori_bidang,
                    $row->pelapor->nama_lengkap ?? 'Anonim',
                    $row->alamat_map,
                    strtoupper($row->status),
                    $row->deskripsi_laporan
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Ekspor Rekap ke PDF
     */
    public function eksporPdf()
    {
        $namaBidangAdmin = Auth::user()->bidang->nama_bidang ?? '';

        $laporan = LaporanKeluhan::with('pelapor')
            ->where('kategori_bidang', $namaBidangAdmin)
            ->whereIn('status', ['diteruskan', 'proses', 'selesai'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Me-load view PDF
        $pdf = Pdf::loadView('admin_bidang.laporan.pdf', compact('laporan', 'namaBidangAdmin'));

        // Atur ukuran kertas
        $pdf->setPaper('legal', 'landscape');

        return $pdf->download('Rekap_Laporan_' . str_replace(' ', '_', $namaBidangAdmin) . '_' . date('Y_m_d') . '.pdf');
    }
}
