@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    /* Kustomisasi Popup Leaflet agar Modern */
    .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.15); border: 1px solid #e2e8f0; padding: 0; overflow: hidden;}
    .leaflet-popup-content { margin: 0; width: 280px !important; }
    .leaflet-container a.leaflet-popup-close-button { color: #94a3b8; top: 8px; right: 8px; font-size: 16px;}
    .leaflet-container a.leaflet-popup-close-button:hover { color: #ef4444; }

    /* --- TAMBAHAN ANIMASI HALUS --- */
    @keyframes fadeInUpElement {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animasi-masuk {
        animation: fadeInUpElement 0.6s ease-out forwards;
        opacity: 0; /* Elemen sembunyi dulu sebelum animasi mulai */
    }
    /* Jeda berurutan untuk efek kaskade */
    .jeda-1 { animation-delay: 0.1s; }
    .jeda-2 { animation-delay: 0.2s; }
    .jeda-3 { animation-delay: 0.3s; }
    .jeda-4 { animation-delay: 0.4s; }
    .jeda-5 { animation-delay: 0.5s; }
</style>
@endpush

@section('konten')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div></div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow animasi-masuk jeda-1">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                <i class="fas fa-chart-bar"></i>
            </div>
            @if(($statistik['persen_total'] ?? 0) >= 0)
                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full flex items-center">
                    <i class="fas fa-arrow-trend-up mr-1"></i> {{ $statistik['persen_total'] ?? 0 }}%
                </span>
            @else
                <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded-full flex items-center">
                    <i class="fas fa-arrow-trend-down mr-1"></i> {{ abs($statistik['persen_total'] ?? 0) }}%
                </span>
            @endif
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Total Laporan</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ number_format($statistik['total_laporan'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="mt-4 text-xs text-gray-400 font-medium flex justify-between">
            <span>Dibandingkan bulan lalu</span>
            <span class="font-bold {{ ($statistik['selisih_total'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-500' }}">
                {{ ($statistik['selisih_total'] ?? 0) > 0 ? '+' : '' }}{{ $statistik['selisih_total'] ?? 0 }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow animasi-masuk jeda-2">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                <i class="fas fa-check-circle"></i>
            </div>
            <span class="{{ ($statistik['rasio_selesai'] ?? 0) >= 75 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} text-xs font-bold px-2 py-1 rounded-full flex items-center">
                <i class="fas fa-star mr-1"></i> Performa
            </span>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Selesai</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ number_format($statistik['selesai'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="mt-4 text-xs text-gray-400 font-medium flex justify-between">
            <span>Rasio Penyelesaian</span>
            <span class="text-gray-800 font-bold">{{ $statistik['rasio_selesai'] ?? 0 }}%</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow animasi-masuk jeda-3">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center text-yellow-600">
                <i class="fas fa-sync-alt"></i>
            </div>
            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-full flex items-center">
                <i class="fas fa-tools mr-1"></i> Lapangan
            </span>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Proses</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ number_format($statistik['dalam_proses'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="mt-4 text-xs text-gray-400 font-medium flex justify-between">
            <span>Laporan sedang dikerjakan</span>
            <span class="text-gray-800 font-bold">{{ $statistik['dalam_proses'] ?? 0 }} Aktif</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow animasi-masuk jeda-4">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center text-red-500">
                <i class="fas fa-clipboard-list"></i>
            </div>
            @if(($statistik['laporan_terbaru'] ?? 0) > 0)
                <span class="bg-red-50 text-red-600 border border-red-200 text-xs font-bold px-2 py-1 rounded-full flex items-center animate-pulse">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Butuh Validasi
                </span>
            @else
                <span class="bg-gray-100 text-gray-500 text-xs font-bold px-2 py-1 rounded-full flex items-center">
                    <i class="fas fa-check mr-1"></i> Clear
                </span>
            @endif
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Menunggu Validasi</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ number_format($statistik['laporan_terbaru'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="mt-4 text-xs text-gray-400 font-medium flex justify-between">
            <span>Laporan baru / pending</span>
            <span class="{{ ($statistik['laporan_terbaru'] ?? 0) > 0 ? 'text-red-500' : 'text-gray-800' }} font-bold">
                {{ $statistik['laporan_terbaru'] ?? 0 }} Urgent
            </span>
        </div>
    </div>
</div>

<div id="peta-container" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8 relative transition-all animasi-masuk jeda-5">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Peta Sebaran Laporan</h3>
            <p class="text-sm text-gray-400 font-medium">Visualisasi geografis infrastruktur per wilayah</p>
        </div>
        <div class="flex space-x-3 relative">
            <div class="relative">
                <button id="btn-layer" onclick="toggleMenuPeta('menu-layer')" class="w-10 h-10 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 flex items-center justify-center transition focus:outline-none" title="Ganti Tampilan Peta">
                    <i class="fas fa-layer-group"></i>
                </button>
                <div id="menu-layer" class="hidden absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-xl border border-gray-100 z-[2000] p-2">
                    <button onclick="gantiLayerPeta('standar')" class="w-full text-left px-3 py-2 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">Peta Jalan (Bawaan)</button>
                    <button onclick="gantiLayerPeta('satelit')" class="w-full text-left px-3 py-2 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">Citra Satelit</button>
                </div>
            </div>

            <div class="relative">
                <button id="btn-filter" onclick="toggleMenuPeta('menu-filter')" class="w-10 h-10 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 flex items-center justify-center transition focus:outline-none" title="Filter Laporan">
                    <i class="fas fa-filter"></i>
                </button>
                <div id="menu-filter" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 z-[2000] p-4">
                    <p class="text-xs font-bold text-gray-400 mb-3 uppercase tracking-wider border-b pb-2">Filter Data Laporan</p>
                    <label class="flex items-center space-x-3 mb-2 cursor-pointer hover:bg-gray-50 p-1 rounded transition">
                        <input type="checkbox" value="pending" class="filter-cb form-checkbox h-4 w-4 text-red-500" checked onchange="renderMarkers()">
                        <span class="text-sm font-medium text-gray-700">Mendesak / Pending</span>
                    </label>
                    <label class="flex items-center space-x-3 mb-2 cursor-pointer hover:bg-gray-50 p-1 rounded transition">
                        <input type="checkbox" value="proses" class="filter-cb form-checkbox h-4 w-4 text-yellow-500" checked onchange="renderMarkers()">
                        <span class="text-sm font-medium text-gray-700">Dalam Pengerjaan</span>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-1 rounded transition">
                        <input type="checkbox" value="selesai" class="filter-cb form-checkbox h-4 w-4 text-green-500" checked onchange="renderMarkers()">
                        <span class="text-sm font-medium text-gray-700">Stabil / Selesai</span>
                    </label>
                </div>
            </div>

            <button id="btn-fullscreen" onclick="toggleFullscreen()" class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-bold rounded-lg transition flex items-center focus:outline-none shadow-md hover:shadow-lg">
                <i class="fas fa-expand mr-2" id="icon-fs"></i> <span id="text-fs">Layar Penuh</span>
            </button>
        </div>
    </div>

    <div id="map-wrapper" class="relative w-full rounded-xl overflow-hidden border border-gray-200 z-0 transition-all duration-300" style="height:400px; min-height:400px;">
        <div id="map" class="w-full h-full bg-gray-100"></div>

        <div class="absolute top-4 left-4 bg-white/95 backdrop-blur rounded-xl p-4 shadow-lg border border-white/50 z-[1000]">
            <h4 class="text-[10px] font-extrabold text-gray-500 mb-3 tracking-widest uppercase">Legenda Peta</h4>
            <div class="space-y-3">
                <div class="flex items-center text-xs font-bold text-gray-600">
                    <i class="fas fa-map-marker-alt text-red-500 text-lg w-5 text-center drop-shadow-sm mr-2"></i> Mendesak / Pending
                </div>
                <div class="flex items-center text-xs font-bold text-gray-600">
                    <i class="fas fa-map-marker-alt text-yellow-500 text-lg w-5 text-center drop-shadow-sm mr-2"></i> Dalam Pengerjaan
                </div>
                <div class="flex items-center text-xs font-bold text-gray-600">
                    <i class="fas fa-map-marker-alt text-green-500 text-lg w-5 text-center drop-shadow-sm mr-2"></i> Stabil / Selesai
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    let map, layerStandar, layerSatelit, markerGroup;
    let dataLaporan = @json($sebaran_laporan ?? []);

    function initMap() {
        if (!document.getElementById('map')) return;
        if (map) return;

        console.log('[SIGAP-beranda] init map start');
        map = L.map('map').setView([-6.5627, 107.7613], 12);

        layerStandar = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' });
        layerSatelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });

        layerStandar.addTo(map);
        markerGroup = L.layerGroup().addTo(map);
        renderMarkers();

        setTimeout(() => { map.invalidateSize(); }, 250);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        initMap();
    }

    // Utility: escape HTML to avoid XSS in popups
    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        const entities = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
        return String(text).replace(/[&<>"']/g, function(m) { return entities[m]; });
    }

    // Utility: toggle description inside a popup (short <-> full)
    function toggleDeskripsi(btn) {
        const parent = btn.closest('.leaflet-popup-content') || btn.parentElement;
        if (!parent) return;
        const shortEl = parent.querySelector('.deskripsi-short');
        const fullEl = parent.querySelector('.deskripsi-full');
        if (!shortEl || !fullEl) return;

        if (fullEl.classList.contains('hidden')) {
            shortEl.classList.add('hidden');
            fullEl.classList.remove('hidden');
            btn.innerText = 'Sembunyikan';
            btn.className = 'text-gray-400 hover:text-gray-600 font-bold ml-1 transition hover:bg-gray-200 p-0.5 rounded mt-1 block';
        } else {
            fullEl.classList.add('hidden');
            shortEl.classList.remove('hidden');
            btn.innerText = 'selengkapnya';
            btn.className = 'text-blue-500 hover:text-blue-700 font-bold ml-1 transition';
        }
    }
    // Jadikan fungsi global agar bisa dipanggil dari popup Leaflet
    window.toggleDeskripsi = toggleDeskripsi;

    function renderMarkers() {
        if(!markerGroup) return;
        markerGroup.clearLayers();
        let filterAktif = Array.from(document.querySelectorAll('.filter-cb:checked')).map(cb => cb.value);

        console.log('[SIGAP-beranda] markers data length:', Array.isArray(dataLaporan) ? dataLaporan.length : 0);
        let added = 0;

        if(Array.isArray(dataLaporan) && dataLaporan.length > 0) {
            dataLaporan.forEach(function(laporan) {
                // Konversi filter proses agar menangkap status diteruskan juga
                let statusFilter = laporan.status;
                if(laporan.status === 'diteruskan') {
                    statusFilter = 'proses';
                }

                if(filterAktif.includes(statusFilter) && laporan.lokasi_gps) {
                    let koordinat = laporan.lokasi_gps.split(',');
                    if(koordinat.length == 2) {
                        let lat = parseFloat(koordinat[0].trim());
                        let lng = parseFloat(koordinat[1].trim());

                        // 1. Logika Warna Ikon & Badge Status
                        let warnaIkon = 'text-red-500';
                        let warnaBadge = 'bg-red-50 text-red-700';
                        let teksStatus = 'VALIDASI';

                        if(laporan.status === 'pending' || laporan.status === 'ditolak') {
                            warnaIkon = 'text-red-500';
                            warnaBadge = 'bg-red-50 text-red-700';
                            teksStatus = laporan.status === 'ditolak' ? 'DITOLAK' : 'VALIDASI';
                        }
                        else if(laporan.status === 'proses' || laporan.status === 'diteruskan') {
                            warnaIkon = 'text-yellow-500';
                            warnaBadge = 'bg-yellow-50 text-yellow-700';
                            teksStatus = 'DALAM PENGERJAAN';
                        }
                        else if(laporan.status === 'selesai') {
                            warnaIkon = 'text-green-500';
                            warnaBadge = 'bg-green-50 text-green-700';
                            teksStatus = 'SELESAI';
                        }

                        // 2. Pembuatan Ikon Pin Map
                                                let ikonCustom = L.divIcon({
                                                        className: 'bg-transparent',
                                                        html: `<div style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5); font-size:28px; line-height:1; display:inline-block;" class="${warnaIkon} hover:scale-110 transition-transform cursor-pointer drop-shadow">
                                                                         <i class="fas fa-map-marker-alt"></i>
                                                                     </div>`,
                                                        iconSize: [30, 42],
                                                        iconAnchor: [15, 40],
                                                        popupAnchor: [0, -35]
                                                });

                        // 3. Siapkan URL Google Maps dan tautan detail yang BENAR
                        let urlMaps = `https://www.google.com/maps?q=${lat},${lng}`;
                        // MENGGUNAKAN ID YANG ASLI DARI DATABASE UNTUK DETAIL LAPORAN
                        let urlDetail = `/admin-universal/laporan/detail/${laporan.id}`;

                        // 4. Nama Pelapor dan Foto Profil
                        let namaPelapor = (laporan.pelapor && laporan.pelapor.nama_lengkap) ? escapeHtml(laporan.pelapor.nama_lengkap) : 'Masyarakat Subang';
                        let fotoSrc = (laporan.pelapor && laporan.pelapor.foto_profil) ? ('/storage/' + laporan.pelapor.foto_profil) : null;

                        let imgHtml = fotoSrc
                            ? `<img src="${fotoSrc}" alt="Profil" class="w-10 h-10 rounded-full object-cover border border-gray-200 shrink-0">`
                            : `<div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-xs font-bold border border-gray-200 uppercase shrink-0">${namaPelapor.substring(0, 1)}</div>`;

                        // 5. Kategori Bidang
                        let kategori = (laporan.status === 'pending' || !laporan.kategori_bidang) ? 'Menunggu Disposisi' : escapeHtml(laporan.kategori_bidang);

                        // 6. Deskripsi singkat + tombol selengkapnya
                        let deskripsiAsli = laporan.deskripsi_laporan || 'Laporan infrastruktur dari masyarakat.';
                        let teksAman = escapeHtml(deskripsiAsli);
                        let batasKarakter = 70;
                        let teksSingkatAman = teksAman.length > batasKarakter ? escapeHtml(deskripsiAsli.substring(0, batasKarakter)) + '...' : teksAman;
                        let btnSelengkapnya = teksAman.length > batasKarakter ? `<button type="button" onclick="event.stopPropagation(); window.toggleDeskripsi(this)" class="text-blue-500 hover:text-blue-700 font-bold ml-1 transition">selengkapnya</button>` : '';

                        // 7. Bind Popup
                        let marker = L.marker([lat, lng], {icon: ikonCustom});
                        marker.bindPopup(`
                            <div class="flex flex-col items-center">
                                <div class="px-4 pt-4 pb-2 text-center w-full">
                                    <p class="text-[10px] font-bold text-gray-400 mb-0.5 tracking-wider">ID: #${escapeHtml(laporan.id_laporan)}</p>
                                    <h3 class="font-extrabold text-gray-950 text-base leading-tight uppercase mb-1.5">${kategori}</h3>

                                    <div class="${warnaBadge} px-3 py-1 text-[9px] rounded-full font-extrabold my-2 uppercase tracking-widest border border-gray-100 inline-block shadow-inner">
                                        STATUS: ${teksStatus}
                                    </div>
                                </div>

                                <div class="w-full text-left bg-gray-50/50 p-3.5 border-y border-gray-100 hover:bg-gray-50 transition duration-300">
                                    <div class="flex items-start space-x-3 mb-1">
                                        ${imgHtml}
                                        <div class="flex-1 text-[11px] font-medium leading-relaxed break-words relative w-full text-left">
                                            <div class="text-gray-800 font-bold mb-0.5">${namaPelapor}</div>
                                            <span class="deskripsi-short text-gray-600">${teksSingkatAman}</span>
                                            <span class="deskripsi-full hidden text-gray-600">${teksAman}</span>
                                            ${btnSelengkapnya}
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full p-3.5 space-y-2 bg-white">
                                    <a href="${urlDetail}" class="w-full bg-[#1E3A8A] hover:bg-blue-800 text-white text-xs font-bold py-3 rounded-xl flex items-center justify-center transition shadow focus:outline-none hover:shadow-lg transform hover:-translate-y-0.5" style="text-decoration: none; color: white !important;">
                                        <i class="fas fa-eye mr-2"></i> Buka Detail Laporan
                                    </a>
                                    <a href="${urlMaps}" target="_blank" class="w-full bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 text-xs font-bold py-2.5 rounded-xl flex items-center justify-center transition shadow-sm focus:outline-none" style="text-decoration: none;">
                                        <i class="fas fa-map-marked-alt mr-2 text-green-600"></i> Buka Google Maps
                                    </a>
                                </div>
                            </div>
                        `, { maxWidth: 280, minWidth: 280 });

                        markerGroup.addLayer(marker);
                        added++;
                    }
                }
            });
            console.log('[SIGAP-beranda] markers added:', added);
        }
    }

    // Fungsi Ganti Layer, Toggle Menu, dan Layar Penuh
    function gantiLayerPeta(jenis) {
        if(jenis === 'standar') {
            map.removeLayer(layerSatelit);
            layerStandar.addTo(map);
        } else {
            map.removeLayer(layerStandar);
            layerSatelit.addTo(map);
        }
        document.getElementById('menu-layer').classList.add('hidden');
    }

    function toggleMenuPeta(menuId) {
        let menu = document.getElementById(menuId);
        if(menu.classList.contains('hidden')) {
            document.getElementById('menu-layer').classList.add('hidden');
            document.getElementById('menu-filter').classList.add('hidden');
            menu.classList.remove('hidden');
        } else {
            menu.classList.add('hidden');
        }
    }

    function toggleFullscreen() {
        let container = document.getElementById('peta-container');
        if (!document.fullscreenElement) {
            container.requestFullscreen().catch(err => { alert(`Gagal: ${err.message}`); });
        } else {
            document.exitFullscreen();
        }
    }

    document.addEventListener('fullscreenchange', (event) => {
        let wrapper = document.getElementById('map-wrapper');
        let teksBtn = document.getElementById('text-fs');
        let iconBtn = document.getElementById('icon-fs');

        if (document.fullscreenElement) {
            wrapper.classList.remove('h-[400px]');
            wrapper.style.height = 'calc(100vh - 120px)';
            teksBtn.innerText = "Keluar Penuh";
            iconBtn.classList.remove('fa-expand');
            iconBtn.classList.add('fa-compress');
        } else {
            wrapper.style.height = '';
            wrapper.classList.add('h-[400px]');
            teksBtn.innerText = "Layar Penuh";
            iconBtn.classList.remove('fa-compress');
            iconBtn.classList.add('fa-expand');
        }
        setTimeout(() => { map.invalidateSize(); }, 300);
    });

    document.addEventListener('click', function(event) {
        if(!event.target.closest('#btn-layer') && !event.target.closest('#menu-layer')) {
            let menuL = document.getElementById('menu-layer');
            if(menuL) menuL.classList.add('hidden');
        }
        if(!event.target.closest('#btn-filter') && !event.target.closest('#menu-filter')) {
            let menuF = document.getElementById('menu-filter');
            if(menuF) menuF.classList.add('hidden');
        }
    });
</script>
@endpush
