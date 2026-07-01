{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- BANTUAN / FAQ — resources/views/landing/_bantuan.blade.php --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<section id="bantuan" class="py-20 lg:py-28" style="background:#EFF6FF;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        <div class="text-center mb-14 reveal">
            <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 mb-6 border"
                 style="background:rgba(15,45,107,0.08); border-color:rgba(15,45,107,0.12);">
                <i class="fas fa-question-circle text-sm" style="color:#0F2D6B"></i>
                <span class="text-xs font-bold uppercase tracking-wider" style="color:#0F2D6B">Pusat Bantuan</span>
            </div>
            <h2 class="text-3xl sm:text-4xl font-black mb-4" style="color:#0F2D6B">
                Pertanyaan yang Sering Ditanya
            </h2>
            <p class="text-gray-500 text-base font-medium max-w-xl mx-auto">
                Butuh bantuan? Temukan jawaban dari pertanyaan umum di bawah ini.
                Jika belum terjawab, hubungi kami langsung.
            </p>
        </div>

        {{-- FAQ Accordion --}}
        <div class="space-y-4">
            @foreach([
                [
                    'q' => 'Apakah SIGAP gratis untuk digunakan?',
                    'a' => 'Ya, aplikasi SIGAP sepenuhnya gratis untuk seluruh warga Kabupaten Subang. Tidak ada biaya pendaftaran, biaya langganan, maupun biaya penggunaan apapun.',
                ],
                [
                    'q' => 'Apakah SIGAP tersedia untuk iPhone (iOS)?',
                    'a' => 'Saat ini SIGAP baru tersedia untuk perangkat Android. Versi iOS sedang dalam tahap pengembangan dan akan segera hadir. Pantau terus halaman ini untuk informasi terbaru.',
                ],
                [
                    'q' => 'Apakah data pribadi saya aman?',
                    'a' => 'Keamanan data adalah prioritas utama kami. Semua data dienkripsi menggunakan protokol standar industri dan tidak dibagikan ke pihak ketiga manapun. Sistem SIGAP dikelola langsung oleh Dinas PUPR Kab. Subang.',
                ],
                [
                    'q' => 'Apa yang terjadi setelah saya mengirim laporan?',
                    'a' => 'Laporan Anda langsung masuk ke sistem Admin PUPR dan akan diverifikasi. Anda dapat memantau status penanganan secara real-time melalui aplikasi — mulai dari "Diterima", "Diproses", hingga "Selesai". Rata-rata respons awal dalam 48 jam kerja.',
                ],
                [
                    'q' => 'Aplikasi tidak bisa diinstal, apa yang harus dilakukan?',
                    'a' => 'Pastikan Anda sudah mengaktifkan opsi "Izinkan dari sumber ini" (Allow from this source) di pengaturan Android Anda. Lihat panduan instalasi di atas. Jika masih mengalami masalah, hubungi kami melalui nomor telepon atau email yang tertera di footer halaman ini.',
                ],
                [
                    'q' => 'Jenis kerusakan apa saja yang bisa dilaporkan?',
                    'a' => 'Anda dapat melaporkan berbagai kerusakan infrastruktur yang menjadi tanggung jawab Dinas PUPR, seperti: jalan berlubang, jembatan rusak, saluran drainase tersumbat, lampu jalan mati, dan fasilitas publik lainnya di wilayah Kabupaten Subang.',
                ],
            ] as $i => $faq)
            <div class="reveal faq-item bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm"
                 style="transition-delay:{{ $i * 0.08 }}s">
                <button class="faq-btn w-full flex items-center justify-between p-6 text-left font-bold hover:bg-sky-soft transition-colors duration-200"
                        style="color:#0F2D6B;"
                        onclick="toggleFaq(this)">
                    <span class="text-sm sm:text-base pr-4">{{ $faq['q'] }}</span>
                    <i class="fas fa-chevron-down flex-shrink-0 transition-transform duration-300" style="color:#F59E0B"></i>
                </button>
                <div class="faq-answer overflow-hidden transition-all duration-400 ease-in-out" style="max-height:0;">
                    <p class="px-6 pb-6 text-gray-500 text-sm leading-relaxed">{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>
