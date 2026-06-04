<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BuktiProgresPekerja;
use App\Models\LaporanKeluhan;
use App\Models\PenugasanPekerja;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class PenugasanController extends Controller
{
    /**
     * Daftar tugas milik pekerja yang sedang login (ringkas, tanpa pagination).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $tugas = PenugasanPekerja::with(['laporan', 'buktiProgres'])
            ->where('id_pekerja', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($t) => $this->formatTugas($t));

        return response()->json(['data' => $tugas]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET TUGAS PEKERJA (Dedicated endpoint — lengkap dengan filter & pagination)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Mengembalikan daftar tugas milik Pegawai UPTD yang sedang login.
     *
     * Endpoint  : GET /api/pekerja/tugas
     * Middleware: auth:sanctum
     *
     * Query params (opsional):
     *   status  : Filter berdasarkan status_tugas
     *             (ditugaskan|survei_selesai|ditunda|dikerjakan|
     *              menunggu_review|revisi|selesai|terkendala)
     *   per_page: Jumlah item per halaman (default: 10)
     *
     * Response JSON:
     * {
     *   "success": true,
     *   "message": "Daftar tugas berhasil diambil.",
     *   "data": [ { ...tugas... } ],
     *   "meta": { "total", "per_page", "current_page", "last_page" }
     * }
     */
    public function getTugasPekerja(Request $request)
    {
        // 1. Guard: pastikan user sudah login (via Sanctum)
        /** @var \App\Models\User $pekerja */
        $pekerja = auth()->user();

        // 2. Bangun query — filter id_pekerja ke user yang login
        //    Eager load relasi 'laporan' beserta sub-relasi 'pelapor' & 'bidangTujuan'
        //    agar tidak terjadi N+1 query problem
        $query = PenugasanPekerja::with([
                'laporan',
                'laporan.pelapor',
                'laporan.bidangTujuan',
                'buktiProgres',
            ])
            ->where('id_pekerja', $pekerja->id);

        // 3. Filter opsional berdasarkan status_tugas
        $statusValid = [
            'ditugaskan', 'survei_selesai', 'ditunda',
            'dikerjakan', 'menunggu_review', 'revisi',
            'selesai', 'terkendala',
        ];

        if ($request->filled('status') && in_array($request->status, $statusValid)) {
            $query->where('status_tugas', $request->status);
        }

        // 4. Urutkan terbaru di atas & paginate
        $perPage   = (int) $request->input('per_page', 10);
        $penugasan = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // 5. Format setiap item ke struktur yang rapi
        $data = $penugasan->getCollection()->map(
            fn($t) => $this->formatTugasLengkap($t)
        );

        // 6. Kembalikan response JSON RESTful
        return response()->json([
            'success' => true,
            'message' => 'Daftar tugas berhasil diambil.',
            'data'    => $data,
            'meta'    => [
                'total'        => $penugasan->total(),
                'per_page'     => $penugasan->perPage(),
                'current_page' => $penugasan->currentPage(),
                'last_page'    => $penugasan->lastPage(),
            ],
        ]);
    }

    /**
     * Detail satu penugasan.
     */
    public function show(Request $request, $id)
    {
        $penugasan = PenugasanPekerja::with([
                'laporan',
                'laporan.pelapor',
                'laporan.bidangTujuan',
                'buktiProgres',
            ])
            ->where('id_pekerja', $request->user()->id)
            ->findOrFail($id);

        return response()->json($this->formatTugasLengkap($penugasan));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUBMIT SURVEI
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Pekerja mengirimkan hasil survei lapangan.
     *
     * Endpoint  : POST /api/penugasan/{id}/survei
     * Middleware: auth:sanctum, role:pekerja
     *
     * Body (JSON / multipart):
     *   status_validitas_survei  : required | in:valid,tidak_valid
     *   deskripsi_temuan_survei  : required | string
     *   rekomendasi_survei       : nullable | string
     */
    public function submitSurvei(Request $request, $id)
    {
        // 1. Ambil penugasan & pastikan milik pekerja yang login
        $penugasan = PenugasanPekerja::where('id_pekerja', $request->user()->id)
            ->findOrFail($id);

        // 2. Validasi input
        $validated = $request->validate([
            'status_validitas_survei' => 'required|in:valid,tidak_valid',
            'deskripsi_temuan_survei' => 'required|string',
            'rekomendasi_survei'      => 'nullable|string',
        ]);

        // 3. Simpan data survei & ubah status_tugas → survei_selesai
        $penugasan->update([
            'status_validitas_survei' => $validated['status_validitas_survei'],
            'deskripsi_temuan_survei' => $validated['deskripsi_temuan_survei'],
            'rekomendasi_survei'      => $validated['rekomendasi_survei'] ?? null,
            'status_tugas'            => 'survei_selesai',
        ]);

        // Sinkronisasi status LaporanKeluhan menjadi 'menunggu_validasi'
        $laporan = $penugasan->laporan;
        if ($laporan) {
            $laporan->update(['status' => 'menunggu_validasi']);
        }

        return response()->json([
            'message'   => 'Hasil survei berhasil dikirimkan.',
            'penugasan' => $this->formatTugasLengkap($penugasan->fresh([
                'laporan',
                'laporan.pelapor',
                'laporan.bidangTujuan',
                'buktiProgres'
            ])),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPLOAD BUKTI PROGRES
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Pekerja mengunggah file bukti progres (foto / video).
     *
     * Endpoint  : POST /api/penugasan/{id}/progres
     * Middleware: auth:sanctum, role:pekerja
     *
     * Body (multipart/form-data):
     *   file       : required | file | mimes:jpg,jpeg,png,mp4,mov | max:20480 (20 MB)
     *   tipe_file  : nullable | in:foto,video  (auto-detect jika tidak dikirim)
     *   keterangan : nullable | string
     *
     * Constraint : Maksimal 5 file per penugasan (foto + video dihitung bersama).
     */
    public function uploadProgres(Request $request, $id)
    {
        // 1. Ambil penugasan & pastikan milik pekerja yang login
        $penugasan = PenugasanPekerja::where('id_pekerja', $request->user()->id)
            ->findOrFail($id);

        // 2. Validasi input
        $request->validate([
            'file'       => 'required|file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
            'tipe_file'  => 'nullable|in:foto,video',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // 3. Cek batas maksimal 5 file per penugasan
        $jumlahFileSaatIni = BuktiProgresPekerja::where('id_penugasan', $penugasan->id)->count();

        if ($jumlahFileSaatIni >= 5) {
            return response()->json([
                'message' => 'Batas maksimal unggahan adalah 5 file per penugasan. '
                           . 'Saat ini sudah ada ' . $jumlahFileSaatIni . ' file.',
            ], 422);
        }

        // 4. Tentukan tipe file (dari request atau auto-detect dari ekstensi)
        $uploadedFile = $request->file('file');
        $ekstensi     = strtolower($uploadedFile->getClientOriginalExtension());
        $tipeFile     = $request->tipe_file
                        ?? (in_array($ekstensi, ['mp4', 'mov']) ? 'video' : 'foto');

        // 5. Simpan file ke storage
        $folderPath = 'bukti_progres/' . $penugasan->id;
        $filePath   = $uploadedFile->store($folderPath, 'public');

        // 6. Buat record di tabel bukti_progres_pekerja
        $bukti = BuktiProgresPekerja::create([
            'id_penugasan' => $penugasan->id,
            'file_path'    => $filePath,
            'tipe_file'    => $tipeFile,
            'keterangan'   => $request->keterangan,
        ]);

        return response()->json([
            'message' => 'File bukti progres berhasil diunggah.',
            'bukti'   => [
                'id'          => $bukti->id,
                'file_url'    => asset('storage/' . $bukti->file_path),
                'tipe_file'   => $bukti->tipe_file,
                'keterangan'  => $bukti->keterangan,
                'uploaded_at' => $bukti->created_at?->format('d M Y H:i'),
            ],
            'sisa_slot' => 5 - ($jumlahFileSaatIni + 1),
        ], 201);
    }

    /**
     * Update status dan progres dari penugasan (khusus pekerja).
     */
    public function updateStatus(Request $request, $id)
    {
        $penugasan = PenugasanPekerja::where('id_pekerja', $request->user()->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:ditugaskan,survei_selesai,ditunda,dikerjakan,menunggu_review,revisi,selesai,terkendala',
            'progres_persen' => 'nullable|integer|min:0|max:100',
            'alasan_penundaan' => 'nullable|string',
        ]);

        $statusTugas = $validated['status'];
        // Pekerja tidak boleh langsung men-selesaikan penugasan tanpa validasi Admin Bidang
        if ($statusTugas === 'selesai') {
            $statusTugas = 'menunggu_review';
        }

        $updateData = [
            'status_tugas' => $statusTugas,
        ];

        if ($request->has('progres_persen')) {
            $updateData['progres_persen'] = $validated['progres_persen'];
        } else {
            // Auto-set progres berdasarkan status jika tidak dikirim
            if ($statusTugas === 'menunggu_review') {
                $updateData['progres_persen'] = 100;
            } elseif ($statusTugas === 'dikerjakan') {
                $updateData['progres_persen'] = max($penugasan->progres_persen, 30);
            }
        }

        if ($statusTugas === 'ditunda' || $statusTugas === 'terkendala') {
            if ($request->filled('alasan_penundaan')) {
                $updateData['alasan_penundaan'] = $validated['alasan_penundaan'];
            }
        }

        $penugasan->update($updateData);

        // Sinkronisasi status LaporanKeluhan jika relevan
        $laporan = $penugasan->laporan;
        if ($laporan) {
            $statusLaporan = null;
            if ($statusTugas === 'dikerjakan') {
                $statusLaporan = 'proses';
            } elseif (in_array($statusTugas, ['survei_selesai', 'menunggu_review'])) {
                $statusLaporan = 'menunggu_validasi';
            } elseif ($statusTugas === 'terkendala') {
                $statusLaporan = 'terkendala';
            } elseif ($statusTugas === 'revisi') {
                $statusLaporan = 'revisi';
            } elseif ($statusTugas === 'ditunda') {
                $statusLaporan = 'ditunda';
            }

            if ($statusLaporan) {
                $laporan->update(['status' => $statusLaporan]);
            }
        }

        return response()->json([
            'message' => 'Status penugasan berhasil diperbarui.',
            'penugasan' => $this->formatTugasLengkap($penugasan->fresh([
                'laporan',
                'laporan.pelapor',
                'laporan.bidangTujuan',
                'buktiProgres'
            ])),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPER
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Format ringkas — dipakai oleh index(), show(), submitSurvei(), dll.
     */
    private function formatTugas(PenugasanPekerja $t, bool $withDetail = false): array
    {
        $data = [
            'id'                       => $t->id,
            'id_laporan'               => $t->id_laporan,
            'status_tugas'             => $t->status_tugas,
            'progres_persen'           => $t->progres_persen,
            'instruksi_tambahan'       => $t->instruksi_tambahan,
            'alasan_penundaan'         => $t->alasan_penundaan,
            'catatan_revisi'           => $t->catatan_revisi,
            'status_validitas_survei'  => $t->status_validitas_survei,
            'deskripsi_temuan_survei'  => $t->deskripsi_temuan_survei,
            'rekomendasi_survei'       => $t->rekomendasi_survei,
            'created_at'               => $t->created_at,
        ];

        if ($withDetail) {
            // Sertakan bukti progres beserta URL-nya
            $data['bukti_progres'] = $t->buktiProgres->map(fn($b) => [
                'id'         => $b->id,
                'file_url'   => asset('storage/' . $b->file_path),
                'tipe_file'  => $b->tipe_file,
                'keterangan' => $b->keterangan,
                'created_at' => $b->created_at?->format('d M Y H:i'),
            ]);
        }

        return $data;
    }

    /**
     * Format lengkap — dipakai oleh getTugasPekerja().
     * Menyertakan detail laporan, pelapor, lokasi, dan semua bukti progres.
     */
    private function formatTugasLengkap(PenugasanPekerja $t): array
    {
        $laporan = $t->laporan;

        // Parse koordinat GPS dari string "lat,lng"
        $koordinat = null;
        if ($laporan && str_contains((string) $laporan->lokasi_gps, ',')) {
            [$lat, $lng]  = explode(',', $laporan->lokasi_gps, 2);
            $koordinat = [
                'latitude'  => (float) trim($lat),
                'longitude' => (float) trim($lng),
            ];
        }

        return [
            // ── Identitas penugasan ──────────────────────────────────────────
            'id'              => $t->id,
            'status_tugas'    => $t->status_tugas,
            'label_status'    => $this->labelStatusTugas($t->status_tugas),
            'progres_persen'  => $t->progres_persen ?? 0,
            'ditugaskan_pada' => $t->created_at?->format('d M Y'),
            'diperbarui_pada' => $t->updated_at?->format('d M Y H:i'),

            // ── Instruksi & catatan ──────────────────────────────────────────
            'instruksi_tambahan' => $t->instruksi_tambahan,
            'alasan_penundaan'   => $t->alasan_penundaan,
            'catatan_revisi'     => $t->catatan_revisi,

            // ── Data hasil survei lapangan ───────────────────────────────────
            'survei' => [
                'status_validitas'  => $t->status_validitas_survei,
                'deskripsi_temuan' => $t->deskripsi_temuan_survei,
                'rekomendasi'       => $t->rekomendasi_survei,
            ],

            // ── Detail laporan keluhan yang ditugaskan ───────────────────────
            'laporan' => $laporan ? [
                'id'            => $laporan->id,
                'kode_laporan'  => $laporan->id_laporan,
                'judul'         => explode(' — ', (string) $laporan->deskripsi_laporan)[0]
                                    ?? $laporan->deskripsi_laporan,
                'deskripsi'     => $laporan->deskripsi_laporan,
                'kategori'      => $laporan->kategori_bidang,
                'status'        => $laporan->status,
                'alamat'        => $laporan->alamat_map,
                'koordinat'     => $koordinat,
                'foto_url'      => ($laporan->foto_bukti && $laporan->foto_bukti !== 'tidak ada')
                                    ? asset('storage/' . $laporan->foto_bukti)
                                    : null,
                'tanggal_lapor' => $laporan->created_at?->format('d M Y'),
                'pelapor'       => $laporan->relationLoaded('pelapor') && $laporan->pelapor
                                    ? [
                                        'nama'  => $laporan->pelapor->nama_lengkap,
                                        'email' => $laporan->pelapor->email,
                                    ]
                                    : null,
                'bidang'        => $laporan->relationLoaded('bidangTujuan') && $laporan->bidangTujuan
                                    ? $laporan->bidangTujuan->nama_bidang
                                    : null,
            ] : null,

            // ── Bukti progres yang sudah diunggah ───────────────────────────
            'bukti_progres' => $t->buktiProgres->map(fn($b) => [
                'id'         => $b->id,
                'file_url'   => asset('storage/' . $b->file_path),
                'tipe_file'  => $b->tipe_file,
                'keterangan' => $b->keterangan,
                'uploaded_at'=> $b->created_at?->format('d M Y H:i'),
            ])->values(),
            'total_bukti'  => $t->buktiProgres->count(),
            'sisa_slot'    => max(0, 5 - $t->buktiProgres->count()),
        ];
    }

    /**
     * Mapping status_tugas ke label teks yang ramah pengguna.
     */
    private function labelStatusTugas(string $status): string
    {
        return match ($status) {
            'ditugaskan'      => 'Ditugaskan',
            'survei_selesai'  => 'Survei Selesai',
            'ditunda'         => 'Ditunda',
            'dikerjakan'      => 'Sedang Dikerjakan',
            'menunggu_review' => 'Menunggu Review',
            'revisi'          => 'Perlu Revisi',
            'selesai'         => 'Selesai',
            'terkendala'      => 'Terkendala',
            default           => ucfirst($status),
        };
    }
}
