<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder — Seed akun default SIGAP (Fokus: Laporan Jalan)
 *
 * Role yang tersedia:
 *   - masyarakat : pengguna mobile yang mengirim laporan
 *   - admin_jalan: admin yang mengelola laporan via dashboard web
 *
 * Default credentials (UBAH SEGERA di production!):
 *   Admin Jalan : username=admin_jalan | password=AdminJalan123!
 *   Masyarakat  : username=user_test   | password=UserTest123!
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Admin Jalan (untuk akses dashboard web) ───────────────────────
        User::updateOrCreate(
            ['username' => 'admin_jalan'],
            [
                'nama_lengkap' => 'Administrator Jalan',
                'username'     => 'admin_jalan',
                'email'        => 'admin.jalan@sigap.id',
                'password'     => Hash::make('AdminJalan123!'),
                'nomor_hp'     => '081234567890',
                'peran'        => 'admin_jalan',
                'status_akun'  => 'aktif',
            ]
        );

        // ─── 2. Akun-akun Masyarakat untuk testing ───────────────────────────
        $masyarakatData = [
            [
                'username'     => 'user_test',
                'nama_lengkap' => 'Budi Santoso',
                'email'        => 'budi.test@sigap.id',
                'nomor_hp'     => '082345678901',
            ],
            [
                'username'     => 'siti_rahma',
                'nama_lengkap' => 'Siti Rahmawati',
                'email'        => 'siti.rahma@sigap.id',
                'nomor_hp'     => '082345678902',
            ],
            [
                'username'     => 'asep_sunandar',
                'nama_lengkap' => 'Asep Sunandar',
                'email'        => 'asep.sunandar@sigap.id',
                'nomor_hp'     => '082345678903',
            ],
            [
                'username'     => 'dewi_lestari',
                'nama_lengkap' => 'Dewi Lestari',
                'email'        => 'dewi.lestari@sigap.id',
                'nomor_hp'     => '082345678904',
            ],
            [
                'username'     => 'ridwan_k',
                'nama_lengkap' => 'Ridwan Kurniawan',
                'email'        => 'ridwan.k@sigap.id',
                'nomor_hp'     => '082345678905',
            ],
        ];

        foreach ($masyarakatData as $data) {
            User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'nama_lengkap' => $data['nama_lengkap'],
                    'username'     => $data['username'],
                    'email'        => $data['email'],
                    'password'     => Hash::make('UserTest123!'),
                    'nomor_hp'     => $data['nomor_hp'],
                    'peran'        => 'masyarakat',
                    'status_akun'  => 'aktif',
                ]
            );
        }

        $this->command->info('✅ UserSeeder berhasil! Akun yang dibuat:');
        $this->command->line('   → Admin Jalan : admin.jalan@sigap.id | AdminJalan123!');
        $this->command->line('   → Masyarakat  : budi.test@sigap.id   | UserTest123!');
        $this->command->line('   → + 4 akun masyarakat lainnya (password: UserTest123!)');
    }
}
