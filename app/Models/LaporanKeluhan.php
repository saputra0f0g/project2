<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKeluhan extends Model
{
    use HasFactory;

    protected $table = 'laporan_keluhan';

    /**
     * Status laporan yang tersedia (sesuai spesifikasi baru).
     */
    const STATUS_MENUNGGU       = 'menunggu';
    const STATUS_DIVERIFIKASI   = 'diverifikasi';
    const STATUS_DALAM_PERBAIKAN = 'dalam_perbaikan';
    const STATUS_SELESAI        = 'selesai';
    const STATUS_DITOLAK        = 'ditolak';

    /**
     * Jenis kerusakan jalan yang dapat dilaporkan.
     */
    const JENIS_KERUSAKAN = [
        'jalan_berlubang'   => 'Jalan Berlubang',
        'jalan_retak'       => 'Jalan Retak / Permukaan Tidak Rata',
        'jalan_amblas'      => 'Jalan Amblas / Longsor',
        'jalan_banjir'      => 'Genangan Air / Banjir di Jalan',
        'lampu_jalan_mati'  => 'Lampu Jalan Mati',
        'trotoar_rusak'     => 'Trotoar / Bahu Jalan Rusak',
        'rambu_rusak'       => 'Rambu Lalu Lintas Rusak / Hilang',
        'drainase_tersumbat'=> 'Drainase / Got Tersumbat',
        'lainnya'           => 'Lainnya',
    ];

    protected $attributes = [
        'kategori_bidang' => 'Jalan',
    ];

    protected $fillable = [
        'id_laporan',           // Kode unik: JL + tahun + nomor urut (contoh: JL260001)
        'id_pelapor',           // FK ke users
        'kategori_bidang',      // Kompatibilitas database skema lama ('Jalan')
        'judul_laporan',        // Judul singkat dari pelapor
        'jenis_kerusakan',      // Enum jenis kerusakan jalan
        'tingkat_kerusakan',    // ringan | sedang | berat
        'deskripsi_laporan',    // Deskripsi lengkap
        'lokasi_gps',           // Format: "latitude,longitude"
        'alamat_map',           // Alamat tekstual
        'foto_bukti',           // Path foto bukti (storage)
        'fotos_bukti',          // Semua path foto bukti pelapor
        'foto_penanganan',      // Foto survey dan hasil penanganan admin/petugas
        'status',               // menunggu | diverifikasi | dalam_perbaikan | selesai | ditolak
        'catatan_admin',        // Catatan dari admin jalan
        'alasan_penolakan',     // Alasan jika ditolak
    ];

    protected $casts = [
        'fotos_bukti' => 'array',
        'foto_penanganan' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /** User masyarakat yang membuat laporan */
    public function pelapor()
    {
        return $this->belongsTo(User::class, 'id_pelapor', 'id');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeMenunggu($query)
    {
        return $query->where('status', self::STATUS_MENUNGGU);
    }

    public function scopeAktif($query)
    {
        return $query->whereNotIn('status', [self::STATUS_SELESAI, self::STATUS_DITOLAK]);
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    /** Label status yang ramah tampilan */
    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'menunggu'        => 'Menunggu',
            'diverifikasi'    => 'Diverifikasi',
            'dalam_perbaikan' => 'Dalam Perbaikan',
            'selesai'         => 'Selesai',
            'ditolak'         => 'Ditolak',
            default           => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /** Warna badge untuk status (Tailwind) */
    public function getBadgeStatusAttribute(): string
    {
        return match ($this->status) {
            'menunggu'        => 'yellow',
            'diverifikasi'    => 'blue',
            'dalam_perbaikan' => 'orange',
            'selesai'         => 'green',
            'ditolak'         => 'red',
            default           => 'gray',
        };
    }

    /** Kelas badge status lengkap untuk dashboard Admin Jalan */
    public function getKelasBadgeStatusAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU         => 'bg-amber-100 text-amber-700',
            self::STATUS_DIVERIFIKASI     => 'bg-blue-100 text-blue-700',
            self::STATUS_DALAM_PERBAIKAN  => 'bg-orange-100 text-orange-700',
            self::STATUS_SELESAI          => 'bg-emerald-100 text-emerald-700',
            self::STATUS_DITOLAK          => 'bg-red-100 text-red-700',
            default                       => 'bg-gray-100 text-gray-600',
        };
    }

    /** Kelas badge tingkat kerusakan */
    public function getKelasBadgeTingkatAttribute(): string
    {
        return match ($this->tingkat_kerusakan) {
            'ringan' => 'bg-emerald-100 text-emerald-700',
            'sedang' => 'bg-amber-100 text-amber-700',
            'berat'  => 'bg-red-100 text-red-700',
            default  => 'bg-gray-100 text-gray-600',
        };
    }

    /** Label jenis kerusakan */
    public function getLabelJenisKerusakanAttribute(): string
    {
        return self::JENIS_KERUSAKAN[$this->jenis_kerusakan] ?? $this->jenis_kerusakan;
    }

    /** URL foto bukti */
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto_bukti ? asset('storage/' . $this->foto_bukti) : null;
    }

    /** Semua foto bukti dari pelapor, termasuk laporan lama dengan satu foto. */
    public function getFotoBuktiPathsAttribute(): array
    {
        if (is_array($this->fotos_bukti) && $this->fotos_bukti !== []) {
            return array_values(array_filter($this->fotos_bukti));
        }

        if (!$this->foto_bukti) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $this->foto_bukti))));
    }

    public function getFotoBuktiUrlsAttribute(): array
    {
        return array_map(fn ($path) => $this->photoUrl($path), $this->foto_bukti_paths);
    }

    /** Foto survey/penanganan yang dapat ditampilkan sebagai hasil kerja. */
    public function getFotoPenangananUrlsAttribute(): array
    {
        return array_map(
            fn ($path) => $this->photoUrl($path),
            array_values(array_filter($this->foto_penanganan ?? []))
        );
    }

    private function photoUrl(string $path): string
    {
        return preg_match('~^https?://~i', $path)
            ? $path
            : asset('storage/' . $path);
    }

    /** Parse koordinat GPS → [lat, lng] */
    public function getKoordinatAttribute(): array
    {
        if (!$this->lokasi_gps || $this->lokasi_gps === 'tidak diketahui') {
            return ['lat' => null, 'lng' => null];
        }
        $parts = explode(',', $this->lokasi_gps, 2);
        return [
            'lat' => isset($parts[0]) ? (float) trim($parts[0]) : null,
            'lng' => isset($parts[1]) ? (float) trim($parts[1]) : null,
        ];
    }
}
