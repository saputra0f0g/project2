<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanKeluhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    /**
     * Laporan milik user yang login (Masyarakat).
     */
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = LaporanKeluhan::where('id_pelapor', $user->id)->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where('deskripsi_laporan', 'like', "%{$request->search}%");
        }

        $laporan = $query->paginate(10);

        return response()->json([
            'data' => $laporan->map(fn($l) => $this->formatLaporan($l)),
            'meta' => [
                'total'        => $laporan->total(),
                'current_page' => $laporan->currentPage(),
                'last_page'    => $laporan->lastPage(),
            ],
        ]);
    }

    /**
     * Semua laporan — khusus pegawai/admin.
     */
    public function indexSemua(Request $request)
    {
        $user = $request->user();
        if ($user->peran === 'masyarakat') {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $query = LaporanKeluhan::with(['pelapor', 'bidangTujuan'])->latest();

        if ($request->status)   $query->where('status',           $request->status);
        if ($request->bidang)   $query->where('id_bidang_tujuan', $request->bidang);
        if ($request->kategori) $query->where('kategori_bidang',  $request->kategori);
        if ($request->search) {
            $query->where('deskripsi_laporan', 'like', "%{$request->search}%");
        }

        $laporan = $query->paginate(15);

        return response()->json([
            'data' => $laporan->map(fn($l) => $this->formatLaporan($l, true)),
            'meta' => [
                'total'        => $laporan->total(),
                'current_page' => $laporan->currentPage(),
                'last_page'    => $laporan->lastPage(),
            ],
        ]);
    }

    /**
     * Detail satu laporan.
     */
    public function show(Request $request, $id)
    {
        $laporan = LaporanKeluhan::with(['pelapor', 'bidangTujuan'])->findOrFail($id);

        // Masyarakat hanya bisa lihat laporannya sendiri
        if ($request->user()->peran === 'masyarakat'
            && $laporan->id_pelapor !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($this->formatLaporan($laporan, true));
    }

    /**
     * Buat laporan baru (dengan foto & GPS).
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori'  => 'nullable|string',   // admin yg mendisposisikan
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'alamat'    => 'nullable|string',
            'foto'      => 'nullable|array|max:5',
            'foto.*'    => 'image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // Simpan foto utama (pertama)
        $fotoBuktiPath = null;
        if ($request->hasFile('foto') && count($request->file('foto')) > 0) {
            $fotoBuktiPath = $request->file('foto')[0]->store('laporan', 'public');
        }

        // Koordinat GPS: simpan sebagai "lat,lng"
        $lokasi = null;
        if ($request->latitude && $request->longitude) {
            $lokasi = $request->latitude . ',' . $request->longitude;
        }

        // Generate kode laporan unik: SK + tahun + bulan + nomor urut
        $count   = LaporanKeluhan::whereYear('created_at', now()->year)->count() + 1;
        $kode    = 'SK' . now()->format('y') . str_pad($count, 4, '0', STR_PAD_LEFT);

        $laporan = LaporanKeluhan::create([
            'id_laporan'        => $kode,
            'id_pelapor'        => $request->user()->id,
            'kategori_bidang'   => $request->kategori ?? 'umum', // admin yg menentukan nanti
            'deskripsi_laporan' => $request->judul . ' — ' . $request->deskripsi,
            'lokasi_gps'        => $lokasi ?? 'tidak diketahui',
            'alamat_map'        => $request->alamat ?? '-',
            'foto_bukti'        => $fotoBuktiPath ?? 'tidak ada',
            'status'            => 'pending',
        ]);

        return response()->json([
            'message' => 'Laporan berhasil dikirim.',
            'laporan' => $this->formatLaporan($laporan),
        ], 201);
    }

    /**
     * Update status laporan — khusus pegawai.
     */
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        if ($user->peran === 'masyarakat') {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'status'  => 'required|in:pending,diteruskan,proses,selesai,ditolak,terkendala',
            'catatan' => 'nullable|string',
        ]);

        $laporan = LaporanKeluhan::findOrFail($id);
        $laporan->update([
            'status'            => $request->status,
            'alasan_penolakan'  => $request->catatan,
        ]);

        return response()->json([
            'message' => 'Status laporan diperbarui.',
            'laporan' => $this->formatLaporan($laporan),
        ]);
    }

    /**
     * Hapus laporan — hanya bisa jika status masih pending.
     */
    public function destroy(Request $request, $id)
    {
        $laporan = LaporanKeluhan::where('id', $id)
                                 ->where('id_pelapor', $request->user()->id)
                                 ->where('status', 'pending')
                                 ->firstOrFail();

        // Hapus foto dari storage
        if ($laporan->foto_bukti && $laporan->foto_bukti !== 'tidak ada') {
            Storage::disk('public')->delete($laporan->foto_bukti);
        }

        $laporan->delete();
        return response()->json(['message' => 'Laporan berhasil dihapus.']);
    }

    public function statistik(Request $request)
    {
        $user  = $request->user();
        $query = LaporanKeluhan::query();

        if ($user->peran === 'masyarakat') {
            $query->where('id_pelapor', $user->id);
        }

        return response()->json([
            'total'      => (clone $query)->count(),
            'menunggu'   => (clone $query)->where('status', 'pending')->count(),
            'diproses'   => (clone $query)->whereIn('status', ['diteruskan', 'proses'])->count(),
            'selesai'    => (clone $query)->where('status', 'selesai')->count(),
            'ditolak'    => (clone $query)->where('status', 'ditolak')->count(),
        ]);
    }

    /**
     * Statistik laporan untuk dashboard Pegawai (dengan detail per bidang).
     */
    public function statistikPegawai(Request $request)
    {
        $user = $request->user();
        if ($user->peran === 'masyarakat') {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $query = LaporanKeluhan::query();

        return response()->json([
            'total'      => (clone $query)->whereNotIn('status', ['selesai', 'ditolak'])->count(),
            'menunggu'   => (clone $query)->where('status', 'pending')->count(),
            'diproses'   => (clone $query)->whereIn('status', ['diteruskan', 'proses'])->count(),
            'selesai'    => (clone $query)->where('status', 'selesai')->count(),
            'ditolak'    => (clone $query)->where('status', 'ditolak')->count(),
            'bidang'     => [
                'bina_marga'  => (clone $query)->whereRaw('LOWER(kategori_bidang) LIKE ?', ['%bina marga%'])->whereNotIn('status', ['selesai', 'ditolak'])->count(),
                'sda'         => (clone $query)->whereRaw('LOWER(kategori_bidang) LIKE ?', ['%air%'])->whereNotIn('status', ['selesai', 'ditolak'])->count(),
                'cipta_karya' => (clone $query)->whereRaw('LOWER(kategori_bidang) LIKE ?', ['%cipta karya%'])->whereNotIn('status', ['selesai', 'ditolak'])->count(),
            ]
        ]);
    }

    /**
     * Update status & foto progres — khusus pegawai.
     */
    public function updateProgresPegawai(Request $request, $id)
    {
        $user = $request->user();
        if ($user->peran === 'masyarakat') {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'status'  => 'required|in:pending,diteruskan,proses,selesai,ditolak,terkendala',
            'catatan' => 'nullable|string',
            'foto'    => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $laporan = LaporanKeluhan::findOrFail($id);
        
        $updateData = [
            'status'            => $request->status,
        ];
        
        if ($request->filled('catatan')) {
            $updateData['alasan_penolakan'] = $request->catatan;
        }

        // Simpan foto progres ke kolom video_bukti (karena belum ada migrasi kolom foto_progres)
        if ($request->hasFile('foto')) {
            $fotoProgresPath = $request->file('foto')->store('laporan_progres', 'public');
            $updateData['video_bukti'] = $fotoProgresPath;
        }

        $laporan->update($updateData);

        return response()->json([
            'message' => 'Progres laporan berhasil diperbarui.',
            'laporan' => $this->formatLaporan($laporan),
        ]);
    }

    // ─── HELPER: Format laporan untuk response mobile ─────────────────────────
    private function formatLaporan(LaporanKeluhan $l, bool $withRelasi = false): array
    {
        // Parse koordinat dari "lat,lng"
        $gps = explode(',', $l->lokasi_gps);

        $data = [
            'id'            => $l->id,
            'kode_laporan'  => $l->id_laporan,
            'judul'         => explode(' — ', $l->deskripsi_laporan)[0] ?? $l->deskripsi_laporan,
            'deskripsi'     => $l->deskripsi_laporan,
            'kategori'      => $l->kategori_bidang,
            'latitude'      => isset($gps[0]) ? (float)$gps[0] : null,
            'longitude'     => isset($gps[1]) ? (float)$gps[1] : null,
            'alamat'        => $l->alamat_map,
            'foto_url'      => ($l->foto_bukti && $l->foto_bukti !== 'tidak ada')
                               ? asset('storage/' . $l->foto_bukti)
                               : null,
            'foto_progres_url' => ($l->video_bukti && $l->video_bukti !== 'tidak ada')
                               ? asset('storage/' . $l->video_bukti)
                               : null,
            'status'        => $this->mapStatus($l->status),
            'status_raw'    => $l->status,
            'catatan'       => $l->alasan_penolakan,
            'tanggal'       => $l->created_at?->format('d M Y'),
            'created_at'    => $l->created_at,
        ];

        if ($withRelasi && $l->relationLoaded('pelapor') && $l->pelapor) {
            $data['pelapor'] = [
                'id'    => $l->pelapor->id,
                'nama'  => $l->pelapor->nama_lengkap,
                'email' => $l->pelapor->email,
            ];
        }

        return $data;
    }

    // Map status DB → status mobile
    private function mapStatus(string $status): string
    {
        return match ($status) {
            'pending'      => 'Menunggu',
            'diteruskan'   => 'Diproses',
            'proses'       => 'Diproses',
            'selesai'      => 'Selesai',
            'ditolak'      => 'Ditolak',
            'terkendala'   => 'Terkendala',
            default        => $status,
        };
    }
}
