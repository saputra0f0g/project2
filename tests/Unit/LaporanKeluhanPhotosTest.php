<?php

namespace Tests\Unit;

use App\Models\LaporanKeluhan;
use Tests\TestCase;

class LaporanKeluhanPhotosTest extends TestCase
{
    public function test_all_reporter_photos_are_returned_with_legacy_fallback(): void
    {
        $laporan = new LaporanKeluhan();
        $laporan->foto_bukti = 'lama-1.jpg,lama-2.jpg';

        $this->assertSame(['lama-1.jpg', 'lama-2.jpg'], $laporan->foto_bukti_paths);

        $laporan->fotos_bukti = ['baru-1.jpg', 'baru-2.jpg'];

        $this->assertSame(['baru-1.jpg', 'baru-2.jpg'], $laporan->foto_bukti_paths);
        $this->assertSame([
            asset('storage/baru-1.jpg'),
            asset('storage/baru-2.jpg'),
        ], $laporan->foto_bukti_urls);
    }

    public function test_admin_handling_photos_are_exposed_as_urls(): void
    {
        $laporan = new LaporanKeluhan();
        $laporan->foto_penanganan = ['survey.jpg', 'perbaikan.jpg'];

        $this->assertSame([
            asset('storage/survey.jpg'),
            asset('storage/perbaikan.jpg'),
        ], $laporan->foto_penanganan_urls);
    }
}
