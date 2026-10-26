<?php

namespace App\Http\Controllers\AdminJalan;

use App\Http\Controllers\Controller;
use App\Models\LaporanKeluhan;
use Illuminate\Http\Request;

/**
 * LaporanController (Admin Jalan) — Dashboard Web
 * ─────────────────────────────────────────────────────────────────────────────
 * Mengelola laporan kerusakan jalan:
 *   - Daftar semua laporan dengan filter
 *   - Detail laporan
 *   - Update status laporan (verifikasi, proses, selesai, tolak)
 *   - Ekspor laporan
 */
class LaporanController extends Controller
{
    /**
     * Daftar semua laporan masuk.
     */
    public function indeks(Request $request)
    {
        $query = LaporanKeluhan::with('pelapor')->latest();

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter jenis kerusakan
        if ($request->filled('jenis')) {
            $query->where('jenis_kerusakan', $request->jenis);
        }

        // Pencarian
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('judul_laporan', 'like', "%{$keyword}%")
                  ->orWhere('id_laporan', 'like', "%{$keyword}%")
                  ->orWhere('alamat_map', 'like', "%{$keyword}%")
                  ->orWhere('deskripsi_laporan', 'like', "%{$keyword}%")
                  ->orWhereHas('pelapor', function ($uq) use ($keyword) {
                      $uq->where('nama_lengkap', 'like', "%{$keyword}%")
                         ->orWhere('email', 'like', "%{$keyword}%");
                  });
            });
        }

        $laporan = $query->paginate(15)->withQueryString();

        // Data untuk filter dropdown
        $statusOptions = [
            'menunggu'        => 'Menunggu',
            'diverifikasi'    => 'Diverifikasi',
            'dalam_perbaikan' => 'Dalam Perbaikan',
            'selesai'         => 'Selesai',
            'ditolak'         => 'Ditolak',
        ];

        $jenisOptions = LaporanKeluhan::JENIS_KERUSAKAN;

        return view('admin_jalan.laporan.indeks', compact(
            'laporan', 'statusOptions', 'jenisOptions'
        ));
    }

    /**
     * Detail satu laporan.
     */
    public function detail($id)
    {
        $laporan = LaporanKeluhan::with('pelapor')->findOrFail($id);

        $statusOptions = [
            'menunggu'        => 'Menunggu',
            'diverifikasi'    => 'Diverifikasi',
            'dalam_perbaikan' => 'Dalam Perbaikan',
            'selesai'         => 'Selesai',
            'ditolak'         => 'Ditolak',
        ];

        return view('admin_jalan.laporan.detail', compact('laporan', 'statusOptions'));
    }

    /**
     * POST /admin-jalan/laporan/update-status/{id}
     * Admin Jalan mengubah status laporan.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'  => 'required|in:menunggu,diverifikasi,dalam_perbaikan,selesai,ditolak',
            'catatan' => 'nullable|string|max:1000',
            'foto_penanganan' => 'nullable|array|max:10',
            'foto_penanganan.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in'       => 'Status tidak valid.',
            'catatan.max'     => 'Catatan maksimal 1000 karakter.',
        ]);

        // Validasi: alasan penolakan wajib jika status = ditolak
        if ($request->status === 'ditolak' && !$request->filled('catatan')) {
            return back()->withErrors(['catatan' => 'Alasan penolakan wajib diisi.'])->withInput();
        }

        $laporan = LaporanKeluhan::findOrFail($id);
        $statusLama = $laporan->status;

        $updateData = ['status' => $request->status];
        if ($request->hasFile('foto_penanganan')) {
            $newPhotos = collect($request->file('foto_penanganan'))
                ->map(fn ($foto) => $foto->store('laporan/penanganan', 'public'))
                ->all();
            $updateData['foto_penanganan'] = array_values(array_merge(
                $laporan->foto_penanganan ?? [],
                $newPhotos
            ));
        }

        if ($request->status === 'ditolak') {
            $updateData['alasan_penolakan'] = $request->catatan;
        } elseif ($request->filled('catatan')) {
            $updateData['catatan_admin'] = $request->catatan;
        }

        $laporan->update($updateData);
        $labelBaru = $laporan->fresh()->label_status;

        return redirect()
            ->route('admin_jalan.laporan.detail', $id)
            ->with('success', "Status laporan {$laporan->id_laporan} berhasil diubah menjadi {$labelBaru}.");
    }

    /**
     * GET /admin-jalan/laporan/ekspor-csv
     * Ekspor semua laporan ke CSV.
     */
    public function eksporCsv(Request $request)
    {
        $query = LaporanKeluhan::with('pelapor')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $laporan = $query->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan_jalan_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($laporan) {
            $file = fopen('php://output', 'w');
            // BOM untuk Excel agar bisa baca UTF-8
            fputs($file, "\xEF\xBB\xBF");

            // Excel with Indonesian regional settings expects semicolon-separated fields.
            fputcsv($file, [
                'Kode Laporan', 'Tanggal', 'Nama Pelapor', 'Telepon Pelapor',
                'Judul', 'Jenis Kerusakan',
                'Alamat', 'Koordinat GPS', 'Status', 'Catatan Admin',
            ], ';', '"', '', "\r\n");

            foreach ($laporan as $l) {
                fputcsv($file, [
                    $l->id_laporan,
                    $l->created_at?->format('d/m/Y H:i'),
                    $l->pelapor?->nama_lengkap ?? '-',
                    $l->pelapor?->nomor_hp ?? '-',
                    $l->judul_laporan ?? $l->deskripsi_laporan,
                    LaporanKeluhan::JENIS_KERUSAKAN[$l->jenis_kerusakan] ?? $l->jenis_kerusakan,
                    $l->alamat_map,
                    $l->lokasi_gps,
                    $l->label_status,
                    $l->catatan_admin ?? $l->alasan_penolakan ?? '-',
                ], ';', '"', '', "\r\n");
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
