<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKeluhan extends Model
{
    use HasFactory;

    protected $table = 'laporan_keluhan';

    protected $fillable = [
        'id_laporan',
        'id_pelapor',
        'kategori_bidang',
        'deskripsi_laporan',
        'lokasi_gps',
        'alamat_map',
        'foto_bukti',
        'video_bukti',
        'status',
        'id_bidang_tujuan',
        'catatan_disposisi',
        'alasan_penolakan',
    ];

    // Relasi ke User (Masyarakat yang melapor)
    public function pelapor()
    {
        // Menghubungkan id_pelapor di tabel ini dengan id di tabel User
        return $this->belongsTo(User::class, 'id_pelapor', 'id');
    }

    // Relasi ke Bidang (Tujuan disposisi)
    public function bidangTujuan()
    {
        return $this->belongsTo(Bidang::class, 'id_bidang_tujuan');
    }

    public function pekerja()
    {
        // Menyambungkan id_pekerja di tabel laporan dengan id di tabel users
        return $this->belongsTo(User::class, 'id_pekerja', 'id');
    }

    // Relasi ke PenugasanPekerja
    public function penugasan()
    {
        return $this->hasOne(PenugasanPekerja::class, 'id_laporan')->latestOfMany();
    }
}
