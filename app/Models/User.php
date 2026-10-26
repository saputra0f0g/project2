<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasPushSubscriptions, Notifiable;

    /**
     * Kolom yang dapat diisi secara massal.
     * Role disederhanakan: hanya 'masyarakat' & 'admin_jalan'.
     */
    protected $fillable = [
        'nama_lengkap',
        'username',
        'email',
        'nomor_hp',
        'password',
        'peran',          // 'masyarakat' | 'admin_jalan'
        'status_akun',    // 'aktif' | 'non-aktif'
        'foto_profil',
        'terakhir_login',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'terakhir_login' => 'datetime',
        ];
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    /** Hanya user masyarakat */
    public function scopeMasyarakat($query)
    {
        return $query->where('peran', 'masyarakat');
    }

    /** Hanya admin jalan */
    public function scopeAdminJalan($query)
    {
        return $query->where('peran', 'admin_jalan');
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    public function isAdminJalan(): bool
    {
        return $this->peran === 'admin_jalan';
    }

    public function isMasyarakat(): bool
    {
        return $this->peran === 'masyarakat';
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /** Semua laporan yang dibuat oleh user ini */
    public function laporan()
    {
        return $this->hasMany(LaporanKeluhan::class, 'id_pelapor');
    }
}
