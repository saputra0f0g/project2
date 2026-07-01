{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- HERO — resources/views/landing/_hero.blade.php           --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<section id="beranda" class="hero-bg min-h-screen flex items-center pt-5 relative overflow-hidden">

    {{-- Decorative blobs --}}
    <div class="blob w-96 h-96 bg-blue-400  top-0    -left-24"></div>
    <div class="blob w-72 h-72 bg-gold      top-1/2  -right-20"></div>
    <div class="blob w-56 h-56 bg-blue-300  bottom-0  left-1/3"></div>

    {{-- Dot-grid overlay --}}
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image:radial-gradient(circle,white 1px,transparent 1px);background-size:32px 32px;"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28 w-full">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- ── Text Column ── --}}
            <div class="text-white">

                {{-- Headline --}}
                <h1 class="text-4xl sm:text-5xl lg:text-[3.25rem] font-black leading-[1.1] mb-6 tracking-tight">
                    Lapor Kerusakan
                    <span class="text-gold">Infrastruktur</span>
                    Subang Kini Makin
                    <em class="not-italic text-blue-200">Cepat, Akurat,</em>
                    dan Transparan!
                </h1>

                {{-- Sub-headline --}}
                <p class="text-blue-100 text-base lg:text-lg leading-relaxed mb-10 max-w-xl font-medium">
                    SIGAP hadir sebagai jembatan digital antara warga Subang dan Dinas PUPR — memudahkan pelaporan kerusakan jalan, jembatan, dan infrastruktur publik langsung dari genggaman tangan Anda.
                    <strong class="text-white"> Laporan Anda diproses secara real-time dan transparan.</strong>
                </p>

                {{-- CTA Download --}}
                <a href="#download" id="download" class="btn-cta inline-flex items-center gap-3 px-7 py-4 rounded-2xl text-navy-dark font-black text-lg mb-5 group" style="color:#091E4A;">
                    <div class="w-10 h-10 bg-black/15 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fab fa-android text-xl text-white"></i>
                    </div>
                    <span>Unduh Aplikasi SIGAP (APK)</span>
                    <i class="fas fa-download group-hover:translate-y-0.5 transition-transform"></i>
                </a>

                {{-- Meta --}}
                <p class="text-blue-200 text-xs font-semibold mb-5">
                    <i class="fas fa-info-circle mr-1"></i>
                    Versi 1.0 &nbsp;|&nbsp; Ukuran: ~25 MB &nbsp;|&nbsp; Khusus Android 8.0+
                </p>
            </div>

            {{-- ── Phone Mockup Column ── --}}
            <div class="flex justify-center lg:justify-end animate-float">
                <div class="relative">
                    {{-- Glow --}}
                    <div class="absolute inset-0 bg-gold/25 blur-3xl rounded-full scale-75 translate-y-12 pointer-events-none"></div>

                    {{-- Screenshot mockup --}}
                    <div class="relative phone-shadow">
                        <img src="{{ asset('gambar/mockup.png') }}"
                             alt="Screenshot Aplikasi SIGAP PUPR"
                             class="w-[260px] sm:w-[272px] rounded-[2.8rem] shadow-2xl border-[5px] border-gray-700">
                    </div>

                    {{-- Floating chips --}}
                    <div class="absolute -top-4 -right-6 bg-green-500 text-white rounded-2xl px-3 py-2 shadow-xl text-xs font-black">
                        <i class="fas fa-map-marker-alt mr-1"></i> GPS Aktif
                    </div>
                    <div class="absolute -bottom-4 -left-6 bg-white rounded-2xl px-3 py-2 shadow-xl text-xs font-black" style="color:#0F2D6B">
                        <i class="fas fa-bolt mr-1" style="color:#F59E0B"></i> Real-Time
                    </div>
                </div>
            </div>

        </div>

        {{-- Stats bar --}}
        <div class="mt-16 grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach([
                ['1.200+',  'Laporan Diterima',  'fa-file-alt'],
                ['87%',     'Diselesaikan',       'fa-check-circle'],
                ['48 Jam',  'Rata-rata Respons',  'fa-clock'],
                ['6 Bidang','PUPR Terkoneksi',    'fa-building'],
            ] as [$num, $label, $icon])
            <div class="glass rounded-2xl p-4 text-center">
                <i class="fas {{ $icon }} text-gold text-xl mb-2"></i>
                <p class="text-white font-black text-2xl">{{ $num }}</p>
                <p class="text-blue-200 text-xs font-semibold mt-1">{{ $label }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Wave divider --}}
    <div class="absolute bottom-0 left-0 right-0 pointer-events-none">
        <svg viewBox="0 0 1440 72" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0 72L60 62C120 52 240 34 360 29C480 24 600 34 720 39C840 44 960 44 1080 39C1200 34 1320 24 1380 19L1440 14V72H0Z" fill="white"/>
        </svg>
    </div>
</section>
