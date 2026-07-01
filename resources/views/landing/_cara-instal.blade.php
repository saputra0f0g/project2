{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- CARA INSTAL — resources/views/landing/_cara-instal.blade.php --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<section id="cara-instal" class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        <div class="text-center mb-14 reveal">
            <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 mb-6 border"
                 style="background:#FFF7ED; border-color:#FED7AA;">
                <i class="fab fa-android text-base" style="color:#16a34a"></i>
                <span class="text-xs font-bold uppercase tracking-wider" style="color:#9a3412">Panduan Instalasi APK</span>
            </div>
            <h2 class="text-3xl sm:text-4xl font-black mb-4" style="color:#0F2D6B">
                Cara Instal Aplikasi <span style="color:#F59E0B">SIGAP</span>
            </h2>
            <p class="text-gray-500 text-base max-w-2xl mx-auto font-medium mb-6">
                Karena SIGAP belum tersedia di Google Play Store, aplikasi diinstal melalui file APK.
                Ikuti langkah-langkah berikut — mudah dan aman!
            </p>

            {{-- Warning notice --}}
        </div>

        {{-- Steps --}}
        <div class="max-w-4xl mx-auto">
            <div class="relative">
                {{-- Vertical connector line --}}
                <div class="hidden sm:block absolute left-[47px] top-20 bottom-20 w-0.5 z-0"
                     style="background:linear-gradient(to bottom, #0F2D6B, #F59E0B, #ea580c, #16a34a); opacity:0.2;"></div>

                <div class="space-y-6">
                    @foreach([
                        [
                            'num'       => '1',
                            'icon'      => 'fa-download',
                            'bg'        => '#0F2D6B',
                            'title'     => 'Unduh File APK SIGAP',
                            'desc'      => 'Ketuk tombol <strong>"Unduh Aplikasi SIGAP (APK)"</strong> di halaman ini. File APK akan otomatis tersimpan di folder <strong>Downloads</strong> di HP Anda.',
                            'tip'       => 'Pastikan koneksi internet stabil. Ukuran file ±25 MB.',
                            'tip_bg'    => '#EFF6FF',
                            'tip_color' => '#1e40af',
                            'tip_icon'  => '#3b82f6',
                        ],
                        [
                            'num'       => '2',
                            'icon'      => 'fa-folder-open',
                            'bg'        => '#F59E0B',
                            'title'     => 'Buka File APK yang Diunduh',
                            'desc'      => 'Buka <strong>File Manager</strong> atau tarik notifikasi dari atas layar, lalu ketuk file <strong>SIGAP.apk</strong>. Jika muncul peringatan keamanan, ketuk <strong>"Pengaturan"</strong>.',
                            'tip'       => 'Bisa juga dibuka langsung dari notifikasi "Unduhan selesai" di panel atas.',
                            'tip_bg'    => '#FFFBEB',
                            'tip_color' => '#92400e',
                            'tip_icon'  => '#D97706',
                        ],
                        [
                            'num'       => '3',
                            'icon'      => 'fa-toggle-on',
                            'bg'        => '#ea580c',
                            'title'     => 'Aktifkan "Izinkan dari Sumber Ini"',
                            'desc'      => 'Di halaman Pengaturan yang terbuka, cari opsi <strong>"Izinkan dari sumber ini"</strong> (Allow from this source) lalu <strong>aktifkan</strong> tombolnya, kemudian tekan <strong>Kembali</strong>.',
                            'tip'       => 'Pengaturan ini hanya berlaku satu kali untuk browser/file manager yang Anda pakai.',
                            'tip_bg'    => '#FFF7ED',
                            'tip_color' => '#9a3412',
                            'tip_icon'  => '#ea580c',
                        ],
                        [
                            'num'       => '4',
                            'icon'      => 'fa-check-circle',
                            'bg'        => '#16a34a',
                            'title'     => 'Instalasi Selesai — Aplikasi Siap!',
                            'desc'      => 'Ketuk <strong>"Instal"</strong> dan tunggu hingga selesai. Setelah selesai, ketuk <strong>"Buka"</strong> untuk langsung menggunakan SIGAP. Daftarkan akun Anda dan mulai buat laporan!',
                            'tip'       => 'Ikon SIGAP akan muncul di layar utama HP Anda setelah instalasi selesai.',
                            'tip_bg'    => '#F0FDF4',
                            'tip_color' => '#14532d',
                            'tip_icon'  => '#16a34a',
                        ],
                    ] as $step)
                    <div class="reveal flex gap-5 sm:gap-8 items-start">

                        {{-- Step icon block --}}
                        <div class="flex-shrink-0 relative z-10">
                            <div class="w-24 h-24 rounded-2xl flex flex-col items-center justify-center shadow-xl"
                                 style="background:{{ $step['bg'] }};">
                                <i class="fas {{ $step['icon'] }} text-white text-2xl mb-1"></i>
                                <span class="text-white/75 text-[10px] font-black">Langkah {{ $step['num'] }}</span>
                            </div>
                        </div>

                        {{-- Step content --}}
                        <div class="flex-1 rounded-2xl p-6 border border-gray-100" style="background:#F9FAFB;">
                            <h3 class="text-lg font-black mb-2" style="color:#0F2D6B">{{ $step['title'] }}</h3>
                            <p class="text-gray-600 text-sm leading-relaxed mb-3">{!! $step['desc'] !!}</p>

                            <div class="flex items-center gap-2 rounded-xl px-4 py-2.5 border"
                                 style="background:{{ $step['tip_bg'] }}; border-color:{{ $step['tip_bg'] }};">
                                <i class="fas fa-lightbulb text-sm flex-shrink-0" style="color:{{ $step['tip_icon'] }}"></i>
                                <p class="text-xs font-semibold" style="color:{{ $step['tip_color'] }}">{{ $step['tip'] }}</p>
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Bottom CTA --}}
            <div class="mt-12 text-center reveal">
                <a href="#download"
                   class="btn-cta inline-flex items-center gap-3 px-10 py-5 rounded-2xl font-black text-lg"
                   style="color:#091E4A;">
                    <i class="fab fa-android text-2xl text-white"></i>
                    Unduh APK SIGAP Sekarang
                    <i class="fas fa-arrow-down"></i>
                </a>
                <p class="text-gray-400 text-xs mt-3 font-semibold">
                    Gratis &nbsp;•&nbsp; Versi 1.0 &nbsp;•&nbsp; Android 8.0+
                </p>
            </div>

        </div>
    </div>
</section>
