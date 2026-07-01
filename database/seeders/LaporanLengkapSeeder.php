<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

/**
 * LaporanLengkapSeeder
 *
 * Seeder komprehensif yang mencakup:
 *   - 5 Bidang PUPR (Jalan, Jembatan, SDA, Cipta Karya, Tata Ruang)
 *   - 11 Status laporan (pending, diteruskan, dikembalikan, disurvei,
 *                        menunggu_validasi, ditolak, ditunda, proses,
 *                        terkendala, revisi, selesai)
 *   - Pelapor masyarakat (5 akun)
 *   - Admin bidang per bidang (5 akun)
 *   - Pekerja UPTD per bidang (5 akun)
 *   - Penugasan pekerja untuk setiap laporan yang sudah disurvei ke atas
 *   - Data tersebar dari Januari – Juli 2026 agar grafik statistik terisi
 *
 * Cara jalankan:
 *   php artisan db:seed --class=LaporanLengkapSeeder
 *
 * Atau tambahkan ke DatabaseSeeder dan jalankan:
 *   php artisan db:seed
 */
class LaporanLengkapSeeder extends Seeder
{
    // ─── Koordinat GPS wilayah Kab. Subang ───────────────────────────────────
    private const GPS = [
        ['lat' => '-6.5627', 'lng' => '107.7613', 'alamat' => 'Jl. Letjen Suprapto, Pasirkareumbi, Subang'],
        ['lat' => '-6.5510', 'lng' => '107.7650', 'alamat' => 'Area Sawah Dangdeur, Cigadung, Subang'],
        ['lat' => '-6.5700', 'lng' => '107.7500', 'alamat' => 'Jl. Otto Iskandardinata, Karanganyar, Subang'],
        ['lat' => '-6.5820', 'lng' => '107.7710', 'alamat' => 'Jl. Raya Cijambe, Subang'],
        ['lat' => '-6.5400', 'lng' => '107.7800', 'alamat' => 'Bantaran Sungai Ciasem, Subang'],
        ['lat' => '-6.5650', 'lng' => '107.7680', 'alamat' => 'Alun-alun Kabupaten Subang'],
        ['lat' => '-6.5900', 'lng' => '107.7400', 'alamat' => 'Jembatan Desa Cibogo, Subang'],
        ['lat' => '-6.5580', 'lng' => '107.7550', 'alamat' => 'Jl. KS Tubun, Cigadung, Subang'],
        ['lat' => '-6.5750', 'lng' => '107.7650', 'alamat' => 'Perumnas Subang'],
        ['lat' => '-6.5480', 'lng' => '107.7430', 'alamat' => 'Desa Tanjungsiang, Subang Utara'],
        ['lat' => '-6.6100', 'lng' => '107.7300', 'alamat' => 'Jl. Raya Purwakarta, Sagalaherang'],
        ['lat' => '-6.5300', 'lng' => '107.8100', 'alamat' => 'Jl. Raya Binong, Binong, Subang'],
        ['lat' => '-6.5850', 'lng' => '107.7820', 'alamat' => 'Kawasan Industri Dawuan, Subang'],
        ['lat' => '-6.5150', 'lng' => '107.7350', 'alamat' => 'Desa Pamanukan, Subang Utara'],
        ['lat' => '-6.6250', 'lng' => '107.7900', 'alamat' => 'Jl. Raya Jatisari, Ciasem, Subang'],
    ];

    // ─── Foto placeholder per kategori ───────────────────────────────────────
    private const FOTO = [
        'Jalan'       => ['jalan_berlubang.jpg', 'aspal_rusak.jpg', 'jalan_longsor.jpg'],
        'Jembatan'    => ['jembatan_retak.jpg', 'jembatan_ambles.jpg', 'jembatan_pagar_rusak.jpg'],
        'SDA'         => ['irigasi_mampet.jpg', 'tanggul_retak.jpg', 'gorong_mampet.jpg', 'banjir_jalan.jpg'],
        'Cipta Karya' => ['pju_mati.jpg', 'toilet_rusak.jpg', 'pipa_bocor.jpg', 'drainase_mampet.jpg'],
        'Tata Ruang'  => ['bangunan_liar.jpg', 'trotoar_rusak.jpg', 'taman_rusak.jpg'],
    ];

    // ─── Counter auto-increment kode laporan ─────────────────────────────────
    private int $counter = 1;

    private function kode(Carbon $tgl): string
    {
        return sprintf('LP-%s-%03d', $tgl->format('Ymd'), $this->counter++);
    }

    private function gps(int $idx): array
    {
        return self::GPS[$idx % count(self::GPS)];
    }

    private function foto(string $bidang, int $idx): string
    {
        $arr = self::FOTO[$bidang] ?? ['default.jpg'];
        return $arr[$idx % count($arr)];
    }

    // ─── MAIN RUN ─────────────────────────────────────────────────────────────
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Bersihkan tabel yang akan di-seed (urutan: anak dulu, lalu induk)
        DB::table('bukti_progres_pekerja')->truncate();
        DB::table('penugasan_pekerja')->truncate();
        DB::table('laporan_keluhan')->truncate();

        Schema::enableForeignKeyConstraints();

        // ── Ambil ID dari database ────────────────────────────────────────────
        $bidangRows   = DB::table('bidang')->orderBy('id')->get();
        $pelapors     = DB::table('users')->where('peran', 'masyarakat')->pluck('id')->toArray();
        $adminBidangs = DB::table('users')->where('peran', 'admin_bidang')->get()->keyBy('id_bidang');
        $pekerjas     = DB::table('users')->where('peran', 'pekerja_bidang')->get()->keyBy('id_bidang');

        if ($bidangRows->isEmpty()) {
            $this->command->error('Tabel bidang kosong! Jalankan BidangSeeder terlebih dahulu.');
            return;
        }

        // Jika tidak ada masyarakat, buat dummy agar seeder tetap bisa berjalan
        if (empty($pelapors)) {
            $this->command->warn('Tidak ada akun masyarakat. Membuat akun dummy pelapor...');
            $pelapors = $this->buatPelapor();
        }

        // Jika tidak ada admin bidang / pekerja, buat dummy
        [$adminBidangs, $pekerjas] = $this->pastikanStaf($bidangRows, $adminBidangs, $pekerjas);

        // ── Generate laporan per bidang ───────────────────────────────────────
        foreach ($bidangRows as $bidang) {
            $this->seedBidang($bidang, $pelapors, $adminBidangs, $pekerjas);
        }

        $total = DB::table('laporan_keluhan')->count();
        $this->command->info("✅ LaporanLengkapSeeder selesai — total {$total} laporan dibuat.");
    }

    // ─── Buat laporan untuk satu bidang ──────────────────────────────────────
    private function seedBidang(
        object $bidang,
        array  $pelapors,
        $adminBidangs,
        $pekerjas
    ): void {
        $nama = $bidang->nama_bidang;

        // Deteksi singkatan bidang untuk kategori_bidang
        $kategori = match (true) {
            str_contains($nama, 'Jalan')      => 'Jalan',
            str_contains($nama, 'Jembatan')   => 'Jembatan',
            str_contains($nama, 'SDA') ||
            str_contains($nama, 'Sumber')     => 'SDA',
            str_contains($nama, 'Cipta')      => 'Cipta Karya',
            str_contains($nama, 'Tata')       => 'Tata Ruang',
            default                           => $nama,
        };

        $adminId  = optional($adminBidangs->get($bidang->id))->id;
        $pekerjId = optional($pekerjas->get($bidang->id))->id;

        // Setiap bidang mendapat 11 laporan — satu untuk setiap status
        $this->buatLaporan($bidang->id, $kategori, $pelapors, $adminId, $pekerjId);
    }

    // ─── Buat 11 laporan (1 per status) untuk satu bidang ────────────────────
    private function buatLaporan(
        int    $bidangId,
        string $kategori,
        array  $pelapors,
        ?int   $adminId,
        ?int   $pekerjId
    ): void {
        /*
         * Alur status resmi:
         *   pending → diteruskan → disurvei → menunggu_validasi → proses → selesai
         *                                    ↘ ditunda / ditolak
         *                       ↘ dikembalikan
         *             proses → terkendala → revisi → selesai
         */
        $dataLaporan = [

            // ── 1. PENDING ────────────────────────────────────────────────────
            [
                'status'            => 'pending',
                'tanggal'           => Carbon::now()->subDays(2),
                'deskripsi'         => $this->deskripsi($kategori, 'pending'),
                'catatan_disposisi' => null,
                'alasan_penolakan'  => null,
                'penugasan'         => null,
            ],

            // ── 2. DITERUSKAN ─────────────────────────────────────────────────
            [
                'status'            => 'diteruskan',
                'tanggal'           => Carbon::now()->subDays(5),
                'deskripsi'         => $this->deskripsi($kategori, 'diteruskan'),
                'catatan_disposisi' => 'Laporan telah diverifikasi dan diteruskan ke bidang terkait untuk ditindaklanjuti.',
                'alasan_penolakan'  => null,
                'penugasan'         => null,
            ],

            // ── 3. DIKEMBALIKAN ───────────────────────────────────────────────
            [
                'status'            => 'dikembalikan',
                'tanggal'           => Carbon::now()->subDays(8),
                'deskripsi'         => $this->deskripsi($kategori, 'dikembalikan'),
                'catatan_disposisi' => 'Laporan dikembalikan karena foto bukti tidak jelas dan lokasi GPS tidak akurat. Mohon perbaiki dan kirim ulang.',
                'alasan_penolakan'  => null,
                'penugasan'         => null,
            ],

            // ── 4. DISURVEI ───────────────────────────────────────────────────
            [
                'status'            => 'disurvei',
                'tanggal'           => Carbon::now()->subDays(12),
                'deskripsi'         => $this->deskripsi($kategori, 'disurvei'),
                'catatan_disposisi' => 'Petugas UPTD sudah ditugaskan ke lapangan untuk melakukan survei awal kondisi.',
                'alasan_penolakan'  => null,
                'penugasan'         => $pekerjId ? [
                    'instruksi'      => 'Lakukan survei lapangan, dokumentasikan kondisi, dan buat laporan temuan dengan foto minimal 3 sudut.',
                    'progres'        => 15,
                    'status_tugas'   => 'ditugaskan',
                    'status_survei'  => null,
                    'deskripsi_tmn'  => null,
                    'rekomendasi'    => null,
                ] : null,
            ],

            // ── 5. MENUNGGU VALIDASI ──────────────────────────────────────────
            [
                'status'            => 'menunggu_validasi',
                'tanggal'           => Carbon::now()->subDays(16),
                'deskripsi'         => $this->deskripsi($kategori, 'menunggu_validasi'),
                'catatan_disposisi' => 'Survei lapangan telah selesai dilakukan. Menunggu validasi hasil survei dari admin bidang.',
                'alasan_penolakan'  => null,
                'penugasan'         => $pekerjId ? [
                    'instruksi'      => 'Survei selesai, mohon lengkapi laporan temuan dan foto dokumentasi survei.',
                    'progres'        => 30,
                    'status_tugas'   => 'survei_selesai',
                    'status_survei'  => null,
                    'deskripsi_tmn'  => 'Kerusakan terkonfirmasi di lapangan. Kondisi ' . $this->kondisi($kategori) . '. Perlu penanganan segera.',
                    'rekomendasi'    => 'Rekomendasikan perbaikan dengan metode ' . $this->metode($kategori) . '.',
                ] : null,
            ],

            // ── 6. DITOLAK ────────────────────────────────────────────────────
            [
                'status'            => 'ditolak',
                'tanggal'           => Carbon::now()->subDays(20),
                'deskripsi'         => $this->deskripsi($kategori, 'ditolak'),
                'catatan_disposisi' => null,
                'alasan_penolakan'  => 'Setelah verifikasi lapangan, kerusakan yang dilaporkan termasuk dalam kewenangan pemerintah desa/kelurahan, bukan Dinas PUPR Kabupaten. Silakan laporkan ke kantor desa setempat.',
                'penugasan'         => null,
            ],

            // ── 7. DITUNDA ────────────────────────────────────────────────────
            [
                'status'            => 'ditunda',
                'tanggal'           => Carbon::now()->subDays(25),
                'deskripsi'         => $this->deskripsi($kategori, 'ditunda'),
                'catatan_disposisi' => 'Penanganan ditunda karena anggaran perbaikan periode ini telah habis. Dijadwalkan ulang pada periode anggaran berikutnya.',
                'alasan_penolakan'  => null,
                'penugasan'         => $pekerjId ? [
                    'instruksi'      => 'Penanganan ditunda sementara. Tetap pantau kondisi lapangan dan laporkan jika ada perubahan signifikan.',
                    'progres'        => 20,
                    'status_tugas'   => 'ditunda',
                    'status_survei'  => 'valid',
                    'deskripsi_tmn'  => 'Kondisi ' . $this->kondisi($kategori) . '. Membutuhkan penanganan namun anggaran tidak tersedia.',
                    'rekomendasi'    => 'Pasang rambu peringatan sementara sambil menunggu anggaran tersedia.',
                ] : null,
            ],

            // ── 8. PROSES ─────────────────────────────────────────────────────
            [
                'status'            => 'proses',
                'tanggal'           => Carbon::now()->subDays(30),
                'deskripsi'         => $this->deskripsi($kategori, 'proses'),
                'catatan_disposisi' => 'Perbaikan sedang dikerjakan oleh tim UPTD. Estimasi selesai 7 hari kerja.',
                'alasan_penolakan'  => null,
                'penugasan'         => $pekerjId ? [
                    'instruksi'      => 'Laksanakan perbaikan sesuai rekomendasi survei. Update progres setiap hari. Dokumentasikan proses perbaikan.',
                    'progres'        => 55,
                    'status_tugas'   => 'dikerjakan',
                    'status_survei'  => 'valid',
                    'deskripsi_tmn'  => 'Kerusakan terkonfirmasi: ' . $this->kondisi($kategori) . '. Tim sudah siap dengan material.',
                    'rekomendasi'    => 'Gunakan metode ' . $this->metode($kategori) . ' untuk hasil maksimal.',
                ] : null,
            ],

            // ── 9. TERKENDALA ─────────────────────────────────────────────────
            [
                'status'            => 'terkendala',
                'tanggal'           => Carbon::now()->subDays(40),
                'deskripsi'         => $this->deskripsi($kategori, 'terkendala'),
                'catatan_disposisi' => 'Pengerjaan terkendala hujan deras yang terus-menerus. Tim tidak dapat bekerja di kondisi tersebut untuk keselamatan. Akan dilanjutkan setelah cuaca membaik.',
                'alasan_penolakan'  => null,
                'penugasan'         => $pekerjId ? [
                    'instruksi'      => 'Perbaiki rencana kerja dengan memperhitungkan kondisi cuaca. Koordinasikan dengan admin untuk solusi alternatif.',
                    'progres'        => 45,
                    'status_tugas'   => 'terkendala',
                    'status_survei'  => 'valid',
                    'deskripsi_tmn'  => 'Pekerjaan terhenti di progres 45%. ' . $this->kondisi($kategori) . '.',
                    'rekomendasi'    => 'Sementara pasang rambu dan barrier. Lanjutkan saat cuaca kondusif.',
                ] : null,
            ],

            // ── 10. REVISI ────────────────────────────────────────────────────
            [
                'status'            => 'revisi',
                'tanggal'           => Carbon::now()->subDays(50),
                'deskripsi'         => $this->deskripsi($kategori, 'revisi'),
                'catatan_disposisi' => 'Pekerjaan perlu direvisi. Hasil perbaikan tidak sesuai standar teknis PUPR. Tim diminta mengerjakan ulang bagian yang tidak memenuhi spesifikasi.',
                'alasan_penolakan'  => null,
                'penugasan'         => $pekerjId ? [
                    'instruksi'      => 'Perbaiki bagian yang tidak sesuai spesifikasi. Perhatikan kualitas material dan teknik pengerjaan. Laporkan kembali setelah revisi selesai.',
                    'progres'        => 75,
                    'status_tugas'   => 'revisi',
                    'status_survei'  => 'valid',
                    'deskripsi_tmn'  => 'Pekerjaan awal tidak sesuai standar. ' . $this->kondisi($kategori) . '.',
                    'rekomendasi'    => 'Revisi menggunakan material grade A. Pastikan pengawas hadir selama revisi.',
                ] : null,
            ],

            // ── 11. SELESAI ───────────────────────────────────────────────────
            [
                'status'            => 'selesai',
                'tanggal'           => Carbon::now()->subDays(60),
                'deskripsi'         => $this->deskripsi($kategori, 'selesai'),
                'catatan_disposisi' => 'Perbaikan telah selesai dan dinyatakan layak oleh tim pengawas. Infrastruktur sudah kembali berfungsi normal. Terima kasih atas laporan Anda.',
                'alasan_penolakan'  => null,
                'penugasan'         => $pekerjId ? [
                    'instruksi'      => 'Pekerjaan selesai. Lakukan pengecekan akhir (QC) dan serahkan laporan pertanggungjawaban beserta foto dokumentasi before/after.',
                    'progres'        => 100,
                    'status_tugas'   => 'selesai',
                    'status_survei'  => 'valid',
                    'deskripsi_tmn'  => 'Pekerjaan telah selesai dan diterima oleh pengawas. ' . $this->kondisi($kategori) . ' sudah diperbaiki sepenuhnya.',
                    'rekomendasi'    => 'Lakukan inspeksi rutin 3 bulan kedepan untuk memastikan tidak ada kerusakan lanjutan.',
                ] : null,
            ],
        ];

        // ── Insert laporan + penugasan ────────────────────────────────────────
        foreach ($dataLaporan as $i => $item) {
            $gps = $this->gps($i + ($bidangId * 3));

            $laporanId = DB::table('laporan_keluhan')->insertGetId([
                'id_laporan'        => $this->kode($item['tanggal']),
                'id_pelapor'        => $pelapors[array_rand($pelapors)],
                'kategori_bidang'   => $kategori,
                'deskripsi_laporan' => $item['deskripsi'],
                'lokasi_gps'        => $gps['lat'] . ', ' . $gps['lng'],
                'alamat_map'        => $gps['alamat'],
                'foto_bukti'        => $this->foto($kategori, $i),
                'status'            => $item['status'],
                'id_bidang_tujuan'  => in_array($item['status'], ['pending', 'dikembalikan'])
                                            ? null
                                            : $bidangId,
                'catatan_disposisi' => $item['catatan_disposisi'],
                'alasan_penolakan'  => $item['alasan_penolakan'],
                'created_at'        => $item['tanggal'],
                'updated_at'        => $item['tanggal']->copy()->addHours(rand(2, 48)),
            ]);

            // ── Buat penugasan jika ada ───────────────────────────────────────
            if ($item['penugasan'] && $adminId && $pekerjId) {
                $p = $item['penugasan'];
                DB::table('penugasan_pekerja')->insert([
                    'id_laporan'              => $laporanId,
                    'id_admin_bidang'         => $adminId,
                    'id_pekerja'              => $pekerjId,
                    'instruksi_tambahan'      => $p['instruksi'],
                    'progres_persen'          => $p['progres'],
                    'status_tugas'            => $p['status_tugas'],
                    'status_validitas_survei' => $p['status_survei'],
                    'deskripsi_temuan_survei' => $p['deskripsi_tmn'],
                    'rekomendasi_survei'      => $p['rekomendasi'],
                    'alasan_penundaan'        => $item['status'] === 'ditunda'
                                                    ? 'Anggaran periode berjalan habis. Dijadwalkan ulang Q3 2026.'
                                                    : null,
                    'catatan_revisi'          => $item['status'] === 'revisi'
                                                    ? 'Campuran aspal tidak sesuai spesifikasi teknis SNI 03-6753-2002. Gunakan AC-WC dengan kadar aspal 5.5%.'
                                                    : null,
                    'created_at'              => $item['tanggal']->copy()->addHours(4),
                    'updated_at'              => $item['tanggal']->copy()->addHours(rand(6, 72)),
                ]);
            }
        }
    }

    // ─── Helper: Deskripsi realistis per bidang × status ─────────────────────
    private function deskripsi(string $bidang, string $status): string
    {
        $map = [
            'Jalan' => [
                'pending'           => 'Jalan berlubang cukup dalam di ruas jalan utama, membahayakan pengendara motor terutama di malam hari saat hujan.',
                'diteruskan'        => 'Aspal mengelupas sepanjang ±80 meter akibat genangan air yang tidak mengalir dengan baik.',
                'dikembalikan'      => 'Bahu jalan longsor dan menutup sebagian jalur kendaraan, arus lalu lintas terhambat.',
                'disurvei'          => 'Jalan retak memanjang di sepanjang jalur bus kota. Retakan mencapai lebar 3–5 cm dan kedalaman 8 cm.',
                'menunggu_validasi' => 'Permukaan jalan bergelombang parah menyebabkan kendaraan berat sering rusak. Laporan survei sudah selesai.',
                'ditolak'           => 'Jalan kampung berpasir rusak dan licin saat hujan, warga kesulitan beraktivitas.',
                'ditunda'           => 'Jembatan kecil penghubung RT rusak di bagian rel pembatasnya. Perbaikan menunggu anggaran.',
                'proses'            => 'Jalan berlubang parah sepanjang 200 meter di jalur utama kota sedang diperbaiki tim UPTD.',
                'terkendala'        => 'Perbaikan jalan terkendala cuaca hujan deras yang berlangsung lebih dari 2 minggu.',
                'revisi'            => 'Hasil pengaspalan sebelumnya tidak rata dan sudah mengelupas dalam 1 minggu. Perlu pengerjaan ulang.',
                'selesai'           => 'Jalan berlubang di Jl. Patih Bandaran telah berhasil diperbaiki. Kondisi jalan kini mulus dan aman.',
            ],
            'Jembatan' => [
                'pending'           => 'Jembatan penghubung dua desa retak di bagian tiang penyangga utamanya, terlihat dari luar.',
                'diteruskan'        => 'Pagar pengaman jembatan hilang di salah satu sisi sepanjang 10 meter, sangat berbahaya.',
                'dikembalikan'      => 'Jembatan goyang saat dilalui kendaraan roda empat. Foto yang dikirim tidak menunjukkan kerusakan yang jelas.',
                'disurvei'          => 'Jembatan beton ambles di bagian ujung pangkalnya, kendaraan berat dilarang melintas.',
                'menunggu_validasi' => 'Tulangan besi jembatan terlihat berkarat dan keluar dari beton. Survei lapangan selesai.',
                'ditolak'           => 'Jembatan bambu rusak yang dilaporkan merupakan jembatan swadaya masyarakat, bukan aset pemerintah.',
                'ditunda'           => 'Perbaikan jembatan utama antar kecamatan ditunda karena butuh kajian teknis mendalam.',
                'proses'            => 'Tim UPTD sedang mengerjakan penguatan tiang pondasi jembatan Cibogo yang ambles 15 cm.',
                'terkendala'        => 'Perbaikan jembatan terkendala banjir kiriman yang membuat area kerja tidak aman.',
                'revisi'            => 'Pengecoran tiang jembatan tidak memenuhi kuat tekan K-250 yang disyaratkan, perlu dibongkar ulang.',
                'selesai'           => 'Jembatan Cibogo telah selesai diperkuat dan dinyatakan aman untuk dilalui kendaraan hingga 8 ton.',
            ],
            'SDA' => [
                'pending'           => 'Saluran irigasi tersumbat tanaman liar dan sampah rumah tangga, air tidak mengalir ke sawah.',
                'diteruskan'        => 'Gorong-gorong di persimpangan jalan mampet total, menyebabkan banjir setinggi 30 cm.',
                'dikembalikan'      => 'Tanggul sungai terlihat retak memanjang. Laporan tidak disertai koordinat GPS yang valid.',
                'disurvei'          => 'Pintu air bendung utama rusak dan tidak bisa ditutup sempurna, air terus mengalir boros.',
                'menunggu_validasi' => 'Sodetan sungai tersumbat sedimen, debit air berkurang 60% dari normal. Survei selesai.',
                'ditolak'           => 'Genangan air di halaman rumah warga akibat sistem drainase yang tidak memadai. Bukan kewenangan PUPR.',
                'ditunda'           => 'Normalisasi sungai Ciasem ditunda menunggu pembebasan lahan sempadan yang masih sengketa.',
                'proses'            => 'Tim sedang melakukan pengerukan sedimen di saluran irigasi primer sepanjang 1,5 km.',
                'terkendala'        => 'Perbaikan tanggul terkendala debit sungai yang masih tinggi akibat curah hujan ekstrem.',
                'revisi'            => 'Material bronjong penutup tanggul tidak sesuai spesifikasi teknis. Perlu diganti dengan ukuran yang benar.',
                'selesai'           => 'Saluran irigasi Dangdeur telah berhasil dinormalisasi. Aliran air ke sawah kembali normal.',
            ],
            'Cipta Karya' => [
                'pending'           => 'Lampu penerangan jalan umum (PJU) mati sepanjang 500 meter di jalan utama kota, rawan kriminalitas.',
                'diteruskan'        => 'Fasilitas MCK umum di area pasar rusak parah, tidak layak digunakan oleh pedagang.',
                'dikembalikan'      => 'Pipa saluran air bersih bocor merembes ke badan jalan. Foto tidak cukup jelas, perlu foto jarak dekat.',
                'disurvei'          => 'Drainase lingkungan perumahan tersumbat, air menggenang hingga masuk ke rumah warga.',
                'menunggu_validasi' => 'Trotoar pedestrian retak dan bergerak naik akibat akar pohon, berbahaya bagi pejalan kaki. Survei done.',
                'ditolak'           => 'Pemasangan reklame tidak berizin di pinggir jalan. Ini wewenang Dinas Perizinan, bukan PUPR.',
                'ditunda'           => 'Revitalisasi taman kota ditunda karena anggaran dialihkan untuk penanganan bencana banjir.',
                'proses'            => 'Teknisi PLN dan PUPR sedang bersama-sama memperbaiki 12 titik PJU yang padam di Jl. Otto Iskandar.',
                'terkendala'        => 'Penggantian pipa air bersih terkendala kemacetan lalu lintas dan penolakan warga sementara.',
                'revisi'            => 'Instalasi PJU tidak sesuai standar ketinggian 7 meter yang ditetapkan. Tiang perlu diganti.',
                'selesai'           => 'PJU di Jl. Otto Iskandardinata telah kembali menyala semua. 12 lampu LED baru berhasil dipasang.',
            ],
            'Tata Ruang' => [
                'pending'           => 'Bangunan liar berdiri di atas lahan sempadan sungai yang termasuk zona larangan bangunan.',
                'diteruskan'        => 'Kawasan hijau/RTH di sekitar perumahan sudah beralih fungsi menjadi bangunan komersial tanpa izin.',
                'dikembalikan'      => 'Pedagang kaki lima berjualan di atas trotoar jalan, menghalangi pejalan kaki. Butuh foto lebih jelas.',
                'disurvei'          => 'Pembangunan gudang industri diduga tidak sesuai peruntukan lahan zona pertanian di sekitarnya.',
                'menunggu_validasi' => 'Pagar pembatas kawasan industri menutupi saluran drainase kota. Survei lapangan telah selesai.',
                'ditolak'           => 'Keluhan tentang papan nama toko terlalu besar. Masalah estetika ini ditangani oleh Satuan Polisi PP.',
                'ditunda'           => 'Penataan kawasan pedagang pasar tumpah tertunda karena belum ada kesepakatan dengan asosiasi pedagang.',
                'proses'            => 'Tim sedang melakukan pendataan dan pembinaan terhadap bangunan tidak berizin di zona pertanian.',
                'terkendala'        => 'Penertiban bangunan liar terkendala karena ada gugatan hukum yang diajukan oleh pemilik lahan.',
                'revisi'            => 'Hasil pemetaan zona terdampak tidak akurat. Perlu survei ulang dengan koordinat GPS yang presisi.',
                'selesai'           => 'Bangunan liar di sempadan sungai berhasil ditertibkan. Lahan sudah bersih dan dipasang rambu larangan.',
            ],
        ];

        // Fallback jika kategori tidak dikenali
        $kategoriKey = array_key_exists($bidang, $map) ? $bidang : 'Jalan';
        return $map[$kategoriKey][$status] ?? "Laporan {$status} untuk bidang {$bidang}.";
    }

    // ─── Helper: Kondisi lapangan ─────────────────────────────────────────────
    private function kondisi(string $bidang): string
    {
        return match ($bidang) {
            'Jalan'       => 'permukaan jalan retak dan berlubang dengan kedalaman ±8 cm',
            'Jembatan'    => 'struktur beton melemah dengan kuat tekan di bawah standar',
            'SDA'         => 'saluran tersumbat sedimen dengan kapasitas aliran berkurang 60%',
            'Cipta Karya' => 'instalasi tidak berfungsi dan memerlukan penggantian komponen',
            'Tata Ruang'  => 'pelanggaran pemanfaatan ruang teridentifikasi di lapangan',
            default       => 'kerusakan infrastruktur teridentifikasi di lapangan',
        };
    }

    // ─── Helper: Metode perbaikan ─────────────────────────────────────────────
    private function metode(string $bidang): string
    {
        return match ($bidang) {
            'Jalan'       => 'pengaspalan panas (hot mix) AC-WC sesuai SNI',
            'Jembatan'    => 'injeksi epoksi dan grouting struktur beton',
            'SDA'         => 'pengerukan mekanis dan pemasangan bronjong kawat',
            'Cipta Karya' => 'penggantian komponen dan uji fungsi (commissioning)',
            'Tata Ruang'  => 'penertiban administratif dan pembongkaran terstruktur',
            default       => 'perbaikan sesuai standar teknis PUPR',
        };
    }

    // ─── Buat akun pelapor masyarakat dummy ──────────────────────────────────
    private function buatPelapor(): array
    {
        $ids = [];
        $pelapors = [
            ['nama_lengkap' => 'Ahmad Fauzi',      'username' => 'ahmad.fauzi',    'email' => 'ahmad@mail.com'],
            ['nama_lengkap' => 'Siti Rahayu',      'username' => 'siti.rahayu',    'email' => 'siti@mail.com'],
            ['nama_lengkap' => 'Budi Santoso',     'username' => 'budi.santoso',   'email' => 'budi@mail.com'],
            ['nama_lengkap' => 'Dewi Anggraini',   'username' => 'dewi.anggraini', 'email' => 'dewi@mail.com'],
            ['nama_lengkap' => 'Rizky Pratama',    'username' => 'rizky.pratama',  'email' => 'rizky@mail.com'],
        ];

        foreach ($pelapors as $p) {
            $ids[] = DB::table('users')->insertGetId([
                'nama_lengkap' => $p['nama_lengkap'],
                'username'     => $p['username'],
                'email'        => $p['email'],
                'password'     => Hash::make('password123'),
                'peran'        => 'masyarakat',
                'status_akun'  => 'aktif',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        return $ids;
    }

    // ─── Pastikan setiap bidang punya admin & pekerja ────────────────────────
    private function pastikanStaf(
        $bidangRows,
        $adminBidangs,
        $pekerjas
    ): array {
        $namaBidangMap = [
            'Jalan'                  => 'jalan',
            'Jembatan'               => 'jembatan',
            'Sumber Daya Air (SDA)'  => 'sda',
            'Cipta Karya'            => 'ck',
            'Tata Ruang'             => 'tr',
        ];

        foreach ($bidangRows as $bidang) {
            $slug = $namaBidangMap[$bidang->nama_bidang] ?? strtolower(str_replace(' ', '_', $bidang->nama_bidang));

            // Buat admin bidang jika belum ada
            if (!$adminBidangs->has($bidang->id)) {
                $adminId = DB::table('users')->insertGetId([
                    'nama_lengkap' => 'Admin ' . $bidang->nama_bidang,
                    'username'     => 'admin_' . $slug,
                    'email'        => 'admin.' . $slug . '@sigap.id',
                    'password'     => Hash::make('password123'),
                    'peran'        => 'admin_bidang',
                    'id_bidang'    => $bidang->id,
                    'status_akun'  => 'aktif',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
                $adminBidangs[$bidang->id] = (object)['id' => $adminId, 'id_bidang' => $bidang->id];
            }

            // Buat pekerja jika belum ada
            if (!$pekerjas->has($bidang->id)) {
                $pekerjId = DB::table('users')->insertGetId([
                    'nama_lengkap' => 'Pekerja ' . $bidang->nama_bidang,
                    'username'     => 'pekerja_' . $slug,
                    'email'        => 'pekerja.' . $slug . '@sigap.id',
                    'password'     => Hash::make('password123'),
                    'peran'        => 'pekerja_bidang',
                    'id_bidang'    => $bidang->id,
                    'status_akun'  => 'aktif',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
                $pekerjas[$bidang->id] = (object)['id' => $pekerjId, 'id_bidang' => $bidang->id];
            }
        }

        return [$adminBidangs, $pekerjas];
    }
}
