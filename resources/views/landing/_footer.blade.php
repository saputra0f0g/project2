{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- FOOTER — resources/views/landing/_footer.blade.php       --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<footer style="background:#0F2D6B; color:white;">

    {{-- Main footer content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10">

            {{-- ── Brand & Tagline ── --}}
            <div class="lg:col-span-2">
                {{-- Logo + app name --}}
                <div class="flex items-center gap-4 mb-5">
                    <img src="{{ asset('gambar/puprlogo.png') }}"
                         alt="Logo PUPR"
                         class="h-12 w-12 object-contain flex-shrink-0">
                    <div>
                        <p class="font-black text-2xl leading-none">SIGAP</p>
                        <p class="text-xs font-semibold tracking-wide mt-0.5" style="color:#93c5fd;">
                            Sistem Informasi Geografis & Pengaduan Publik
                        </p>
                    </div>
                </div>

                <p class="text-sm leading-relaxed mb-6 max-w-sm font-medium" style="color:#bfdbfe;">
                    Platform pelaporan infrastruktur digital resmi Dinas PUPR Kabupaten Subang.
                    Bersama kita wujudkan Subang yang lebih baik, satu laporan per langkah.
                </p>

                {{-- Social links --}}
                <div class="flex gap-3">
                    @foreach([
                        ['fab fa-instagram', '#'],
                        ['fab fa-facebook',  '#'],
                        ['fab fa-youtube',   '#'],
                        ['fab fa-whatsapp',  '#'],
                    ] as [$icon, $href])
                    <a href="{{ $href }}"
                       class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5"
                       style="background:rgba(255,255,255,0.1);"
                       onmouseover="this.style.background='#F59E0B'; this.style.color='#0F2D6B';"
                       onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.color='white';">
                        <i class="{{ $icon }} text-sm"></i>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- ── Kontak ── --}}
            <div>
                <h4 class="font-black text-base mb-5" style="color:#F59E0B">Kontak Kami</h4>
                <ul class="space-y-4 text-sm">
                    <li class="flex gap-3 items-start">
                        <i class="fas fa-map-marker-alt mt-0.5 flex-shrink-0" style="color:#F59E0B"></i>
                        <p style="color:#bfdbfe; line-height:1.6;">
                            Jl. RA Kartini No. 1,<br>Subang, Jawa Barat 41211
                        </p>
                    </li>
                    <li class="flex gap-3 items-center">
                        <i class="fas fa-phone flex-shrink-0" style="color:#F59E0B"></i>
                        <a href="tel:+62260123456"
                           class="transition-colors"
                           style="color:#bfdbfe;"
                           onmouseover="this.style.color='white';"
                           onmouseout="this.style.color='#bfdbfe';">(0260) 123-456</a>
                    </li>
                    <li class="flex gap-3 items-center">
                        <i class="fas fa-envelope flex-shrink-0" style="color:#F59E0B"></i>
                        <a href="mailto:sigap@puprsubang.go.id"
                           class="transition-colors break-all"
                           style="color:#bfdbfe;"
                           onmouseover="this.style.color='white';"
                           onmouseout="this.style.color='#bfdbfe';">sigap@puprsubang.go.id</a>
                    </li>
                    <li class="flex gap-3 items-center">
                        <i class="fas fa-clock flex-shrink-0" style="color:#F59E0B"></i>
                        <p style="color:#bfdbfe;">Sen – Jum: 08.00 – 16.00 WIB</p>
                    </li>
                </ul>
            </div>

            {{-- ── Tautan Cepat ── --}}
            <div>
                <h4 class="font-black text-base mb-5" style="color:#F59E0B">Tautan Cepat</h4>
                <ul class="space-y-3 text-sm">
                    @foreach([
                        ['#beranda',     'Beranda'],
                        ['#tentang',     'Tentang SIGAP'],
                        ['#fitur',       'Fitur Unggulan'],
                        ['#cara-instal', 'Cara Instalasi'],
                        ['#bantuan',     'Pusat Bantuan'],
                        [route('login'), 'Login Internal'],
                    ] as [$href, $label])
                    <li>
                        <a href="{{ $href }}"
                           class="flex items-center gap-2 font-medium transition-colors"
                           style="color:#bfdbfe;"
                           onmouseover="this.style.color='#F59E0B';"
                           onmouseout="this.style.color='#bfdbfe';">
                            <i class="fas fa-chevron-right text-[9px]" style="color:#F59E0B; opacity:0.6;"></i>
                            {{ $label }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t" style="border-color:rgba(255,255,255,0.1);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col sm:flex-row gap-3 items-center justify-between text-xs font-semibold" style="color:#93c5fd;">
                <p>
                    <i class="fas fa-info-circle mr-1"></i>
                    <em>Saat ini aplikasi SIGAP baru tersedia untuk pengguna Android.</em>
                </p>
                <p>© 2026 Dinas PUPR Kabupaten Subang &amp; AmoebIT. Hak cipta dilindungi.</p>
            </div>
        </div>
    </div>

</footer>
