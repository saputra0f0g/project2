<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenugasanPekerja extends Model
{
    use HasFactory;

    protected $table = 'penugasan_pekerja';

    protected $fillable = [
        'id_laporan',
        'id_admin_bidang',
        'id_pekerja',
        'status_validitas_survei',
        'deskripsi_temuan_survei',
        'rekomendasi_survei',
        'instruksi_tambahan',
        'alasan_penundaan',
        'catatan_revisi',
        'progres_persen',
        'status_tugas',
    ];

    // Relasi ke Laporan
    public function laporan()
    {
        return $this->belongsTo(LaporanKeluhan::class, 'id_laporan');
    }

    // Relasi ke Pekerja UPTD
    public function pekerja()
    {
        return $this->belongsTo(User::class, 'id_pekerja');
    }

    // Relasi ke Admin Bidang yang menugaskan
    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin_bidang');
    }

    // Relasi ke bukti progres yang diunggah pekerja
    public function buktiProgres()
    {
        return $this->hasMany(BuktiProgresPekerja::class, 'id_penugasan');
    }
}
