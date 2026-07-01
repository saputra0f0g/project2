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

                    {{-- Phone outer frame --}}
                    <div class="relative phone-shadow">
                        <div class="w-[260px] sm:w-[272px] bg-gray-900 rounded-[3.2rem] p-[10px] border-[5px] border-gray-700 shadow-2xl">

                            {{-- Notch --}}
                            <div class="absolute top-[18px] left-1/2 -translate-x-1/2 w-24 h-[22px] bg-gray-900 rounded-full z-10 flex items-center justify-center gap-2">
                                <div class="w-2 h-2 bg-gray-700 rounded-full"></div>
                                <div class="w-8 h-[5px] bg-gray-700 rounded-full"></div>
                            </div>

                            {{-- Phone screen — replica of beranda masyarakat (#F8F9FB background) --}}
                            <div class="rounded-[2.6rem] overflow-hidden" style="background:#F8F9FB; aspect-ratio:9/19; display:flex; flex-direction:column;">

                                {{-- Status bar --}}
                                <div style="background:#1F3B6D; padding:10px 14px 6px; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
                                    <span style="color:white; font-size:9px; font-weight:700;">9:41</span>
                                    <div style="display:flex; gap:5px; align-items:center;">
                                        <i class="fas fa-signal" style="color:white; font-size:7px;"></i>
                                        <i class="fas fa-wifi" style="color:white; font-size:7px;"></i>
                                        <i class="fas fa-battery-three-quarters" style="color:white; font-size:7px;"></i>
                                    </div>
                                </div>

                                {{-- Scrollable content area --}}
                                <div style="flex:1; overflow:hidden; display:flex; flex-direction:column; gap:0;">

                                    {{-- Welcome banner (welcomeSection) --}}
                                    <div style="padding:12px 14px 8px; display:flex; align-items:flex-start; justify-content:space-between; background:#fff; border-bottom:1px solid #F1F5F9;">
                                        <div style="flex:1;">
                                            <p style="color:#1F3B6D; font-size:7px; font-weight:800; letter-spacing:1px; margin-bottom:2px;">DASHBOARD UTAMA</p>
                                            <p style="font-size:8px; color:#555; margin-bottom:1px;">Selamat Datang,</p>
                                            <p style="font-size:11px; font-weight:700; color:#1F3B6D; margin-bottom:2px;">Budi Santoso 👋</p>
                                            <p style="font-size:7px; color:#888; line-height:1.4;">Pantau laporan infrastruktur<br>Anda secara real-time.</p>
                                        </div>
                                        {{-- Tombol Laporan Masalah --}}
                                        <div style="background:#F4C430; padding:7px 8px; border-radius:8px; display:flex; flex-direction:column; align-items:center; margin-left:8px; min-width:52px;">
                                            <i class="fas fa-file-alt" style="color:white; font-size:11px; margin-bottom:3px;"></i>
                                            <span style="color:white; font-size:7px; font-weight:700; text-align:center; line-height:1.2;">Laporan<br>Masalah</span>
                                        </div>
                                    </div>

                                    {{-- Map placeholder (mapContainer) --}}
                                    <div style="margin:8px 12px; border-radius:10px; overflow:hidden; background:#d1d5db; height:68px; position:relative; flex-shrink:0;">
                                        {{-- Fake map tiles --}}
                                        <div style="width:100%; height:100%; background:linear-gradient(135deg, #c8d6e5 0%, #b8cad8 40%, #a8bfce 100%); position:relative;">
                                            {{-- Fake road lines --}}
                                            <div style="position:absolute; top:50%; left:0; right:0; height:1.5px; background:rgba(255,255,255,0.6);"></div>
                                            <div style="position:absolute; top:30%; left:40%; bottom:0; width:1.5px; background:rgba(255,255,255,0.6);"></div>
                                            {{-- Map markers --}}
                                            <div style="position:absolute; top:30%; left:25%;">
                                                <div style="width:8px; height:8px; border-radius:50%; background:#F59E0B; border:1.5px solid white; box-shadow:0 1px 3px rgba(0,0,0,0.3);"></div>
                                            </div>
                                            <div style="position:absolute; top:55%; left:55%;">
                                                <div style="width:8px; height:8px; border-radius:50%; background:#3B82F6; border:1.5px solid white; box-shadow:0 1px 3px rgba(0,0,0,0.3);"></div>
                                            </div>
                                            <div style="position:absolute; top:25%; left:65%;">
                                                <div style="width:8px; height:8px; border-radius:50%; background:#10B981; border:1.5px solid white; box-shadow:0 1px 3px rgba(0,0,0,0.3);"></div>
                                            </div>
                                        </div>
                                        {{-- Map label overlay --}}
                                        <div style="position:absolute; top:6px; left:7px; background:rgba(255,255,255,0.95); border-radius:12px; padding:2px 7px; display:flex; align-items:center; gap:3px;">
                                            <i class="fas fa-map-marker-alt" style="color:#1F3B6D; font-size:7px;"></i>
                                            <span style="font-size:7px; font-weight:700; color:#1F3B6D;">Peta Laporan</span>
                                            <span style="background:#1F3B6D; color:white; font-size:6px; font-weight:700; padding:1px 4px; border-radius:6px;">3 titik</span>
                                        </div>
                                        {{-- Legend --}}
                                        <div style="position:absolute; bottom:5px; left:7px; background:rgba(255,255,255,0.9); border-radius:10px; padding:3px 7px; display:flex; gap:7px; align-items:center;">
                                            <div style="display:flex; align-items:center; gap:2px;">
                                                <div style="width:5px; height:5px; border-radius:50%; background:#F59E0B;"></div>
                                                <span style="font-size:6px; color:#333; font-weight:600;">Menunggu</span>
                                            </div>
                                            <div style="display:flex; align-items:center; gap:2px;">
                                                <div style="width:5px; height:5px; border-radius:50%; background:#3B82F6;"></div>
                                                <span style="font-size:6px; color:#333; font-weight:600;">Proses</span>
                                            </div>
                                            <div style="display:flex; align-items:center; gap:2px;">
                                                <div style="width:5px; height:5px; border-radius:50%; background:#10B981;"></div>
                                                <span style="font-size:6px; color:#333; font-weight:600;">Selesai</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Stats card (statsCard) --}}
                                    <div style="margin:0 12px 8px; background:#1F3B6D; border-radius:10px; padding:10px 12px; position:relative; overflow:hidden; flex-shrink:0;">
                                        <p style="color:rgba(255,255,255,0.7); font-size:7px; font-weight:700; letter-spacing:1px; margin-bottom:2px;">LAPORAN SAYA</p>
                                        <p style="color:white; font-size:22px; font-weight:700; margin-bottom:6px; line-height:1;">12</p>
                                        <div style="display:flex; gap:4px; flex-wrap:wrap;">
                                            <span style="background:rgba(255,255,255,0.15); color:white; font-size:7px; font-weight:600; padding:2px 6px; border-radius:10px;">⏳ 3 Menunggu</span>
                                            <span style="background:rgba(16,185,129,0.3); color:white; font-size:7px; font-weight:600; padding:2px 6px; border-radius:10px;">✔ 8 Selesai</span>
                                            <span style="background:rgba(59,130,246,0.3); color:white; font-size:7px; font-weight:600; padding:2px 6px; border-radius:10px;">🔧 1 Proses</span>
                                        </div>
                                        {{-- Background icon --}}
                                        <i class="fas fa-copy" style="position:absolute; bottom:-8px; right:-4px; font-size:30px; color:rgba(255,255,255,0.08);"></i>
                                    </div>

                                    {{-- Status Laporan Terbaru (progressSection) --}}
                                    <div style="margin:0 12px 8px; background:#fff; border-radius:10px; padding:10px 11px; flex-shrink:0; box-shadow:0 1px 4px rgba(0,0,0,0.05);">
                                        <p style="font-size:7px; font-weight:800; color:#94A3B8; letter-spacing:1px; margin-bottom:8px;">STATUS LAPORAN TERBARU</p>

                                        {{-- Progress item 1 --}}
                                        <div style="margin-bottom:8px;">
                                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                                <span style="font-size:7px; font-weight:600; color:#1E293B; flex:1; margin-right:4px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">LP-001 — Jalan Berlubang Parah</span>
                                                <span style="background:rgba(59,130,246,0.13); color:#3B82F6; font-size:6px; font-weight:700; padding:1px 5px; border-radius:8px; white-space:nowrap;">Diproses</span>
                                            </div>
                                            <div style="height:4px; background:#F1F5F9; border-radius:3px; margin-bottom:1px;">
                                                <div style="height:4px; width:55%; background:#3B82F6; border-radius:3px;"></div>
                                            </div>
                                            <span style="font-size:6px; color:#94A3B8; float:right;">55%</span>
                                        </div>

                                        {{-- Progress item 2 --}}
                                        <div style="clear:both;">
                                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                                <span style="font-size:7px; font-weight:600; color:#1E293B; flex:1; margin-right:4px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">LP-002 — Drainase Tersumbat</span>
                                                <span style="background:rgba(16,185,129,0.13); color:#10B981; font-size:6px; font-weight:700; padding:1px 5px; border-radius:8px; white-space:nowrap;">Selesai</span>
                                            </div>
                                            <div style="height:4px; background:#F1F5F9; border-radius:3px; margin-bottom:1px;">
                                                <div style="height:4px; width:100%; background:#10B981; border-radius:3px;"></div>
                                            </div>
                                            <span style="font-size:6px; color:#94A3B8; float:right;">100%</span>
                                        </div>

                                        <div style="clear:both; margin-top:7px; text-align:center;">
                                            <span style="color:#1F3B6D; font-size:8px; font-weight:700;">Lihat Semua Laporan →</span>
                                        </div>
                                    </div>

                                    {{-- Hasil Kerja (gallery header) --}}
                                    <div style="margin:0 12px 6px; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
                                        <span style="font-size:10px; font-weight:700; color:#1E293B;">Hasil Kerja</span>
                                        <span style="color:#1F3B6D; font-size:7px; font-weight:700;">Lihat Semua ›</span>
                                    </div>

                                    {{-- Gallery masonry (2 cols) --}}
                                    <div style="margin:0 10px; display:grid; grid-template-columns:1fr 1fr; gap:5px; flex-shrink:0;">
                                        <div style="border-radius:8px; overflow:hidden; background:#c8d6e5; height:52px; position:relative;">
                                            <div style="width:100%; height:100%; background:linear-gradient(135deg,#7f9cbf,#5a7fa0);"></div>
                                            <div style="position:absolute; top:4px; right:4px; background:rgba(0,0,0,0.5); border-radius:10px; padding:1px 5px;">
                                                <span style="color:white; font-size:6px; font-weight:700;">Jalan</span>
                                            </div>
                                            <div style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.4); padding:3px 5px;">
                                                <span style="color:white; font-size:6px; font-weight:600;">Perbaikan Jl. Sudirman</span>
                                            </div>
                                        </div>
                                        <div style="border-radius:8px; overflow:hidden; background:#b8cad8; height:52px; position:relative;">
                                            <div style="width:100%; height:100%; background:linear-gradient(135deg,#8ba5b5,#6a8a9e);"></div>
                                            <div style="position:absolute; top:4px; right:4px; background:rgba(0,0,0,0.5); border-radius:10px; padding:1px 5px;">
                                                <span style="color:white; font-size:6px; font-weight:700;">Drainase</span>
                                            </div>
                                            <div style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.4); padding:3px 5px;">
                                                <span style="color:white; font-size:6px; font-weight:600;">Saluran Air Beres</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>{{-- end scrollable --}}
                            </div>{{-- end screen --}}
                        </div>

                        {{-- Side buttons --}}
                        <div class="absolute -right-[5px] top-24 w-[4px] h-10 bg-gray-600 rounded-full"></div>
                        <div class="absolute -left-[5px] top-20 w-[4px] h-8 bg-gray-600 rounded-full"></div>
                        <div class="absolute -left-[5px] top-32 w-[4px] h-8 bg-gray-600 rounded-full"></div>
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
