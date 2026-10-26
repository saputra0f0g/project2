<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanKeluhan;
use App\Models\User;
use Carbon\Carbon;

/**
 * LaporanJalanSeeder — Seeder data laporan kerusakan infrastruktur jalan (Kabupaten Subang).
 *
 * Sesuai arsitektur baru:
 *   - Fokus: Hanya infrastruktur Jalan
 *   - Pelapor: User dengan peran `masyarakat`
 *   - Status: menunggu, diverifikasi, dalam_perbaikan, selesai, ditolak
 */
class LaporanJalanSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID semua user masyarakat yang tersedia
        $pelaporIds = User::where('peran', 'masyarakat')->pluck('id')->toArray();

        // Fallback jika belum ada user masyarakat sama sekali
        if (empty($pelaporIds)) {
            $userFallback = User::firstOrCreate(
                ['username' => 'user_test'],
                [
                    'nama_lengkap' => 'Budi Santoso',
                    'email'        => 'budi.test@sigap.id',
                    'password'     => bcrypt('UserTest123!'),
                    'nomor_hp'     => '082345678901',
                    'peran'        => 'masyarakat',
                    'status_akun'  => 'aktif',
                ]
            );
            $pelaporIds = [$userFallback->id];
        }

        // Kumpulan foto bukti yang ada di storage/app/public/laporan
        $daftarFoto = [
            'laporan/7Ke29ZQ2nDhb51ysVGVbFOeVXNlvh7CrO62yk6aQ.jpg',
            'laporan/JUJ68ptUfRY381fi3pjd27EpXMogb9TVkJqUEJJV.jpg',
            'laporan/KwXThtY4YAcvPLtyjSUouWQlnYg92JbY7ChwSOlZ.jpg',
            'laporan/LN46EfCUG3RcFoS3hnpSNCNxKL6EZilLmSoTQXbW.jpg',
            'laporan/PIycdsynRwCLL4vzobJMYr35D9FDS8gekrqY62iW.jpg',
            'laporan/ROQwIx9YQuWZkDtIGjJKLadOF7qaLysht95dXAz2.jpg',
            'laporan/XgxNumNPcg8xoegarUqUloTBTeF2cwbUso5gkce9.jpg',
            'laporan/ZBMClgi8F4gIG7tnXVzblCOAc1CNCMRRdim17nlc.jpg',
            'laporan/b74e82AhUEQOLTDKieFowGeJLR1YzT9jQOJbREtR.jpg',
            'laporan/cqzGJk84el55iPE6ISpOny8mN73vPNvgVMys94HD.jpg',
            'laporan/nerdPEXq7eLtzcJMZ5wSpvFYS2aRJKIHru62dfC3.jpg',
            'laporan/rUlMNGEjLBUq6qfvXissfyyp0wR5EkOD24L1OSUg.jpg',
            'laporan/tlhyXfT37k6tM0IvIt6FF8sYB6ax16BdfIBPuWjJ.jpg',
            'laporan/ugDmq1481IbFwYMCIcDDag1IOqUMTzBhnXjINfQI.jpg',
        ];

        // Daftar 20 Laporan Kerusakan Jalan di Area Kabupaten Subang
        $dataLaporan = [
            // ─── STATUS: MENUNGGU VERIFIKASI (Laporan Baru Masuk) ────────────
            [
                'id_laporan'        => 'JL260001',
                'judul_laporan'     => 'Jalan Berlubang Dalam di Depan RSUD Subang',
                'jenis_kerusakan'   => 'jalan_berlubang',
                'tingkat_kerusakan' => 'berat',
                'deskripsi_laporan' => 'Terdapat lubang berdiameter sekitar 80 cm dengan kedalaman 15 cm di dekat pintu masuk RSUD. Sangat berbahaya bagi ambulans dan pengendara motor terutama saat hujan tergenang air.',
                'lokasi_gps'        => '-6.5645, 107.7610',
                'alamat_map'        => 'Jl. Brigjen Katamso No. 37, Dangdeur, Subang',
                'status'            => 'menunggu',
                'catatan_admin'     => null,
                'alasan_penolakan'  => null,
                'days_ago'          => 1,
            ],
            [
                'id_laporan'        => 'JL260002',
                'judul_laporan'     => 'Lampu PJU Mati Total Sepanjang 300 Meter',
                'jenis_kerusakan'   => 'lampu_jalan_mati',
                'tingkat_kerusakan' => 'sedang',
                'deskripsi_laporan' => 'Sebanyak 6 tiang lampu penerangan jalan umum mati total sejak 4 hari lalu. Kondisi jalan sangat gelap gulita dan rawan tindak kejahatan maupun kecelakaan.',
                'lokasi_gps'        => '-6.5710, 107.7530',
                'alamat_map'        => 'Jl. Otto Iskandardinata, Karanganyar, Subang',
                'status'            => 'menunggu',
                'catatan_admin'     => null,
                'alasan_penolakan'  => null,
                'days_ago'          => 2,
            ],
            [
                'id_laporan'        => 'JL260003',
                'judul_laporan'     => 'Genangan Air Akibat Got Saluran Jalan Mampet',
                'jenis_kerusakan'   => 'drainase_tersumbat',
                'tingkat_kerusakan' => 'sedang',
                'deskripsi_laporan' => 'Drainase samping jalan tersumbat sampah dan tanah sedimen sehingga setiap hujan air meluber ke badan jalan setinggi 20-30 cm.',
                'lokasi_gps'        => '-6.5592, 107.7668',
                'alamat_map'        => 'Jl. Kapten Hanafiah, Rawabadak, Subang',
                'status'            => 'menunggu',
                'catatan_admin'     => null,
                'alasan_penolakan'  => null,
                'days_ago'          => 3,
            ],
            [
                'id_laporan'        => 'JL260004',
                'judul_laporan'     => 'Trotoar Ambles dan Penutup Saluran Pecah',
                'jenis_kerusakan'   => 'trotoar_rusak',
                'tingkat_kerusakan' => 'berat',
                'deskripsi_laporan' => 'Bahu jalan dan trotoar ambles sedalam 40 cm. Pejalan kaki terpaksa berjalan di badan jalan raya yang ramai.',
                'lokasi_gps'        => '-6.5670, 107.7601',
                'alamat_map'        => 'Jl. Ahmad Yani, Pasirkareumbi, Subang',
                'status'            => 'menunggu',
                'catatan_admin'     => null,
                'alasan_penolakan'  => null,
                'days_ago'          => 3,
            ],
            [
                'id_laporan'        => 'JL260005',
                'judul_laporan'     => 'Rambu Petunjuk Arah Roboh Tertabrak',
                'jenis_kerusakan'   => 'rambu_rusak',
                'tingkat_kerusakan' => 'ringan',
                'deskripsi_laporan' => 'Tiang rambu penunjuk arah menuju Kalijati roboh ke sisi jalan menghalangi pandangan pengemudi di pertigaan.',
                'lokasi_gps'        => '-6.5520, 107.7420',
                'alamat_map'        => 'Simpang Tiga Cinangsi, Subang',
                'status'            => 'menunggu',
                'catatan_admin'     => null,
                'alasan_penolakan'  => null,
                'days_ago'          => 4,
            ],

            // ─── STATUS: DIVERIFIKASI (Sudah Divalidasi Admin Jalan) ──────────
            [
                'id_laporan'        => 'JL260006',
                'judul_laporan'     => 'Jalan Retak Panjang di Tikungan Sompi',
                'jenis_kerusakan'   => 'jalan_retak',
                'tingkat_kerusakan' => 'sedang',
                'deskripsi_laporan' => 'Permukaan aspal retak buaya memanjang sepanjang 50 meter. Dikhawatirkan semakin amblas jika dilalui kendaraan muatan berat.',
                'lokasi_gps'        => '-6.5740, 107.7655',
                'alamat_map'        => 'Jl. Sompi, Cigadung, Subang',
                'status'            => 'diverifikasi',
                'catatan_admin'     => 'Laporan telah diverifikasi oleh tim peninjau lapangan. Dijadwalkan perataan dan pelapisan aspal baru.',
                'alasan_penolakan'  => null,
                'days_ago'          => 6,
            ],
            [
                'id_laporan'        => 'JL260007',
                'judul_laporan'     => 'Lampu Jalan Kedip-Kedip Sering Padam',
                'jenis_kerusakan'   => 'lampu_jalan_mati',
                'tingkat_kerusakan' => 'ringan',
                'deskripsi_laporan' => 'Lampu PJU di bundaran wisma karya berkedip tidak stabil dan padam saat malam hari.',
                'lokasi_gps'        => '-6.5627, 107.7613',
                'alamat_map'        => 'Bundaran Wisma Karya, Alun-alun Subang',
                'status'            => 'diverifikasi',
                'catatan_admin'     => 'Diverifikasi. Permintaan suku cadang ballast dan lampu LED telah diteruskan ke tim teknisi PJU.',
                'alasan_penolakan'  => null,
                'days_ago'          => 8,
            ],
            [
                'id_laporan'        => 'JL260008',
                'judul_laporan'     => 'Bahu Jalan Amblas Dekat Jembatan Cileuleuy',
                'jenis_kerusakan'   => 'jalan_amblas',
                'tingkat_kerusakan' => 'berat',
                'deskripsi_laporan' => 'Tanah di samping pondasi jalan terkikis aliran sungai kecil hingga mendekati aspal jalan utama.',
                'lokasi_gps'        => '-6.5820, 107.7710',
                'alamat_map'        => 'Jl. Raya Cileuleuy, Cijambe, Subang',
                'status'            => 'diverifikasi',
                'catatan_admin'     => 'Verifikasi selesai. Prioritas penanganan darurat pemasangan bronjong kawat penahan tebing.',
                'alasan_penolakan'  => null,
                'days_ago'          => 9,
            ],
            [
                'id_laporan'        => 'JL260009',
                'judul_laporan'     => 'Genangan Air di Jalur Lambat Pasar Pujasera',
                'jenis_kerusakan'   => 'jalan_banjir',
                'tingkat_kerusakan' => 'sedang',
                'deskripsi_laporan' => 'Air tergenang setinggi 15 cm setiap sore setelah hujan lebat, mengganggu aktivitas pedagang dan pembeli.',
                'lokasi_gps'        => '-6.5648, 107.7634',
                'alamat_map'        => 'Kawasan Pujasera, Jl. Letjen Suprapto, Subang',
                'status'            => 'diverifikasi',
                'catatan_admin'     => 'Diverifikasi. Akan dikerahkan tim pembersihan gorong-gorong dan normalisasi inlet air.',
                'alasan_penolakan'  => null,
                'days_ago'          => 11,
            ],

            // ─── STATUS: DALAM PERBAIKAN (Pekerja Sedang di Lapangan) ─────────
            [
                'id_laporan'        => 'JL260010',
                'judul_laporan'     => 'Perbaikan Jalan Amblas Jalur Kalijati',
                'jenis_kerusakan'   => 'jalan_amblas',
                'tingkat_kerusakan' => 'berat',
                'deskripsi_laporan' => 'Badan jalan retak turun 20 cm akibat pergerakan tanah di sekitar tebing jalan.',
                'lokasi_gps'        => '-6.5320, 107.6830',
                'alamat_map'        => 'Jl. Raya Kalijati Km 8, Subang',
                'status'            => 'dalam_perbaikan',
                'catatan_admin'     => 'Alat berat ekskavator dan material batu belah sudah di lokasi. Sedang proses pengecoran dinding penahan tanah.',
                'alasan_penolakan'  => null,
                'days_ago'          => 12,
            ],
            [
                'id_laporan'        => 'JL260011',
                'judul_laporan'     => 'Penambalan Lubang Aspal Jl. Pejuang 45',
                'jenis_kerusakan'   => 'jalan_berlubang',
                'tingkat_kerusakan' => 'sedang',
                'deskripsi_laporan' => 'Beberapa lubang berdiameter 30-50 cm tersebar di jalur lambat dan tengah.',
                'lokasi_gps'        => '-6.5615, 107.7580',
                'alamat_map'        => 'Jl. Pejuang 45, Karanganyar, Subang',
                'status'            => 'dalam_perbaikan',
                'catatan_admin'     => 'Tim Unit Reaksi Cepat (URC) Jalan sedang melakukan pemotongan aspal dan penambalan cold-mix/hotmix.',
                'alasan_penolakan'  => null,
                'days_ago'          => 14,
            ],
            [
                'id_laporan'        => 'JL260012',
                'judul_laporan'     => 'Pengerukan Saluran Air Meluap di Pagaden',
                'jenis_kerusakan'   => 'drainase_tersumbat',
                'tingkat_kerusakan' => 'sedang',
                'deskripsi_laporan' => 'Saluran samping jalan penuh endapan lumpur pekat menyebabkan jalan licin dan berlumpur.',
                'lokasi_gps'        => '-6.4710, 107.8010',
                'alamat_map'        => 'Jl. Raya Pagaden Barat, Subang',
                'status'            => 'dalam_perbaikan',
                'catatan_admin'     => 'Pekerjaan normalisasi saluran sedang berjalan bersama warga setempat dan tim pemeliharaan jalan.',
                'alasan_penolakan'  => null,
                'days_ago'          => 16,
            ],
            [
                'id_laporan'        => 'JL260013',
                'judul_laporan'     => 'Pergantian Lampu PJU Rusak Kawasan Jalancagak',
                'jenis_kerusakan'   => 'lampu_jalan_mati',
                'tingkat_kerusakan' => 'ringan',
                'deskripsi_laporan' => 'Lampu PJU mati di pertigaan tugu nanas Jalancagak.',
                'lokasi_gps'        => '-6.6710, 107.6850',
                'alamat_map'        => 'Simpang Tugu Nanas, Jalancagak, Subang',
                'status'            => 'dalam_perbaikan',
                'catatan_admin'     => 'Mobil tangga hidrolik dan teknisi sedang mengganti modul lampu dan kabel instalasi yang putus.',
                'alasan_penolakan'  => null,
                'days_ago'          => 18,
            ],

            // ─── STATUS: SELESAI (Perbaikan Rampung 100%) ─────────────────────
            [
                'id_laporan'        => 'JL260014',
                'judul_laporan'     => 'Rekonstruksi Lubang Besar di Jl. Arif Rahman Hakim',
                'jenis_kerusakan'   => 'jalan_berlubang',
                'tingkat_kerusakan' => 'berat',
                'deskripsi_laporan' => 'Lubang sedalam 20 cm tepat di depan sekolah, berisiko besar bagi siswa dan warga.',
                'lokasi_gps'        => '-6.5662, 107.7567',
                'alamat_map'        => 'Jl. Arif Rahman Hakim, Cigadung, Subang',
                'status'            => 'selesai',
                'catatan_admin'     => 'Pekerjaan penambalan dan perataan permukaan aspal telah selesai 100%. Kondisi jalan kini mulus dan aman.',
                'alasan_penolakan'  => null,
                'days_ago'          => 22,
            ],
            [
                'id_laporan'        => 'JL260015',
                'judul_laporan'     => 'Pemasangan U-Ditch Drainase di Jl. Ki Hajar Dewantara',
                'jenis_kerusakan'   => 'drainase_tersumbat',
                'tingkat_kerusakan' => 'sedang',
                'deskripsi_laporan' => 'Saluran drainase lama ambruk tertimbun tanah.',
                'lokasi_gps'        => '-6.5580, 107.7620',
                'alamat_map'        => 'Jl. Ki Hajar Dewantara, Dangdeur, Subang',
                'status'            => 'selesai',
                'catatan_admin'     => 'Pemasangan beton precast U-Ditch telah selesai. Aliran air kini mengalir lancar ke saluran pembuangan utama.',
                'alasan_penolakan'  => null,
                'days_ago'          => 25,
            ],
            [
                'id_laporan'        => 'JL260016',
                'judul_laporan'     => 'Perbaikan Trotoar Rusak di Kawasan Pertokoan',
                'jenis_kerusakan'   => 'trotoar_rusak',
                'tingkat_kerusakan' => 'ringan',
                'deskripsi_laporan' => 'Paving block trotoar lepas dan berserakan tersandung pejalan kaki.',
                'lokasi_gps'        => '-6.5635, 107.7625',
                'alamat_map'        => 'Pertokoan Jl. Suprapto, Pasirkareumbi, Subang',
                'status'            => 'selesai',
                'catatan_admin'     => 'Pemasangan kembali paving block dan pemadatan ulang selesai rapi.',
                'alasan_penolakan'  => null,
                'days_ago'          => 28,
            ],
            [
                'id_laporan'        => 'JL260017',
                'judul_laporan'     => 'Penggantian Rambu Batas Kecepatan Rusak',
                'jenis_kerusakan'   => 'rambu_rusak',
                'tingkat_kerusakan' => 'ringan',
                'deskripsi_laporan' => 'Rambu peringatan tikungan tajam dan batas kecepatan pudar tertutup cat semprot.',
                'lokasi_gps'        => '-6.6210, 107.7450',
                'alamat_map'        => 'Tanjakan Emen arah Cijambe, Subang',
                'status'            => 'selesai',
                'catatan_admin'     => 'Rambu telah diganti dengan daun rambu baru berstandar reflektif tinggi.',
                'alasan_penolakan'  => null,
                'days_ago'          => 35, // Bulan lalu
            ],
            [
                'id_laporan'        => 'JL260018',
                'judul_laporan'     => 'Pengaspalan Ulang Jalur Cijambe - Kasomalang',
                'jenis_kerusakan'   => 'jalan_retak',
                'tingkat_kerusakan' => 'berat',
                'deskripsi_laporan' => 'Aspal mengelupas sepanjang 150 meter akibat beban truk pasir.',
                'lokasi_gps'        => '-6.6800, 107.7400',
                'alamat_map'        => 'Jl. Raya Kasomalang, Subang',
                'status'            => 'selesai',
                'catatan_admin'     => 'Pengaspalan overlay hotmix 2 lapis telah rampung dikerjakan oleh penyedia jasa.',
                'alasan_penolakan'  => null,
                'days_ago'          => 40, // Bulan lalu
            ],

            // ─── STATUS: DITOLAK (Laporan Tidak Valid / Luar Wewenang) ────────
            [
                'id_laporan'        => 'JL260019',
                'judul_laporan'     => 'Jalan Tol Cipali Km 109 Berlubang',
                'jenis_kerusakan'   => 'jalan_berlubang',
                'tingkat_kerusakan' => 'berat',
                'deskripsi_laporan' => 'Terdapat lubang di lajur cepat jalan tol Cipali KM 109.',
                'lokasi_gps'        => '-6.4600, 107.6950',
                'alamat_map'        => 'Jalan Tol Cikopo-Palimanan KM 109, Subang',
                'status'            => 'ditolak',
                'catatan_admin'     => null,
                'alasan_penolakan'  => 'Ruas jalan tol merupakan wewenang pengelola Badan Usaha Jalan Tol (Astra Tol Cipali / BPJT), bukan wewenang Dinas PUPR Kabupaten Subang. Laporan telah kami teruskan ke call center Astra Tol Cipali.',
                'days_ago'          => 5,
            ],
            [
                'id_laporan'        => 'JL260020',
                'judul_laporan'     => 'Jalan Gang Rumah Pribadi Rusak',
                'jenis_kerusakan'   => 'lainnya',
                'tingkat_kerusakan' => 'ringan',
                'deskripsi_laporan' => 'Halaman gang menuju pekarangan rumah pribadi belum disemen.',
                'lokasi_gps'        => '-6.5680, 107.7550',
                'alamat_map'        => 'Gang Kenanga, Cigadung, Subang',
                'status'            => 'ditolak',
                'catatan_admin'     => null,
                'alasan_penolakan'  => 'Lokasi yang dilaporkan merupakan jalan lingkungan perumahan pribadi / bukan aset jalan umum milik Pemerintah Kabupaten Subang.',
                'days_ago'          => 15,
            ],
        ];

        $jumlahDibuat = 0;

        foreach ($dataLaporan as $index => $item) {
            // Pilih pelapor secara bergiliran
            $pelaporId = $pelaporIds[$index % count($pelaporIds)];

            // Pilih foto bukti dari daftar file nyata di storage
            $foto = $daftarFoto[$index % count($daftarFoto)];

            // Buat timestamp mundur sesuai $item['days_ago']
            $createdAt = Carbon::now()->subDays($item['days_ago']);
            $updatedAt = $item['status'] === 'selesai'
                ? (clone $createdAt)->addDays(rand(2, 5))
                : (clone $createdAt)->addHours(rand(1, 12));

            LaporanKeluhan::updateOrCreate(
                ['id_laporan' => $item['id_laporan']],
                [
                    'id_pelapor'        => $pelaporId,
                    'kategori_bidang'   => 'Jalan',
                    'judul_laporan'     => $item['judul_laporan'],
                    'jenis_kerusakan'   => $item['jenis_kerusakan'],
                    'tingkat_kerusakan' => $item['tingkat_kerusakan'],
                    'deskripsi_laporan' => $item['deskripsi_laporan'],
                    'lokasi_gps'        => $item['lokasi_gps'],
                    'alamat_map'        => $item['alamat_map'],
                    'foto_bukti'        => $foto,
                    'status'            => $item['status'],
                    'catatan_admin'     => $item['catatan_admin'],
                    'alasan_penolakan'  => $item['alasan_penolakan'],
                    'created_at'        => $createdAt,
                    'updated_at'        => $updatedAt,
                ]
            );

            $jumlahDibuat++;
        }

        $this->command->info("✅ LaporanJalanSeeder berhasil membuat/memperbarui {$jumlahDibuat} data laporan kerusakan jalan!");
        $this->command->line("   - Menunggu       : 5 laporan");
        $this->command->line("   - Diverifikasi   : 4 laporan");
        $this->command->line("   - Dalam Perbaikan: 4 laporan");
        $this->command->line("   - Selesai        : 5 laporan");
        $this->command->line("   - Ditolak        : 2 laporan");
    }
}
