<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiProgresPekerja extends Model
{
    use HasFactory;

    protected $table = 'bukti_progres_pekerja';

    protected $fillable = [
        'id_penugasan',
        'file_path',
        'tipe_file',
        'keterangan',
    ];

    // Relasi ke PenugasanPekerja
    public function penugasan()
    {
        return $this->belongsTo(PenugasanPekerja::class, 'id_penugasan');
    }
}
