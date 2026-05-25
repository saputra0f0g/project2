<?php

namespace App\Http\Controllers\AdminUniversal;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\LaporanKeluhan;
use App\Models\Bidang;
use Illuminate\Http\Request;
// KITA GUNAKAN LOGAKTIVITAS AGAR KONSISTEN DENGAN SISTEM PROFIL
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    // Menampilkan daftar semua laporan (Dengan Fitur Filter)
    public function indeks(Request $request)
    {
        // 1. Mulai query dasar (Pastikan panggil relasi pelapor agar popup peta jalan)
        $query = LaporanKeluhan::with('pelapor');

        // 2. Terapkan Filter Status
        if ($request->filled('status') && $request->status != 'Semua Status') {
            $query->where('status', $request->status);
        }

        // 3. Terapkan Filter Bidang
        if ($request->filled('bidang') && $request->bidang != 'Semua Bidang') {
            $query->where('kategori_bidang', $request->bidang);
        }

        // 4. Terapkan Filter Rentang Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        // 5. Terapkan Sorting
        if ($request->sort === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            // Default: Terbaru (desc)
            $query->orderBy('created_at', 'desc');
        }

        // 6. Eksekusi query dengan Pagination (10 data per halaman)
        $semua_laporan = $query->paginate(10)->withQueryString();

        // 7. Hitung statistik untuk kartu atas
        $statistik = [
            'total' => LaporanKeluhan::count(),
            'proses' => LaporanKeluhan::whereIn('status', ['diteruskan', 'proses'])->count(),
            'selesai' => LaporanKeluhan::where('status', 'selesai')->count(),
            'ditolak' => LaporanKeluhan::where('status', 'ditolak')->count(),
        ];

        // 8. Data untuk Peta Bawah
        // Panggil relasi pelapor di sini juga agar popup peta memiliki nama pelapor!
        $sebaran_laporan = LaporanKeluhan::with('pelapor')
                            ->whereNotNull('lokasi_gps')
                            ->select('id', 'id_laporan', 'lokasi_gps', 'status', 'kategori_bidang', 'deskripsi_laporan', 'id_pelapor')
                            ->get();

        // Ambil daftar bidang aktif yang ada di database untuk menu dropdown filter
        $daftar_bidang = Bidang::where('status', 'aktif')->pluck('nama_bidang');

        // 9. MENGAMBIL DATA LOG (AKTIVITAS) UNTUK TAMPILAN
        // Menggunakan LogAktivitas
        $aktivitas_terbaru = LogAktivitas::where('kategori', 'laporan')->latest()->take(5)->get();
        $semua_aktivitas = LogAktivitas::where('kategori', 'laporan')->latest()->get();

        return view('admin_universal.laporan.index', compact(
            'statistik', 'semua_laporan', 'sebaran_laporan', 'daftar_bidang',
            'aktivitas_terbaru', 'semua_aktivitas'
        ));
    }

    public function hapusLog()
    {
        LogAktivitas::where('kategori', 'laporan')->delete();
        return back()->with('sukses', 'Seluruh riwayat aktivitas laporan berhasil dikosongkan!');
    }

    public function hapusLogSatu($id)
    {
        $log = LogAktivitas::where('kategori', 'laporan')->findOrFail($id);
        $log->delete();

        return back()->with('sukses', 'Satu riwayat aktivitas berhasil dihapus.');
    }

    public function detail($id)
    {
        $laporan = LaporanKeluhan::with('pelapor', 'bidangTujuan')->findOrFail($id);
        $bidang_aktif = Bidang::where('status', 'aktif')->get();
        $foto_bukti = explode (',', $laporan->foto_bukti);

        return view('admin_universal.laporan.detail', compact('laporan', 'bidang_aktif', 'foto_bukti'));
    }

    public function disposisi(Request $request, $id)
    {
        $request->validate([
            'id_bidang_tujuan' => 'required'
        ]);

        $laporan = LaporanKeluhan::findOrFail($id);
        $laporan->update([
            'status' => 'diteruskan',
            'id_bidang_tujuan' => $request->id_bidang_tujuan
        ]);

        return redirect()->route('admin_universal.laporan')->with('sukses', 'Laporan berhasil didisposisikan ke bidang terkait!');
    }

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required'
        ]);

        $laporan = LaporanKeluhan::findOrFail($id);
        $laporan->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $request->alasan_penolakan
        ]);

        return redirect()->route('admin_universal.laporan')->with('sukses', 'Laporan telah ditolak.');
    }

    public function ekspor()
    {
        $laporan = LaporanKeluhan::with('pelapor')->orderBy('created_at', 'desc')->get();
        $nama_file = "Data_Laporan_SIGAP_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$nama_file",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($laporan) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['ID Laporan', 'Tanggal', 'Bidang', 'Lokasi', 'Pelapor', 'Status', 'Deskripsi'], ';');

            foreach ($laporan as $row) {
                fputcsv($file, [
                    $row->id_laporan,
                    $row->created_at->format('Y-m-d H:i'),
                    $row->kategori_bidang,
                    $row->alamat_map,
                    $row->pelapor->nama_lengkap ?? 'Anonim',
                    strtoupper($row->status),
                    $row->deskripsi_laporan
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function eksporPdf()
    {
        $laporan = LaporanKeluhan::with('pelapor')->orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('admin_universal.laporan.pdf', compact('laporan'));
        $pdf->setPaper('legal', 'landscape');
        return $pdf->download('Laporan_Resmi_SIGAP_'.date('Y_m_d').'.pdf');
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'kategori_bidang' => 'required',
            'alamat_map' => 'required',
            'deskripsi_laporan' => 'required'
        ]);

        LaporanKeluhan::create([
            'id_laporan' => 'REP-' . date('Ymd') . '-' . rand(100, 999),
            'id_pelapor' => Auth::id(),
            'kategori_bidang' => $request->kategori_bidang,
            'deskripsi_laporan' => $request->deskripsi_laporan,
            'alamat_map' => $request->alamat_map,
            'lokasi_gps' => '-6.5627, 107.7613',
            'status' => 'pending'
        ]);

        return redirect()->route('admin_universal.laporan')->with('sukses', 'Laporan manual berhasil ditambahkan!');
    }

    public function cetakDetailPdf($id)
    {
        $laporan = LaporanKeluhan::with('pelapor')->findOrFail($id);
        $pdf = Pdf::loadView('admin_universal.laporan.pdf_detail', compact('laporan'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Detail_Laporan_'.$laporan->id_laporan.'.pdf');
    }

    public function updateStatus(Request $request, $id)
    {
        $laporan = LaporanKeluhan::findOrFail($id);
        $laporan->status = $request->status;

        if ($request->status == 'ditolak' && $request->filled('alasan_penolakan')) {
            $laporan->alasan_penolakan = $request->alasan_penolakan;
        }

        if ($request->status == 'diteruskan' && $request->filled('bidang_tujuan')) {
            $laporan->kategori_bidang = $request->bidang_tujuan;

            if ($request->filled('catatan_disposisi')) {
                $laporan->catatan_disposisi = $request->catatan_disposisi;
            }
        }

        $laporan->save();

        $statusBaru = $request->status;
        $teksAktivitas = '';

        if ($statusBaru == 'diteruskan') {
            $teksAktivitas = 'Mendisposisikan laporan ' . $laporan->id_laporan . ' ke Bidang ' . $request->bidang_tujuan;
        } elseif ($statusBaru == 'ditolak') {
            $teksAktivitas = 'Menolak laporan ' . $laporan->id_laporan;
        } elseif ($statusBaru == 'selesai') {
            $teksAktivitas = 'Menyelesaikan laporan ' . $laporan->id_laporan;
        } else {
            $teksAktivitas = 'Mengubah status laporan ' . $laporan->id_laporan;
        }

        // Simpan menggunakan LogAktivitas
        LogAktivitas::create([
            'aktivitas' => $teksAktivitas,
            'kategori'  => 'laporan',
            'user_id'   => Auth::id()
        ]);

        return redirect()->back()->with('sukses', 'Status laporan berhasil diperbarui!');
    }
}
