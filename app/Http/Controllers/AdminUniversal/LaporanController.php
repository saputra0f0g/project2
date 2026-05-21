<?php

namespace App\Http\Controllers\AdminUniversal;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\LaporanKeluhan;
use App\Models\Bidang;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    // Menampilkan daftar semua laporan (Dengan Fitur Filter)
    public function indeks(\Illuminate\Http\Request $request)
    {
        // 1. Mulai query dasar
        $query = LaporanKeluhan::with('pelapor')->orderBy('created_at', 'desc');

        // 2. Terapkan Filter jika ada request dari form pencarian
        if ($request->filled('status') && $request->status != 'Semua Status') {
            if ($request->status == 'proses') {
                $query->whereIn('status', ['diteruskan', 'proses']);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('bidang') && $request->bidang != 'Semua Bidang') {
            $query->where('kategori_bidang', $request->bidang);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        // 3. Eksekusi query dengan Pagination (10 data per halaman)
        $semua_laporan = $query->paginate(10);

        // 4. Hitung statistik untuk kartu atas
        $statistik = [
            'total' => LaporanKeluhan::count(),
            'terbaru' => LaporanKeluhan::where('status', 'pending')->count(),
            'proses' => LaporanKeluhan::whereIn('status', ['diteruskan', 'proses'])->count(),
            'selesai' => LaporanKeluhan::where('status', 'selesai')->count(),
            'ditolak' => LaporanKeluhan::where('status', 'ditolak')->count(),
        ];

        // 5. Data untuk Peta Bawah
        $sebaran_laporan = LaporanKeluhan::whereNotNull('lokasi_gps')
                                         ->select('id_laporan', 'lokasi_gps', 'status', 'kategori_bidang')
                                         ->get();

        // 6. Ambil daftar bidang unik yang ada di database untuk menu dropdown
        $daftar_bidang = LaporanKeluhan::select('kategori_bidang')->distinct()->pluck('kategori_bidang');

        return view('admin_universal.laporan.index', compact('semua_laporan', 'statistik', 'sebaran_laporan', 'daftar_bidang'));
    }

    // Menampilkan detail spesifik satu laporan
    public function detail($id)
    {
        $laporan = LaporanKeluhan::with('pelapor', 'bidangTujuan')->findOrFail($id);
        $bidang_aktif = Bidang::where('status', 'aktif')->get(); // Untuk dropdown disposisi
        $foto_bukti = explode (',', $laporan->foto_bukti);

        return view('admin_universal.laporan.detail', compact('laporan', 'bidang_aktif', 'foto_bukti'));
    }

    // Proses Meneruskan (Disposisi) Laporan ke Bidang
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

    // Proses Menolak Laporan
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

    // Fungsi Ekspor ke Excel (Format CSV Murni Ber-kolom Rapi)
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
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM agar rapi di Excel
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

    // Fungsi Ekspor ke PDF Profesional
    public function eksporPdf()
    {
        $laporan = LaporanKeluhan::with('pelapor')->orderBy('created_at', 'desc')->get();

        // Kita akan memuat tampilan khusus PDF yang akan kita buat selanjutnya
        $pdf = Pdf::loadView('admin_universal.laporan.pdf', compact('laporan'));

        // Mengatur ukuran kertas ke F4 / Legal dengan orientasi Landscape (Tidur) agar tabel muat
        $pdf->setPaper('legal', 'landscape');

        return $pdf->download('Laporan_Resmi_SIGAP_'.date('Y_m_d').'.pdf');
    }

    // Fungsi untuk Menyimpan Laporan Baru dari Dasbor
    public function simpan(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'kategori_bidang' => 'required',
            'alamat_map' => 'required',
            'deskripsi_laporan' => 'required'
        ]);

        LaporanKeluhan::create([
            'id_laporan' => 'REP-' . date('Ymd') . '-' . rand(100, 999),
            'id_pelapor' => \Illuminate\Support\Facades\Auth::id(),
            'kategori_bidang' => $request->kategori_bidang,
            'deskripsi_laporan' => $request->deskripsi_laporan,
            'alamat_map' => $request->alamat_map,
            'lokasi_gps' => '-6.5627, 107.7613', // Titik default Subang
            'status' => 'pending'
        ]);

        return redirect()->route('admin_universal.laporan')->with('sukses', 'Laporan manual berhasil ditambahkan!');
    }

    // Fungsi Cetak PDF Khusus 1 Laporan (A4 Portrait)
    public function cetakDetailPdf($id)
    {
        $laporan = LaporanKeluhan::with('pelapor')->findOrFail($id);

        // Memuat desain PDF khusus detail
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin_universal.laporan.pdf_detail', compact('laporan'));

        // Atur ukuran kertas A4, Posisi Berdiri (Portrait)
        $pdf->setPaper('A4', 'portrait');

        // Menggunakan stream() agar PDF terbuka di tab baru (bisa diprint langsung), bukan otomatis terunduh
        return $pdf->stream('Detail_Laporan_'.$laporan->id_laporan.'.pdf');
    }

    // Fungsi untuk mengubah status laporan dari halaman Detail
    public function updateStatus(\Illuminate\Http\Request $request, $id)
    {
        $laporan = LaporanKeluhan::findOrFail($id);
        $laporan->status = $request->status;

        // Simpan data tambahan jika ada
        if ($request->status == 'ditolak' && $request->filled('alasan_penolakan')) {
            // Pastikan kamu punya kolom 'alasan_penolakan' di database / tabel laporan
            $laporan->alasan_penolakan = $request->alasan_penolakan;
        }

        if ($request->status == 'diteruskan' && $request->filled('bidang_tujuan')) {
            $laporan->kategori_bidang = $request->bidang_tujuan;

            // Tambahkan baris ini untuk menyimpan catatan jika ada (Pastikan kolom 'catatan_disposisi' ada di database)
            if ($request->filled('catatan_disposisi')) {
                $laporan->catatan_disposisi = $request->catatan_disposisi;
            }
        }

        $laporan->save();

        \App\Models\LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => "Memperbarui status laporan #{$id} menjadi " . $request->status,
            'kategori' => 'Laporan'
        ]);

        return redirect()->back()->with('sukses', 'Status laporan berhasil diperbarui!');
    }
}
