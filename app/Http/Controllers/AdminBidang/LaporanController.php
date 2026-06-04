<?php

namespace App\Http\Controllers\AdminBidang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanKeluhan;
use App\Models\User;
use App\Models\PenugasanPekerja;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Tampil daftar laporan khusus bidang yang sedang login
     */
    public function indeks(Request $request)
    {
        $user = Auth::user();
        $namaBidangAdmin = $user->bidang->nama_bidang ?? '';

        $laporan_masuk = LaporanKeluhan::with('pelapor')
            ->where('kategori_bidang', $namaBidangAdmin)
            ->whereIn('status', ['diteruskan', 'menunggu_validasi', 'proses', 'terkendala', 'revisi', 'ditunda', 'selesai', 'ditolak'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        $statistik = [
            'total'    => LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)->whereIn('status', ['diteruskan', 'menunggu_validasi', 'proses', 'terkendala', 'revisi', 'ditunda', 'selesai', 'ditolak'])->count(),
            'menunggu' => LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)->whereIn('status', ['diteruskan', 'menunggu_validasi'])->count(),
            'proses'   => LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)->whereIn('status', ['proses', 'terkendala', 'revisi', 'ditunda'])->count(),
            'selesai'  => LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)->where('status', 'selesai')->count(),
        ];

        $sebaran_laporan = LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)
            ->whereIn('status', ['diteruskan', 'menunggu_validasi', 'proses', 'terkendala', 'revisi', 'ditunda', 'selesai'])
            ->get(['id', 'id_laporan', 'lokasi_gps', 'status', 'kategori_bidang']);

        // AMBIL DATA AKTIVITAS UNTUK DITAMPILKAN DI SIDEBAR PETA
        $aktivitas_terbaru = LogAktivitas::where('kategori', 'laporan_bidang')->latest()->take(5)->get();
        // AMBIL SEMUA DATA AKTIVITAS UNTUK DI POP-UP
        $semua_aktivitas = LogAktivitas::where('kategori', 'laporan_bidang')->latest()->get();


        //=================================================================================
        // fungsi filter dan sorting
        // 1. Buat Query Dasar
        $query = \App\Models\LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin);

        // 2. Logika Filter Status
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // 3. Logika Sorting (Urutkan)
        if ($request->sort === 'terlama') {
            $query->orderBy('created_at', 'asc'); // Yang paling lama di atas
        } else {
            $query->orderBy('created_at', 'desc'); // Default: Yang terbaru di atas
        }

        // 4. Eksekusi dengan paginasi (tambahkan withQueryString agar filter tidak hilang saat ganti halaman)
        $laporan_masuk = $query->paginate(10)->withQueryString();

        //=================================================================================

        return view('admin_bidang.laporan.index', compact('laporan_masuk', 'statistik', 'sebaran_laporan', 'aktivitas_terbaru', 'semua_aktivitas'));
    }

    /**
     * Tampil detail laporan
     */
    public function detail($id)
    {
        $user = Auth::user();
        $namaBidangAdmin = $user->bidang->nama_bidang ?? '';

        $laporan = LaporanKeluhan::with(['pelapor', 'penugasan.buktiProgres'])
            ->where('kategori_bidang', $namaBidangAdmin)
            ->findOrFail($id);

        $pekerja = \App\Models\User::whereIn('peran', ['pekerja_bidang', 'pekerja_uptd', 'pekerja', 'pekerja_lapangan'])
                        ->where('status_akun', 'aktif')
                        ->get();

        return view('admin_bidang.laporan.detail', compact('laporan', 'pekerja'));
    }

    /**
     * FUNGSI BARU 1: Hapus Semua Log Aktivitas (Admin Bidang)
     */
    public function hapusSemuaLog()
    {
        LogAktivitas::where('kategori', 'laporan_bidang')->delete();
        return back()->with('sukses', 'Seluruh riwayat aktivitas laporan bidang berhasil dikosongkan!');
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
        $pekerjaTarget = User::find($request->id_pekerja);

        PenugasanPekerja::create([
            'id_laporan' => $laporan->id,
            'id_admin_bidang' => $user->id,
            'id_pekerja' => $request->id_pekerja,
            'instruksi_tambahan' => $request->instruksi_tambahan,
            'status_tugas' => 'ditugaskan'
        ]);

        // Simpan info id_pekerja dan prioritas ke tabel laporan_keluhan
        $laporan->update([
            'status' => 'proses',
            'id_pekerja' => $request->id_pekerja,
            'prioritas' => $request->prioritas,
            'instruksi_tambahan' => $request->instruksi_tambahan
        ]);

        // CATAT AKTIVITAS KE SISTEM LOG
        LogAktivitas::create([
            'aktivitas' => "Menugaskan Tim " . ($pekerjaTarget->nama_lengkap ?? 'UPTD') . " untuk Laporan #" . $laporan->id_laporan,
            'kategori'  => 'laporan_bidang',
            'user_id'   => $user->id
        ]);

        return redirect()->route('admin_bidang.laporan')->with('sukses', 'Laporan berhasil diproses! Pekerja telah ditugaskan ke lokasi.');
    }

    /**
     * FUNGSI BARU 2: Kembalikan Laporan ke Admin Universal (Pusat)
     */
    public function kembalikanPusat(Request $request, $id)
    {
        $user = Auth::user();
        $namaBidangAdmin = $user->bidang->nama_bidang ?? '';

        $laporan = LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)->findOrFail($id);

        $alasan = $request->alasan_pengembalian ?? 'Tidak ada alasan.';

        // Ubah status jadi dikembalikan, dan kosongkan kategori bidang agar kembali ditangani Admin Universal
        $laporan->update([
            'status' => 'dikembalikan',
            'kategori_bidang' => null,
            'catatan_disposisi' => "Dikembalikan oleh Bidang " . $namaBidangAdmin . ". Alasan: " . $alasan
        ]);

        // CATAT AKTIVITAS KE SISTEM LOG
        LogAktivitas::create([
            'aktivitas' => "Mengembalikan Laporan #" . $laporan->id_laporan . " ke Admin Universal.",
            'kategori'  => 'laporan_bidang',
            'user_id'   => $user->id
        ]);

        return redirect()->route('admin_bidang.laporan')->with('sukses', 'Laporan berhasil dikembalikan ke Admin Universal Pusat.');
    }

    /**
     * FUNGSI BARU 3: Batalkan Penugasan Pekerja
     */
    public function batalkanTugas(Request $request, $id)
    {
        $user = Auth::user();
        $namaBidangAdmin = $user->bidang->nama_bidang ?? '';

        $laporan = LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)->findOrFail($id);

        $alasan = $request->alasan_pembatalan ?? 'Dibatalkan oleh Admin Bidang.';

        // Kembalikan status jadi diteruskan (standby di admin bidang), copot pekerjanya
        $laporan->update([
            'status' => 'diteruskan',
            'id_pekerja' => null,
            'prioritas' => null,
            'instruksi_tambahan' => "TUGAS DIBATALKAN: " . $alasan
        ]);

        // Jika kamu menggunakan tabel penugasan_pekerja terpisah, batalkan juga statusnya di sana
        PenugasanPekerja::where('id_laporan', $laporan->id)->update([
            'status_tugas' => 'dibatalkan',
            'instruksi_tambahan' => "TUGAS DIBATALKAN: " . $alasan
        ]);

        // CATAT AKTIVITAS KE SISTEM LOG
        LogAktivitas::create([
            'aktivitas' => "Membatalkan penugasan pekerja untuk Laporan #" . $laporan->id_laporan,
            'kategori'  => 'laporan_bidang',
            'user_id'   => $user->id
        ]);

        return redirect()->route('admin_bidang.laporan')->with('sukses', 'Penugasan pekerja berhasil dibatalkan.');
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

    /**
     * Validasi hasil survei lapangan dari UPTD
     */
    public function validasiSurvei(Request $request, $id)
    {
        $request->validate([
            'aksi' => 'required|in:acc,tunda,tolak',
            'catatan' => 'required_if:aksi,tunda,tolak|nullable|string',
        ]);

        $user = Auth::user();
        $namaBidangAdmin = $user->bidang->nama_bidang ?? '';

        $laporan = LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)->findOrFail($id);
        $penugasan = PenugasanPekerja::where('id_laporan', $laporan->id)->latest()->firstOrFail();

        $aksi = $request->aksi;
        $catatan = $request->catatan;

        if ($aksi === 'acc') {
            $laporan->update(['status' => 'proses']);
            $penugasan->update(['status_tugas' => 'dikerjakan']);
            $msg = 'Hasil survei disetujui! Status laporan diubah menjadi PROSES perbaikan.';
        } elseif ($aksi === 'tunda') {
            $laporan->update(['status' => 'ditunda']);
            $penugasan->update([
                'status_tugas' => 'ditunda',
                'alasan_penundaan' => $catatan
            ]);
            $msg = 'Pengerjaan ditunda sementara.';
        } else {
            $laporan->update([
                'status' => 'ditolak',
                'alasan_penolakan' => $catatan
            ]);
            $penugasan->update([
                'status_tugas' => 'dibatalkan',
                'alasan_penundaan' => 'Ditolak saat validasi survei: ' . $catatan
            ]);
            $msg = 'Laporan telah ditolak.';
        }

        // Catat ke log aktivitas
        LogAktivitas::create([
            'aktivitas' => "Melakukan validasi survei untuk Laporan #" . $laporan->id_laporan . " (Aksi: " . strtoupper($aksi) . ")",
            'kategori'  => 'laporan_bidang',
            'user_id'   => $user->id
        ]);

        return redirect()->route('admin_bidang.laporan.detail', $id)->with('sukses', $msg);
    }

    /**
     * Setujui hasil pekerjaan akhir dari UPTD
     */
    public function setujuiProgres($id)
    {
        $user = Auth::user();
        $namaBidangAdmin = $user->bidang->nama_bidang ?? '';

        $laporan = LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)->findOrFail($id);
        $penugasan = PenugasanPekerja::where('id_laporan', $laporan->id)->latest()->firstOrFail();

        $laporan->update(['status' => 'selesai']);
        $penugasan->update([
            'status_tugas' => 'selesai',
            'progres_persen' => 100
        ]);

        LogAktivitas::create([
            'aktivitas' => "Menyetujui progres akhir Laporan #" . $laporan->id_laporan . ". Perbaikan selesai.",
            'kategori'  => 'laporan_bidang',
            'user_id'   => $user->id
        ]);

        return redirect()->route('admin_bidang.laporan.detail', $id)->with('sukses', 'Pekerjaan perbaikan disetujui dan laporan dinyatakan SELESAI!');
    }

    /**
     * Tolak progres akhir dari UPTD dan minta revisi
     */
    public function tolakProgres(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string',
        ]);

        $user = Auth::user();
        $namaBidangAdmin = $user->bidang->nama_bidang ?? '';

        $laporan = LaporanKeluhan::where('kategori_bidang', $namaBidangAdmin)->findOrFail($id);
        $penugasan = PenugasanPekerja::where('id_laporan', $laporan->id)->latest()->firstOrFail();

        $laporan->update(['status' => 'revisi']);
        $penugasan->update([
            'status_tugas' => 'revisi',
            'catatan_revisi' => $request->alasan_penolakan
        ]);

        LogAktivitas::create([
            'aktivitas' => "Menolak progres akhir & meminta revisi untuk Laporan #" . $laporan->id_laporan,
            'kategori'  => 'laporan_bidang',
            'user_id'   => $user->id
        ]);

        return redirect()->route('admin_bidang.laporan.detail', $id)->with('sukses', 'Progres ditolak. Tim UPTD telah diminta melakukan revisi.');
    }
}
