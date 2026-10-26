<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * SIGAP — Seed database (Fokus: Laporan Infrastruktur Jalan)
     *
     * Urutan seeder:
     *   1. UserSeeder          → buat akun admin_jalan & masyarakat test
     *   2. LaporanJalanSeeder  → buat data laporan contoh (opsional)
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,         // 1. Akun admin jalan & masyarakat
            LaporanJalanSeeder::class, // 2. Data dummy laporan kerusakan jalan
        ]);
    }
}
