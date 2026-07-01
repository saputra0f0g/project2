{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- TENTANG & MITRA — resources/views/landing/_tentang.blade.php --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<section id="tentang" class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid lg:grid-cols-2 gap-16 items-center">

            {{-- ── Left: About text ── --}}
            <div class="reveal">
                <div class="inline-flex items-center gap-2 bg-sky-soft border border-blue-100 rounded-full px-4 py-2 mb-6">
                    <i class="fas fa-info-circle text-navy text-sm" style="color:#0F2D6B"></i>
                    <span class="text-xs font-bold uppercase tracking-wider" style="color:#0F2D6B">Tentang SIGAP</span>
                </div>

                <h2 class="text-3xl sm:text-4xl font-black mb-6 leading-tight" style="color:#0F2D6B">
                    Inovasi Digital Terpadu untuk
                    <span style="color:#F59E0B">Infrastruktur Lebih Baik</span>
                </h2>

                {{-- Logo besar SIGAP PUPR --}}
                <div class="mb-6">
                    <img src="{{ asset('gambar/puprsigap1.png') }}"
                         alt="SIGAP PUPR Subang"
                         class="h-14 object-contain">
                </div>

                <p class="text-gray-600 text-base leading-relaxed mb-4">
                    <strong style="color:#0F2D6B">SIGAP</strong> — <em>Sistem Informasi Geografis dan Pengaduan Publik</em> —
                    adalah platform digital terintegrasi yang dirancang khusus oleh
                    <strong style="color:#0F2D6B">Dinas PUPR Kabupaten Subang</strong> bersama <strong style="color:#0F2D6B">AmoebIT</strong>
                    untuk merevolusi cara warga berpartisipasi dalam perawatan infrastruktur daerah.
                </p>
                <p class="text-gray-600 text-base leading-relaxed mb-8">
                    Dengan SIGAP, setiap warga Subang dapat menjadi "mata" pemerintah di lapangan —
                    melaporkan kerusakan jalan, jembatan, saluran air, dan fasilitas publik lainnya secara instan,
                    lengkap dengan bukti foto dan titik GPS yang presisi.
                </p>

                <div class="flex flex-wrap gap-3">
                    @foreach([
                        ['fa-mobile-alt',  '#EFF6FF', '#0F2D6B', '100% Mobile-First'],
                        ['fa-lock',        '#FFFBEB', '#D97706', 'Data Terenkripsi'],
                        ['fa-sync-alt',    '#F0FDF4', '#15803d', 'Update Real-Time'],
                    ] as [$icon, $bg, $color, $text])
                    <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl border"
                         style="background:{{ $bg }}; border-color:{{ $bg }};">
                        <i class="fas {{ $icon }} text-base" style="color:{{ $color }}"></i>
                        <span class="text-sm font-bold" style="color:#0F2D6B">{{ $text }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Right: Partners ── --}}
            <div class="reveal">
                <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-8 text-center flex items-center justify-center gap-2">
                    <i class="fas fa-handshake" style="color:#F59E0B"></i>
                    Didukung Oleh
                </p>

                <div class="space-y-5">

                    {{-- Partner 1: PUPR --}}
                    <div class="flex items-center gap-5 p-6 rounded-2xl border-2 border-gray-100 hover:border-blue-200 hover:shadow-xl transition-all duration-300 group">
                        <div class="w-20 h-20 bg-sky-soft rounded-2xl flex items-center justify-center flex-shrink-0 border border-blue-100 group-hover:scale-105 transition-transform">
                            <img src="{{ asset('gambar/puprlogo.png') }}"
                                 alt="Logo PUPR"
                                 class="w-14 h-14 object-contain">
                        </div>
                        <div>
                            <p class="font-black text-lg leading-tight" style="color:#0F2D6B">Dinas PUPR</p>
                            <p class="text-gray-500 text-sm font-semibold">Kabupaten Subang</p>
                            <p class="text-gray-400 text-xs mt-1 leading-relaxed">
                                Pekerjaan Umum & Penataan Ruang — Pengelola infrastruktur daerah Kab. Subang
                            </p>
                        </div>
                    </div>

                    {{-- Partner 2: AmoebIT --}}
                    <div class="flex items-center gap-5 p-6 rounded-2xl border-2 border-gray-100 hover:border-yellow-200 hover:shadow-xl transition-all duration-300 group">
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center flex-shrink-0 border border-yellow-100 group-hover:scale-105 transition-transform"
                             style="background:#FFFBEB;">
                            <div class="text-center">
                                <i class="fas fa-code text-2xl mb-1 block" style="color:#D97706"></i>
                                <p class="text-[8px] font-black leading-none" style="color:#D97706">AmoebIT</p>
                            </div>
                        </div>
                        <div>
                            <p class="font-black text-lg leading-tight" style="color:#0F2D6B">AmoebIT</p>
                            <p class="text-gray-500 text-sm font-semibold">Technology Partner</p>
                            <p class="text-gray-400 text-xs mt-1 leading-relaxed">
                                Pengembang aplikasi mobile & web terintegrasi sistem pelaporan SIGAP
                            </p>
                        </div>
                    </div>

                </div>

                {{-- Tagline --}}
                <div class="mt-6 text-center p-4 rounded-2xl" style="background:#EFF6FF;">
                    <img src="{{ asset('gambar/puprsigap.png') }}" alt="SIGAP PUPR" class="h-8 object-contain mx-auto mb-2">
                    <p class="text-xs font-semibold" style="color:#0F2D6B">
                        "Bersama Membangun Subang yang Lebih Baik"
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>
