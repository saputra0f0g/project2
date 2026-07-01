<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Urutan penting: Bidang & User harus ada sebelum Laporan
        $this->call([
            BidangSeeder::class,       // 1. Buat bidang PUPR
            UserSeeder::class,         // 2. Buat admin universal + staf awal
            LaporanLengkapSeeder::class, // 3. Buat 55 laporan (5 bidang × 11 status)
        ]);
    }
}
