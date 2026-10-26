<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanKeluhan;
use App\Services\WebPushNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * LaporanController — API untuk pelaporan infrastruktur Jalan
 * ─────────────────────────────────────────────────────────────────────────────
 * Role:
 *   - masyarakat : submit laporan, lihat histori & status laporan sendiri
 *   - admin_jalan: lihat semua laporan, update status laporan
 */
class LaporanController extends Controller
{
    // Status yang valid (sesuai enum di DB)
    private const VALID_STATUS = [
        'menunggu',
        'diverifikasi',
        'dalam_perbaikan',
        'selesai',
        'ditolak',
    ];

    // ─── MASYARAKAT ──────────────────────────────────────────────────────────

    /**
     * GET /api/laporan
     * Histori laporan milik user yang login (Masyarakat).
     * Support filter: ?status=, ?search=, ?per_page=
     */
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = LaporanKeluhan::where('id_pelapor', $user->id)->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('judul_laporan', 'like', "%{$keyword}%")
                  ->orWhere('deskripsi_laporan', 'like', "%{$keyword}%")
                  ->orWhere('alamat_map', 'like', "%{$keyword}%");
            });
        }

        $perPage = min((int) $request->get('per_page', 10), 50);
        $laporan = $query->paginate($perPage);

        return response()->json([
            'data' => $laporan->map(fn($l) => $this->formatLaporan($l, $request)),
            'meta' => [
                'total'        => $laporan->total(),
                'current_page' => $laporan->currentPage(),
                'last_page'    => $laporan->lastPage(),
                'per_page'     => $laporan->perPage(),
            ],
        ]);
    }

    /**
     * POST /api/laporan
     * Masyarakat submit laporan kerusakan jalan baru.
     */
    public function store(Request $request, WebPushNotifier $webPushNotifier)
    {
        // Hanya masyarakat yang bisa submit laporan
        if ($request->user()->peran !== 'masyarakat') {
            return response()->json(['message' => 'Hanya masyarakat yang dapat mengirim laporan.'], 403);
        }

        $request->validate([
            'judul'            => 'required|string|max:255',
            'deskripsi'        => 'required|string|min:10',
            'jenis_kerusakan'  => 'required|in:jalan_berlubang,jalan_retak,jalan_amblas,jalan_banjir,lampu_jalan_mati,trotoar_rusak,rambu_rusak,drainase_tersumbat,lainnya',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
            'alamat'           => 'nullable|string|max:500',
            'foto'             => 'required|array|min:1|max:5',
            'foto.*'           => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'judul.required'             => 'Judul laporan wajib diisi.',
            'judul.max'                  => 'Judul laporan maksimal 255 karakter.',
            'deskripsi.required'         => 'Deskripsi kerusakan wajib diisi.',
            'deskripsi.min'              => 'Deskripsi minimal 10 karakter.',
            'jenis_kerusakan.required'   => 'Jenis kerusakan wajib dipilih.',
            'jenis_kerusakan.in'       => 'Jenis kerusakan tidak valid.',
            'latitude.numeric'           => 'Data koordinat latitude tidak valid.',
            'latitude.between'           => 'Koordinat latitude tidak valid (-90 s/d 90).',
            'longitude.numeric'          => 'Data koordinat longitude tidak valid.',
            'longitude.between'          => 'Koordinat longitude tidak valid (-180 s/d 180).',
            'foto.max'                   => 'Maksimal 5 foto yang dapat diunggah.',
            'foto.*.image'               => 'File yang diunggah harus berupa gambar.',
            'foto.*.mimes'               => 'Format foto harus JPG, JPEG, PNG, atau WebP.',
            'foto.*.max'                 => 'Ukuran setiap foto maksimal 5 MB.',
        ]);

        $fotoBuktiPaths = collect($request->file('foto', []))
            ->map(fn ($foto) => $foto->store('laporan/jalan', 'public'))
            ->all();

        // Koordinat GPS
        $lokasi = null;
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $lokasi = $request->latitude . ',' . $request->longitude;
        }

        // Generate kode laporan: JL + YY + nomor urut 4 digit
        // Contoh: JL260001, JL260002, ...
        $count  = LaporanKeluhan::whereYear('created_at', now()->year)->count() + 1;
        $kode   = 'JL' . now()->format('y') . str_pad($count, 4, '0', STR_PAD_LEFT);

        $laporan = LaporanKeluhan::create([
            'id_laporan'        => $kode,
            'id_pelapor'        => $request->user()->id,
            'judul_laporan'     => $request->judul,
            'jenis_kerusakan'   => $request->jenis_kerusakan,
            'deskripsi_laporan' => $request->deskripsi,
            'lokasi_gps'        => $lokasi ?? 'tidak diketahui',
            'alamat_map'        => $request->alamat ?? '-',
            'foto_bukti'        => $fotoBuktiPaths[0] ?? null,
            'fotos_bukti'       => $fotoBuktiPaths,
            'foto_penanganan'   => [],
            'status'            => 'menunggu',
        ]);

        $webPushNotifier->notifyAdminsOfNewReport($laporan);

        return response()->json([
            'message' => 'Laporan berhasil dikirim. Tim Admin Jalan akan segera memverifikasinya.',
            'laporan' => $this->formatLaporan($laporan, $request),
        ], 201);
    }

    /**
     * GET /api/laporan/{id}
     * Detail satu laporan.
     * Masyarakat hanya bisa lihat laporannya sendiri.
     * Admin Jalan bisa lihat semua.
     */
    public function show(Request $request, $id)
    {
        $laporan = LaporanKeluhan::with('pelapor')->findOrFail($id);
        $user    = $request->user();

        // Masyarakat hanya boleh akses laporan miliknya sendiri
        if ($user->peran === 'masyarakat' && $laporan->id_pelapor !== $user->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $withRelasi = $user->peran === 'admin_jalan';
        return response()->json($this->formatLaporan($laporan, $request, $withRelasi));
    }

    /**
     * DELETE /api/laporan/{id}
     * Masyarakat dapat menghapus laporan yang masih berstatus 'menunggu'.
     */
    public function destroy(Request $request, $id)
    {
        $laporan = LaporanKeluhan::where('id', $id)
                                 ->where('id_pelapor', $request->user()->id)
                                 ->where('status', 'menunggu')
                                 ->firstOrFail();

        // Hapus foto dari storage
        Storage::disk('public')->delete(array_merge(
            $laporan->foto_bukti_paths,
            $laporan->foto_penanganan ?? []
        ));

        $laporan->delete();
        return response()->json(['message' => 'Laporan berhasil dihapus.']);
    }

    // ─── ADMIN JALAN ─────────────────────────────────────────────────────────

    /**
     * GET /api/admin/laporan
     * Admin Jalan: lihat semua laporan masuk.
     * Support filter: ?status=, ?jenis=, ?search=, ?per_page=
     */
    public function indexAdmin(Request $request)
    {
        $user = $request->user();
        if ($user->peran !== 'admin_jalan') {
            return response()->json(['message' => 'Akses ditolak. Khusus Admin Jalan.'], 403);
        }

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
                  ->orWhere('deskripsi_laporan', 'like', "%{$keyword}%");
            });
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        $laporan = $query->paginate($perPage);

        return response()->json([
            'data' => $laporan->map(fn($l) => $this->formatLaporan($l, $request, true)),
            'meta' => [
                'total'        => $laporan->total(),
                'current_page' => $laporan->currentPage(),
                'last_page'    => $laporan->lastPage(),
                'per_page'     => $laporan->perPage(),
            ],
        ]);
    }

    /**
     * PATCH /api/admin/laporan/{id}/status
     * Admin Jalan: update status laporan.
     *
     * Body:
     *   status  (required) : menunggu | diverifikasi | dalam_perbaikan | selesai | ditolak
     *   catatan (optional) : catatan atau alasan (wajib jika ditolak)
     */
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        if ($user->peran !== 'admin_jalan') {
            return response()->json(['message' => 'Akses ditolak. Khusus Admin Jalan.'], 403);
        }

        $request->validate([
            'status'  => 'required|in:menunggu,diverifikasi,dalam_perbaikan,selesai,ditolak',
            'catatan' => 'nullable|string|max:1000',
            'foto_penanganan' => 'nullable|array|max:10',
            'foto_penanganan.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'status.required' => 'Status laporan wajib dipilih.',
            'status.in'       => 'Status tidak valid. Pilih: menunggu, diverifikasi, dalam_perbaikan, selesai, atau ditolak.',
            'catatan.max'     => 'Catatan maksimal 1000 karakter.',
        ]);

        // Jika ditolak, catatan wajib diisi
        if ($request->status === 'ditolak' && !$request->filled('catatan')) {
            return response()->json([
                'message' => 'Alasan penolakan wajib diisi jika laporan ditolak.',
                'errors'  => ['catatan' => ['Alasan penolakan wajib diisi.']],
            ], 422);
        }

        $laporan = LaporanKeluhan::findOrFail($id);

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

        return response()->json([
            'message' => 'Status laporan berhasil diperbarui menjadi: ' . $laporan->fresh()->label_status,
            'laporan' => $this->formatLaporan($laporan->fresh(), $request, true),
        ]);
    }

    /**
     * GET /api/statistik
     * Statistik laporan.
     * - Masyarakat: statistik laporan miliknya sendiri
     * - Admin Jalan: statistik semua laporan
     */
    public function statistik(Request $request)
    {
        $user  = $request->user();
        $query = LaporanKeluhan::query();

        if ($user->peran === 'masyarakat') {
            $query->where('id_pelapor', $user->id);
        }

        $stats = [
            'total'           => (clone $query)->count(),
            'menunggu'        => (clone $query)->where('status', 'menunggu')->count(),
            'diverifikasi'    => (clone $query)->where('status', 'diverifikasi')->count(),
            'dalam_perbaikan' => (clone $query)->where('status', 'dalam_perbaikan')->count(),
            'selesai'         => (clone $query)->where('status', 'selesai')->count(),
            'ditolak'         => (clone $query)->where('status', 'ditolak')->count(),
        ];

        // Statistik tambahan untuk Admin Jalan
        if ($user->peran === 'admin_jalan') {
            $stats['per_jenis'] = LaporanKeluhan::selectRaw('jenis_kerusakan, COUNT(*) as total')
                ->groupBy('jenis_kerusakan')
                ->pluck('total', 'jenis_kerusakan');


            // Laporan 7 hari terakhir
            $stats['laporan_minggu_ini'] = (clone $query)
                ->where('created_at', '>=', now()->subDays(7))
                ->count();
        }

        return response()->json($stats);
    }

    // ─── HELPER ───────────────────────────────────────────────────────────────

    /**
     * Format laporan untuk response JSON API mobile.
     */
    private function formatLaporan(
        LaporanKeluhan $l,
        Request $request,
        bool $withRelasi = false
    ): array
    {
        $koordinat = $l->koordinat;
        $fotoBuktiPaths = $l->foto_bukti_paths;
        $fotoPenangananPaths = array_values(array_filter($l->foto_penanganan ?? []));
        $baseUrl = $request->getSchemeAndHttpHost();

        $data = [
            'id'               => $l->id,
            'kode_laporan'     => $l->id_laporan,
            'judul'            => $l->judul_laporan ?? $l->deskripsi_laporan,
            'deskripsi'        => $l->deskripsi_laporan,
            'jenis_kerusakan'  => $l->jenis_kerusakan,
            'label_jenis'      => $l->label_jenis_kerusakan,
            'latitude'         => $koordinat['lat'],
            'longitude'        => $koordinat['lng'],
            'alamat'           => $l->alamat_map,
            'foto_url'         => isset($fotoBuktiPaths[0])
                ? $this->photoUrl($fotoBuktiPaths[0], $baseUrl)
                : null,
            'foto_bukti_urls'  => array_map(
                fn ($path) => $this->photoUrl($path, $baseUrl),
                $fotoBuktiPaths
            ),
            'foto_penanganan_urls' => array_map(
                fn ($path) => $this->photoUrl($path, $baseUrl),
                $fotoPenangananPaths
            ),
            'status'           => $l->status,
            'label_status'     => $l->label_status,
            'catatan_admin'    => $l->catatan_admin,
            'alasan_penolakan' => $l->alasan_penolakan,
            'tanggal'          => $l->created_at?->format('d M Y'),
            'tanggal_update'   => $l->updated_at?->format('d M Y H:i'),
            'created_at'       => $l->created_at,
        ];

        if ($withRelasi && $l->relationLoaded('pelapor') && $l->pelapor) {
            $data['pelapor'] = [
                'id'       => $l->pelapor->id,
                'nama'     => $l->pelapor->nama_lengkap,
                'email'    => $l->pelapor->email,
                'nomor_hp' => $l->pelapor->nomor_hp,
            ];
        }

        return $data;
    }

    private function photoUrl(string $path, string $baseUrl): string
    {
        if (preg_match('~^https?://~i', $path)) {
            return $path;
        }

        return rtrim($baseUrl, '/') . '/storage/' . ltrim($path, '/');
    }
}
