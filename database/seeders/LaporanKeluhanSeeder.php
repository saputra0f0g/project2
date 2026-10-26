<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanKeluhan;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;
use Carbon\Carbon;

class LaporanKeluhanSeeder extends Seeder
{
    public function run()
    {
        $this->call(LaporanJalanSeeder::class);
    }
}
