{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- FITUR — resources/views/landing/_fitur.blade.php         --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<section id="fitur" class="py-20 lg:py-28" style="background:#EFF6FF;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        <div class="text-center mb-16 reveal">
            <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 mb-6 border"
                 style="background:rgba(15,45,107,0.08); border-color:rgba(15,45,107,0.12);">
                <i class="fas fa-star text-sm" style="color:#F59E0B"></i>
                <span class="text-xs font-bold uppercase tracking-wider" style="color:#0F2D6B">Keunggulan Aplikasi</span>
            </div>
            <h2 class="text-3xl sm:text-4xl font-black mb-4" style="color:#0F2D6B">
                Fitur Unggulan <span style="color:#F59E0B">SIGAP</span>
            </h2>
            <p class="text-gray-500 text-base max-w-xl mx-auto font-medium">
                Dirancang agar laporan Anda sampai ke tangan yang tepat, lengkap, akurat, dan cepat ditangani.
            </p>
        </div>

        {{-- Feature cards --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

            @foreach([
                [
                    'icon'       => 'fa-map-marker-alt',
                    'icon_color' => '#0F2D6B',
                    'icon_bg'    => '#DBEAFE',
                    'title'      => 'Lokasi Sangat Presisi',
                    'desc'       => 'Setiap laporan dilengkapi koordinat GPS otomatis yang tepat. Petugas langsung tahu lokasi kerusakan — tanpa perlu tebak-tebakan atau penjelasan panjang.',
                    'tags'       => [['GPS Otomatis','#DBEAFE','#1e40af'],['Geotagging','#DBEAFE','#1e40af'],['Peta Interaktif','#DBEAFE','#1e40af']],
                    'delay'      => '0s',
                ],
                [
                    'icon'       => 'fa-camera',
                    'icon_color' => '#D97706',
                    'icon_bg'    => '#FEF3C7',
                    'title'      => 'Kirim Bukti Langsung',
                    'desc'       => 'Unggah foto kondisi lapangan langsung dari kamera atau galeri HP. Bukti visual mempercepat verifikasi dan penanganan oleh dinas secara signifikan.',
                    'tags'       => [['Upload Foto','#FEF3C7','#92400e'],['Bukti Lapangan','#FEF3C7','#92400e'],['Instan','#FEF3C7','#92400e']],
                    'delay'      => '0.15s',
                ],
                [
                    'icon'       => 'fa-satellite-dish',
                    'icon_color' => '#15803d',
                    'icon_bg'    => '#D1FAE5',
                    'title'      => 'Pantau Real-Time',
                    'desc'       => 'Lacak status penanganan laporan Anda secara langsung dari HP. Dari "Diterima" hingga "Selesai" — semua progres transparan di depan mata Anda.',
                    'tags'       => [['Status Update','#D1FAE5','#14532d'],['Transparan','#D1FAE5','#14532d'],['Notifikasi Push','#D1FAE5','#14532d']],
                    'delay'      => '0.3s',
                ],
            ] as $feature)
            <div class="feature-card reveal bg-white rounded-3xl p-8 shadow-sm border border-gray-100"
                 style="transition-delay:{{ $feature['delay'] }}">
                {{-- Icon --}}
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6"
                     style="background:{{ $feature['icon_bg'] }};">
                    <i class="fas {{ $feature['icon'] }} text-3xl" style="color:{{ $feature['icon_color'] }}"></i>
                </div>

                <h3 class="text-xl font-black mb-3" style="color:#0F2D6B">{{ $feature['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-5">{{ $feature['desc'] }}</p>

                <div class="flex flex-wrap gap-2">
                    @foreach($feature['tags'] as [$tag, $bg, $color])
                    <span class="text-xs font-bold px-3 py-1 rounded-full"
                          style="background:{{ $bg }}; color:{{ $color }}">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>

        {{-- Feature highlight banner --}}
        <div class="mt-10 reveal">
            <div class="rounded-3xl p-8 lg:p-10 text-white relative overflow-hidden" style="background:#0F2D6B;">
                <div class="blob w-64 h-64 bg-blue-400 -top-20 -right-20"></div>
                <div class="relative z-10 grid sm:grid-cols-3 gap-6 text-center">
                    @foreach([
                        ['fa-shield-alt', 'Data Aman & Terenkripsi',   'Privasi Anda terjaga penuh'],
                        ['fa-bolt',       'Proses Cepat',               'Laporan langsung masuk sistem dinas'],
                        ['fa-headset',    'Dukungan Teknis',            'Tim kami siap membantu Anda'],
                    ] as [$icon, $title, $sub])
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3"
                             style="background:rgba(255,255,255,0.1)">
                            <i class="fas {{ $icon }} text-xl" style="color:#F59E0B"></i>
                        </div>
                        <p class="font-black text-base">{{ $title }}</p>
                        <p class="text-blue-200 text-sm mt-1 font-medium">{{ $sub }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</section>
