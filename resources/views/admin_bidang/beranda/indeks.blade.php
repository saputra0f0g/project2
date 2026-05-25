@extends('layouts.app_bidang')

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-popup-content-wrapper { border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15); }
    .leaflet-popup-content { margin: 16px; }
    .z-menu { z-index: 2000 !important; }

    /* Animasi Elegan */
    @keyframes fadeUpMasuk {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animasi-masuk {
        animation: fadeUpMasuk 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
</style>
@endpush

@section('konten')
<div class="max-w-7xl mx-auto pb-10">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 animasi-masuk delay-1">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 mb-2">Dashboard {{ $bidang->nama_bidang }}</h2>
            <div class="flex items-center gap-3">
                <span class="bg-yellow-400 text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Karyawan Bidang</span>
                <span class="text-xs text-gray-500 font-medium flex items-center">
                    <i class="far fa-calendar-alt mr-1.5"></i> {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6 animasi-masuk delay-2">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group hover:border-blue-200 transition">
            <div class="flex justify-between items-start">
                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Laporan Masuk</h4>
                <i class="fas fa-inbox text-gray-200 text-xl group-hover:text-blue-100 transition"></i>
            </div>
            <div>
                <h2 class="text-3xl font-extrabold text-gray-800">{{ $total_laporan ?? 0 }}</h2>
                <p class="text-[10px] text-gray-400 font-medium mt-1">Total Laporan Bidang Ini</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group hover:border-yellow-200 transition">
            <div class="flex justify-between items-start">
                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Laporan Mendesak</h4>
                <i class="fas fa-exclamation-circle text-gray-200 text-xl group-hover:text-yellow-100 transition"></i>
            </div>
            <div>
                <h2 class="text-3xl font-extrabold text-gray-800">{{ $laporan_mendesak ?? 0 }}</h2>
                <p class="text-[10px] text-gray-400 font-medium mt-1"><span class="text-yellow-600 font-bold bg-yellow-50 px-1.5 py-0.5 rounded border border-yellow-100">Menunggu Validasi</span></p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group hover:border-blue-200 transition">
            <div class="flex justify-between items-start">
                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pekerjaan Berjalan</h4>
                <i class="fas fa-tools text-gray-200 text-xl group-hover:text-blue-100 transition"></i>
            </div>
            <div>
                <h2 class="text-3xl font-extrabold text-gray-800">{{ $laporan_proses ?? 0 }}</h2>
                <p class="text-[10px] text-gray-400 font-medium mt-1">Sedang ditangani tim UPTD</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group hover:border-green-200 transition">
            <div class="flex justify-between items-start">
                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pekerjaan Selesai</h4>
                <i class="fas fa-check-circle text-gray-200 text-xl group-hover:text-green-100 transition"></i>
            </div>
            <div>
                <div class="flex justify-between items-end">
                    <h2 class="text-3xl font-extrabold text-gray-800">{{ $laporan_selesai ?? 0 }}</h2>
                    <span class="text-xs font-bold text-green-500 mb-1">Berhasil</span>
                </div>
            </div>
        </div>
    </div>

    <div id="peta-container" class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 overflow-hidden flex flex-col transition-all duration-300 animasi-masuk delay-3">
        <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center bg-white z-10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-sm">Pemantauan Terkini</h3>
                    <p class="text-[10px] text-gray-400">Pemantauan unit operasional {{ $bidang->nama_bidang }} di wilayah Subang</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="relative">
                    <button id="btn-layer" onclick="toggleMenuPeta('menu-layer')" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 flex items-center justify-center transition focus:outline-none" title="Ganti Tampilan Peta">
                        <i class="fas fa-layer-group text-xs"></i>
                    </button>
                    <div id="menu-layer" class="hidden absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-xl border border-gray-100 z-menu p-2">
                        <button onclick="gantiLayerPeta('standar')" class="w-full text-left px-3 py-2 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">Peta Jalan</button>
                        <button onclick="gantiLayerPeta('satelit')" class="w-full text-left px-3 py-2 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">Citra Satelit</button>
                    </div>
                </div>

                <button id="btn-fullscreen" onclick="toggleFullscreen()" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 flex items-center justify-center transition focus:outline-none" title="Layar Penuh">
                    <i class="fas fa-expand text-xs" id="icon-fs"></i>
                </button>

                <span class="hidden md:flex ml-2 items-center text-[10px] font-bold text-green-600 bg-green-50 px-2.5 py-1 rounded-full border border-green-100">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span> Pembaruan Langsung
                </span>
            </div>
        </div>

        <div id="map-wrapper" class="relative w-full h-[450px] bg-gray-100 z-0 transition-all duration-300">
            <div id="mapDashboard" class="w-full h-full"></div>

            <div class="absolute bottom-6 left-6 bg-white/95 backdrop-blur shadow-lg border border-gray-100 rounded-xl p-4 z-[1000] w-48">
                <h4 class="text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-3">Status Operasional</h4>
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between text-xs font-medium text-gray-700">
                        <div class="flex items-center"><i class="fas fa-map-marker-alt text-yellow-500 w-3 text-center mr-2"></i> Proses</div>
                        <span class="font-bold text-gray-900">{{ $laporan_proses ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs font-medium text-gray-700">
                        <div class="flex items-center"><i class="fas fa-map-marker-alt text-red-500 w-3 text-center mr-2"></i> Mendesak</div>
                        <span class="font-bold text-gray-900">{{ $laporan_mendesak ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs font-medium text-gray-700">
                        <div class="flex items-center"><i class="fas fa-map-marker-alt text-green-500 w-3 text-center mr-2"></i> Selesai</div>
                        <span class="font-bold text-gray-900">{{ $laporan_selesai ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animasi-masuk delay-4">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold text-gray-800 text-sm">Laporan Masuk Terbaru</h3>
                <a href="{{ route('admin_bidang.laporan') }}" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 transition">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] text-gray-400 uppercase tracking-wider border-b border-gray-50">
                            <th class="pb-3 font-bold">ID Laporan</th>
                            <th class="pb-3 font-bold">Pelapor</th>
                            <th class="pb-3 font-bold">Kategori</th>
                            <th class="pb-3 font-bold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-600 divide-y divide-gray-50">
                        @forelse(collect($sebaran_laporan ?? [])->sortByDesc('created_at')->take(5) as $lap)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="py-3">
                                <a href="{{ route('admin_bidang.laporan.detail', $lap->id) }}" class="font-bold text-pupr-blue group-hover:underline">#{{ $lap->id_laporan }}</a>
                            </td>
                            <td class="py-3 font-medium text-gray-700">
                                {{ $lap->pelapor->nama_lengkap ?? 'Masyarakat Umum' }}
                            </td>
                            <td class="py-3 text-xs font-bold text-gray-500 uppercase">
                                {{ $lap->kategori_bidang }}
                            </td>
                            <td class="py-3 text-center">
                                @if($lap->status == 'proses')
                                    <span class="bg-yellow-50 text-yellow-600 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider border border-yellow-100">Proses</span>
                                @elseif($lap->status == 'selesai')
                                    <span class="bg-green-50 text-green-600 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider border border-green-100">Selesai</span>
                                @else
                                    <span class="bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider border border-blue-100">Diteruskan</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-xs text-gray-400">Belum ada laporan masuk terbaru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">
            <h3 class="font-bold text-gray-800 text-sm mb-5">Tim Pekerja (UPTD) Aktif</h3>

            <div class="flex-1 overflow-y-auto pr-2">
                @forelse($tim_pekerja ?? [] as $pekerja)
                <div class="flex items-center justify-between mb-4 last:mb-0 hover:bg-gray-50 p-2 rounded-lg transition -mx-2">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-blue-50 text-pupr-blue flex items-center justify-center text-xs font-bold border border-blue-100">
                            {{ substr($pekerja->nama_lengkap, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800 leading-tight">{{ $pekerja->nama_lengkap }}</p>
                            <p class="text-[10px] text-gray-500 mt-0.5"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> {{ $pekerja->kantor_wilayah ?? 'Subang' }}</p>
                        </div>
                    </div>
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-[0_0_5px_rgba(34,197,94,0.6)] animate-pulse" title="Akun Aktif"></span>
                </div>
                @empty
                <div class="py-8 text-center flex flex-col items-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-users-slash text-gray-300 text-xl"></i>
                    </div>
                    <p class="text-xs text-gray-400 font-medium">Belum ada Tim UPTD yang terdaftar dan aktif.</p>
                </div>
                @endforelse
            </div>

            <a href="{{ route('admin_bidang.monitoring') }}" class="w-full mt-4 py-2.5 bg-gray-50 hover:bg-gray-100 text-center text-gray-600 rounded-xl text-xs font-bold transition border border-gray-200 block shadow-sm">
                Kelola Seluruh Tim
            </a>
        </div>
    </div>

</div>
@endsection

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Fungsi Global untuk Show/Hide Deskripsi
    window.toggleDeskripsi = function(btn) {
        let parent = btn.parentElement;
        let isExpanded = btn.getAttribute('data-expanded') === 'true';
        let fullText = btn.getAttribute('data-full');
        let shortText = btn.getAttribute('data-short');

        // PERBAIKAN: Tambahkan event.stopPropagation() dan type="button"
        if (isExpanded) {
            parent.innerHTML = `${shortText} <button type="button" onclick="event.stopPropagation(); window.toggleDeskripsi(this)" data-full="${fullText}" data-short="${shortText}" data-expanded="false" class="text-blue-500 hover:text-blue-700 font-bold ml-1 transition">selengkapnya</button>`;
        } else {
            parent.innerHTML = `${fullText} <button type="button" onclick="event.stopPropagation(); window.toggleDeskripsi(this)" data-full="${fullText}" data-short="${shortText}" data-expanded="true" class="text-gray-400 hover:text-gray-600 font-bold ml-1 transition">Sembunyikan</button>`;
        }
    }

    let map, layerStandar, layerSatelit, markerGroup;
    let dataLaporan = @json($sebaran_laporan ?? []);

    document.addEventListener("DOMContentLoaded", function() {
        map = L.map('mapDashboard').setView([-6.5627, 107.7613], 12);

        layerStandar = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 20 });
        layerSatelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });

        layerStandar.addTo(map);
        markerGroup = L.layerGroup().addTo(map);

        renderMarkers();
    });

    function renderMarkers() {
        markerGroup.clearLayers();

        if(dataLaporan.length > 0) {
            dataLaporan.forEach(function(laporan) {
                if(laporan.lokasi_gps) {
                    let koordinat = laporan.lokasi_gps.split(',');
                    if(koordinat.length == 2) {
                        let lat = parseFloat(koordinat[0].trim());
                        let lng = parseFloat(koordinat[1].trim());

                        // Tentukan Warna Ikon berdasarkan status
                        let warnaIkon = 'text-red-500';
                        if(laporan.status === 'proses') warnaIkon = 'text-yellow-500';
                        if(laporan.status === 'selesai') warnaIkon = 'text-green-500';

                        let ikonCustom = L.divIcon({
                            className: 'bg-transparent',
                            html: `<div style="text-shadow: 2px 2px 4px rgba(0,0,0,0.4);" class="${warnaIkon} text-[38px] hover:scale-110 transition-transform cursor-pointer">
                                     <i class="fas fa-map-marker-alt"></i>
                                   </div>`,
                            iconSize: [30, 42], iconAnchor: [15, 40], popupAnchor: [0, -35]
                        });

                        // Ambil Data untuk Popup
                        let namaPelapor = (laporan.pelapor && laporan.pelapor.nama_lengkap) ? laporan.pelapor.nama_lengkap : 'Masyarakat Umum';
                        let kategori = laporan.kategori_bidang || 'Laporan';
                        let deskripsi = laporan.deskripsi_laporan || 'Tidak ada deskripsi.';

                        // PERBAIKAN: Bersihkan tanda kutip agar tidak merusak HTML
                        let teksAman = deskripsi.replace(/"/g, '&quot;').replace(/'/g, '&#39;');

                        // Logika Show More / Deskripsi Singkat
                        let batasKarakter = 60;
                        let deskripsiSingkat = deskripsi.length > batasKarakter ? deskripsi.substring(0, batasKarakter) + '...' : deskripsi;
                        let teksSingkatAman = deskripsiSingkat.replace(/"/g, '&quot;').replace(/'/g, '&#39;');

                        // PERBAIKAN: Tambahkan event.stopPropagation() dan type="button"
                        let btnSelengkapnya = deskripsi.length > batasKarakter
                            ? `<button type="button" onclick="event.stopPropagation(); window.toggleDeskripsi(this)" data-full="${teksAman}" data-short="${teksSingkatAman}" data-expanded="false" class="text-blue-500 hover:text-blue-700 font-bold ml-1 transition">selengkapnya</button>`
                            : '';

                        let urlMaps = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
                        let urlDetail = `/admin-bidang/laporan/detail/${laporan.id}`;

                        let marker = L.marker([lat, lng], {icon: ikonCustom});

                        // Desain Popup
                        marker.bindPopup(`
                            <div class="w-60 text-center flex flex-col items-center">
                                <p class="text-[10px] font-bold text-gray-400 mb-1 tracking-wider">ID: ${laporan.id_laporan}</p>
                                <h3 class="font-extrabold text-gray-900 text-base leading-tight">${kategori}</h3>

                                <div class="bg-gray-100 text-gray-600 px-3 py-1 text-[9px] rounded-full font-bold my-2 uppercase tracking-widest border border-gray-200">
                                    STATUS: ${laporan.status}
                                </div>

                                <div class="w-full text-left mt-2 mb-4 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                    <p class="text-[10px] font-bold text-gray-800 mb-1"><i class="fas fa-user-circle text-gray-400 mr-1 text-sm"></i> ${namaPelapor}</p>
                                    <div class="text-[11px] text-gray-600 leading-relaxed break-words relative w-full">
                                        <span class="deskripsi-konten">${deskripsiSingkat} ${btnSelengkapnya}</span>
                                    </div>
                                </div>

                                <div class="w-full space-y-2">
                                    <a href="${urlDetail}" class="w-full bg-[#1E3A8A] hover:bg-blue-800 text-white text-xs font-bold py-2.5 rounded-xl flex items-center justify-center transition shadow-sm" style="text-decoration: none; color: white !important;">
                                        Detail Laporan
                                    </a>
                                    <a href="${urlMaps}" target="_blank" class="w-full bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-bold py-2 rounded-xl flex items-center justify-center transition shadow-sm" style="text-decoration: none;">
                                        <i class="fas fa-map-marked-alt mr-2 text-green-600"></i> Buka Google Maps
                                    </a>
                                </div>
                            </div>
                        `);
                        markerGroup.addLayer(marker);
                    }
                }
            });
        }
    }

    // Fungsi Interaksi UI (Menu & Fullscreen)
    function gantiLayerPeta(jenis) {
        if(jenis === 'standar') { map.removeLayer(layerSatelit); layerStandar.addTo(map); }
        else { map.removeLayer(layerStandar); layerSatelit.addTo(map); }
        document.getElementById('menu-layer').classList.add('hidden');
    }

    function toggleMenuPeta(menuId) {
        let menu = document.getElementById(menuId);
        if(menu.classList.contains('hidden')) {
            document.getElementById('menu-layer').classList.add('hidden');
            menu.classList.remove('hidden');
        } else {
            menu.classList.add('hidden');
        }
    }

    function toggleFullscreen() {
        let container = document.getElementById('peta-container');
        if (!document.fullscreenElement) { container.requestFullscreen().catch(err => { console.log(err); }); }
        else { document.exitFullscreen(); }
    }

    document.addEventListener('fullscreenchange', (event) => {
        let wrapper = document.getElementById('map-wrapper');
        let iconBtn = document.getElementById('icon-fs');

        if (document.fullscreenElement) {
            wrapper.classList.remove('h-[450px]');
            wrapper.style.height = 'calc(100vh - 70px)';
            iconBtn.classList.replace('fa-expand', 'fa-compress');
        } else {
            wrapper.style.height = '';
            wrapper.classList.add('h-[450px]');
            iconBtn.classList.replace('fa-compress', 'fa-expand');
        }
        setTimeout(() => { map.invalidateSize(); }, 300);
    });

    document.addEventListener('click', function(event) {
        if(!event.target.closest('#btn-layer') && !event.target.closest('#menu-layer')) {
            let menuLayer = document.getElementById('menu-layer');
            if (menuLayer) menuLayer.classList.add('hidden');
        }
    });
</script>
@endpush
