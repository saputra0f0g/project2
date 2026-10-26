@extends('layouts.app_admin_jalan')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Admin Jalan')
@section('page_subtitle', 'Monitoring kendali dan sebaran laporan infrastruktur jalan')

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    /* Kustomisasi Popup Leaflet agar Elegan & Modern */
    .leaflet-popup-content-wrapper {
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        padding: 0;
        overflow: hidden;
    }
    .leaflet-popup-content {
        margin: 0;
        width: 290px !important;
    }
    .leaflet-container a.leaflet-popup-close-button {
        color: #94a3b8;
        top: 10px;
        right: 10px;
        font-size: 16px;
    }
    .leaflet-container a.leaflet-popup-close-button:hover {
        color: #ef4444;
    }

    /* Animasi Halus Kaskade Masuk */
    @keyframes fadeInUpElement {
        0% { opacity: 0; transform: translateY(18px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animasi-masuk {
        animation: fadeInUpElement 0.55s ease-out forwards;
        opacity: 0;
    }
    .jeda-1 { animation-delay: 0.05s; }
    .jeda-2 { animation-delay: 0.12s; }
    .jeda-3 { animation-delay: 0.20s; }
    .jeda-4 { animation-delay: 0.28s; }
    .jeda-5 { animation-delay: 0.36s; }
    .jeda-6 { animation-delay: 0.44s; }
</style>
@endpush

@section('content')

{{-- ─── 4 KARTU STATISTIK (GAYA ADMIN UNIVERSAL) ────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

    {{-- 1. Total Laporan --}}
    <a href="{{ route('admin_jalan.laporan.indeks') }}" class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow animasi-masuk jeda-1 block">
        <div class="flex justify-between items-start mb-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shadow-sm">
                <i class="fas fa-road text-lg"></i>
            </div>
            @if(($statistik['persen_total'] ?? 0) >= 0)
                <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full flex items-center">
                    <i class="fas fa-arrow-trend-up mr-1"></i> {{ $statistik['persen_total'] ?? 0 }}%
                </span>
            @else
                <span class="bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded-full flex items-center">
                    <i class="fas fa-arrow-trend-down mr-1"></i> {{ abs($statistik['persen_total'] ?? 0) }}%
                </span>
            @endif
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Total Laporan Jalan</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ number_format($statistik['total_laporan'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="mt-4 text-xs text-gray-400 font-medium flex justify-between">
            <span>Dibandingkan bulan lalu</span>
            <span class="font-bold {{ ($statistik['selisih_total'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-500' }}">
                {{ ($statistik['selisih_total'] ?? 0) > 0 ? '+' : '' }}{{ $statistik['selisih_total'] ?? 0 }}
            </span>
        </div>
    </a>

    {{-- 2. Selesai Ditangani --}}
    <a href="{{ route('admin_jalan.laporan.indeks', ['status' => 'selesai']) }}" class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow animasi-masuk jeda-2 block">
        <div class="flex justify-between items-start mb-4">
            <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center text-green-600 shadow-sm">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
            <span class="{{ ($statistik['rasio_selesai'] ?? 0) >= 70 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} text-xs font-bold px-2.5 py-1 rounded-full flex items-center">
                <i class="fas fa-star mr-1"></i> Performa
            </span>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Selesai Ditangani</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ number_format($statistik['selesai'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="mt-4 text-xs text-gray-400 font-medium flex justify-between">
            <span>Rasio Penyelesaian</span>
            <span class="text-gray-800 font-bold">{{ $statistik['rasio_selesai'] ?? 0 }}%</span>
        </div>
    </a>

    {{-- 3. Dalam Penanganan (Diverifikasi & Dalam Perbaikan) --}}
    <a href="{{ route('admin_jalan.laporan.indeks', ['status' => 'dalam_perbaikan']) }}" class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow animasi-masuk jeda-3 block">
        <div class="flex justify-between items-start mb-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 shadow-sm">
                <i class="fas fa-hard-hat text-lg"></i>
            </div>
            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full flex items-center">
                <i class="fas fa-tools mr-1"></i> Lapangan
            </span>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Dalam Penanganan</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ number_format($statistik['dalam_proses'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="mt-4 text-xs text-gray-400 font-medium flex justify-between">
            <span>Diverifikasi & Perbaikan</span>
            <span class="text-gray-800 font-bold">{{ $statistik['dalam_proses'] ?? 0 }} Aktif</span>
        </div>
    </a>

    {{-- 4. Menunggu Verifikasi --}}
    <a href="{{ route('admin_jalan.laporan.indeks', ['status' => 'menunggu']) }}" class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow animasi-masuk jeda-4 block">
        <div class="flex justify-between items-start mb-4">
            <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center text-red-500 shadow-sm">
                <i class="fas fa-clipboard-list text-lg"></i>
            </div>
            @if(($statistik['menunggu'] ?? 0) > 0)
                <span class="bg-red-50 text-red-600 border border-red-200 text-xs font-bold px-2.5 py-1 rounded-full flex items-center animate-pulse">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Butuh Validasi
                </span>
            @else
                <span class="bg-gray-100 text-gray-500 text-xs font-bold px-2.5 py-1 rounded-full flex items-center">
                    <i class="fas fa-check mr-1"></i> Clear
                </span>
            @endif
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Menunggu Verifikasi</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ number_format($statistik['menunggu'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="mt-4 text-xs text-gray-400 font-medium flex justify-between">
            <span>Laporan baru / pending</span>
            <span class="{{ ($statistik['menunggu'] ?? 0) > 0 ? 'text-red-500' : 'text-gray-800' }} font-bold">
                {{ $statistik['menunggu'] ?? 0 }} Urgent
            </span>
        </div>
    </a>
</div>

{{-- ─── PETA SEBARAN LAPORAN (COMMAND CENTER LEAFLET) ───────────────────── --}}
<div id="peta-container" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8 relative transition-all animasi-masuk jeda-5">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <h3 class="text-lg font-bold text-gray-900">Peta Sebaran Laporan Jalan</h3>
                <span class="bg-blue-50 text-blue-700 text-xs font-bold px-2.5 py-0.5 rounded-full border border-blue-100">
                    GIS Subang
                </span>
            </div>
            <p class="text-sm text-gray-400 font-medium mt-0.5">Visualisasi titik koordinat kerusakan infrastruktur jalan dan status penanganan terkini</p>
        </div>

        <div class="flex items-center space-x-3 relative self-end sm:self-auto">
            {{-- Layer Switcher --}}
            <div class="relative">
                <button id="btn-layer" onclick="toggleMenuPeta('menu-layer')" class="w-10 h-10 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 flex items-center justify-center transition shadow-sm focus:outline-none" title="Ganti Tampilan Peta">
                    <i class="fas fa-layer-group"></i>
                </button>
                <div id="menu-layer" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 z-[2000] p-2">
                    <p class="text-[10px] font-bold text-gray-400 px-3 py-1 uppercase tracking-wider">Tipe Peta</p>
                    <button onclick="gantiLayerPeta('standar')" class="w-full text-left px-3 py-2 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition flex items-center">
                        <i class="fas fa-map mr-2 text-blue-500"></i> Peta Jalan (Bawaan)
                    </button>
                    <button onclick="gantiLayerPeta('satelit')" class="w-full text-left px-3 py-2 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition flex items-center">
                        <i class="fas fa-satellite mr-2 text-emerald-500"></i> Citra Satelit
                    </button>
                </div>
            </div>

            {{-- Filter Laporan --}}
            <div class="relative">
                <button id="btn-filter" onclick="toggleMenuPeta('menu-filter')" class="w-10 h-10 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 flex items-center justify-center transition shadow-sm focus:outline-none" title="Filter Status Laporan">
                    <i class="fas fa-filter"></i>
                </button>
                <div id="menu-filter" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 z-[2000] p-4">
                    <p class="text-xs font-bold text-gray-400 mb-3 uppercase tracking-wider border-b pb-2">Filter Status Laporan</p>
                    <label class="flex items-center space-x-3 mb-2.5 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg transition">
                        <input type="checkbox" value="menunggu" class="filter-cb form-checkbox h-4 w-4 text-amber-500 rounded" checked onchange="renderMarkers()">
                        <span class="text-sm font-medium text-gray-700 flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-2"></span> Menunggu
                        </span>
                    </label>
                    <label class="flex items-center space-x-3 mb-2.5 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg transition">
                        <input type="checkbox" value="diverifikasi" class="filter-cb form-checkbox h-4 w-4 text-blue-500 rounded" checked onchange="renderMarkers()">
                        <span class="text-sm font-medium text-gray-700 flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 mr-2"></span> Diverifikasi
                        </span>
                    </label>
                    <label class="flex items-center space-x-3 mb-2.5 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg transition">
                        <input type="checkbox" value="dalam_perbaikan" class="filter-cb form-checkbox h-4 w-4 text-orange-500 rounded" checked onchange="renderMarkers()">
                        <span class="text-sm font-medium text-gray-700 flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-orange-500 mr-2"></span> Dalam Perbaikan
                        </span>
                    </label>
                    <label class="flex items-center space-x-3 mb-2.5 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg transition">
                        <input type="checkbox" value="selesai" class="filter-cb form-checkbox h-4 w-4 text-emerald-500 rounded" checked onchange="renderMarkers()">
                        <span class="text-sm font-medium text-gray-700 flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2"></span> Selesai
                        </span>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg transition">
                        <input type="checkbox" value="ditolak" class="filter-cb form-checkbox h-4 w-4 text-red-500 rounded" checked onchange="renderMarkers()">
                        <span class="text-sm font-medium text-gray-700 flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500 mr-2"></span> Ditolak
                        </span>
                    </label>
                </div>
            </div>

            {{-- Fullscreen Toggle --}}
            <button id="btn-fullscreen" onclick="toggleFullscreen()" class="px-4 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-slate-900 text-sm font-bold rounded-xl transition flex items-center focus:outline-none shadow-sm hover:shadow-md">
                <i class="fas fa-expand mr-2" id="icon-fs"></i> <span id="text-fs">Layar Penuh</span>
            </button>
        </div>
    </div>

    {{-- Map Canvas & Legenda --}}
    <div id="map-wrapper" class="relative w-full rounded-2xl overflow-hidden border border-gray-200 z-0 transition-all duration-300" style="height:440px; min-height:440px;">
        <div id="map" class="w-full h-full bg-slate-100"></div>

        {{-- Legenda Floating di Peta --}}
        <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-lg border border-white/60 z-[1000] hidden sm:block">
            <h4 class="text-[10px] font-extrabold text-gray-500 mb-2.5 tracking-wider uppercase flex items-center">
                <i class="fas fa-map-pin mr-1.5 text-blue-600"></i> Legenda Status Jalan
            </h4>
            <div class="space-y-2 text-xs font-semibold text-gray-600">
                <div class="flex items-center">
                    <i class="fas fa-map-marker-alt text-amber-500 text-base w-5 text-center mr-2"></i> Menunggu
                </div>
                <div class="flex items-center">
                    <i class="fas fa-map-marker-alt text-blue-500 text-base w-5 text-center mr-2"></i> Diverifikasi
                </div>
                <div class="flex items-center">
                    <i class="fas fa-map-marker-alt text-orange-500 text-base w-5 text-center mr-2"></i> Dalam Perbaikan
                </div>
                <div class="flex items-center">
                    <i class="fas fa-map-marker-alt text-emerald-500 text-base w-5 text-center mr-2"></i> Selesai
                </div>
                <div class="flex items-center">
                    <i class="fas fa-map-marker-alt text-red-500 text-base w-5 text-center mr-2"></i> Ditolak
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── LAPORAN TERBARU & DISTRIBUSI JENIS KERUSAKAN ───────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animasi-masuk jeda-6">

    {{-- Kolom Kiri: 5 Laporan Terbaru Masuk --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-gray-900 font-bold text-base">Laporan Masuk Terbaru</h2>
                <p class="text-gray-400 text-xs mt-0.5">5 laporan kerusakan jalan yang baru dilaporkan masyarakat</p>
            </div>
            <a href="{{ route('admin_jalan.laporan.indeks') }}"
               class="inline-flex items-center text-blue-600 text-sm font-semibold hover:text-blue-800 transition-colors">
                Kelola Semua <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
            </a>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($laporanTerbaru as $laporan)
            <div class="flex items-start gap-4 px-6 py-4 hover:bg-slate-50/70 transition-colors">
                {{-- Icon Jenis Kerusakan --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                    {{ $laporan->jenis_kerusakan === 'jalan_berlubang' ? 'bg-red-100 text-red-600' :
                       ($laporan->jenis_kerusakan === 'lampu_jalan_mati' ? 'bg-yellow-100 text-yellow-600' :
                       ($laporan->jenis_kerusakan === 'drainase_tersumbat' ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-600')) }}">
                    <i class="fas
                        {{ $laporan->jenis_kerusakan === 'jalan_berlubang' ? 'fa-circle-exclamation' :
                           ($laporan->jenis_kerusakan === 'lampu_jalan_mati' ? 'fa-lightbulb' :
                           ($laporan->jenis_kerusakan === 'drainase_tersumbat' ? 'fa-water' : 'fa-road')) }}
                        text-sm"></i>
                </div>

                {{-- Detail Laporan --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-bold text-gray-400">#{{ $laporan->id_laporan }}</span>
                        <p class="text-gray-900 font-bold text-sm truncate">
                            {{ $laporan->judul_laporan ?? 'Laporan #' . $laporan->id_laporan }}
                        </p>
                    </div>
                    <p class="text-gray-500 text-xs mt-0.5 truncate">
                        <i class="fas fa-map-marker-alt mr-1 text-red-400"></i>
                        {{ $laporan->alamat_map ?? 'Lokasi GPS tertera' }}
                    </p>
                    <p class="text-gray-400 text-[11px] mt-0.5">
                        <i class="fas fa-user mr-1 text-gray-400"></i>
                        {{ $laporan->pelapor?->nama_lengkap ?? 'Masyarakat' }}
                        &nbsp;·&nbsp;
                        <i class="far fa-clock mr-1"></i> {{ $laporan->created_at?->diffForHumans() }}
                    </p>
                </div>

                {{-- Status & Aksi --}}
                <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full {{ $laporan->kelas_badge_status }} uppercase tracking-wider">
                        {{ $laporan->label_status }}
                    </span>
                    <a href="{{ route('admin_jalan.laporan.detail', $laporan->id) }}"
                       class="text-blue-600 text-xs font-bold hover:text-blue-800 transition">
                        Detail <i class="fas fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-12 text-gray-300">
                <i class="fas fa-clipboard-check text-4xl mb-3 text-gray-300"></i>
                <p class="font-medium text-gray-400 text-sm">Belum ada laporan masuk</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Kolom Kanan: Distribusi Jenis Kerusakan Jalan --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-gray-900 font-bold text-base">Jenis Kerusakan Jalan</h2>
            <p class="text-gray-400 text-xs mt-0.5">Distribusi masalah infrastruktur jalan</p>
        </div>

        @php
            $jenisLabels = \App\Models\LaporanKeluhan::JENIS_KERUSAKAN;
            $totalSemuaJenis = $perJenis->sum();
        @endphp

        <div class="px-6 py-4 space-y-4">
            @foreach($perJenis as $jenis => $count)
            @php
                $pct = $totalSemuaJenis > 0 ? round($count / $totalSemuaJenis * 100) : 0;
                $label = $jenisLabels[$jenis] ?? ucwords(str_replace('_', ' ', $jenis));
            @endphp
            <div>
                <div class="flex justify-between text-xs mb-1.5 font-medium">
                    <span class="text-gray-700 truncate max-w-[170px]" title="{{ $label }}">
                        {{ $label }}
                    </span>
                    <span class="text-gray-500 font-bold">{{ $count }} <span class="text-gray-400 font-normal">({{ $pct }}%)</span></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500"
                         style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endforeach

            @if($perJenis->isEmpty())
            <div class="text-center py-8 text-gray-300">
                <i class="fas fa-chart-pie text-3xl mb-2 text-gray-300"></i>
                <p class="text-xs text-gray-400">Belum ada data kerusakan</p>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    let map, layerStandar, layerSatelit, markerGroup;
    let dataLaporan = @json($sebaran_laporan ?? []);

    const JENIS_KERUSAKAN_MAP = {
        'jalan_berlubang': 'Jalan Berlubang',
        'jalan_retak': 'Jalan Retak / Permukaan Rata',
        'jalan_amblas': 'Jalan Amblas / Longsor',
        'jalan_banjir': 'Genangan / Banjir di Jalan',
        'lampu_jalan_mati': 'Lampu Jalan Mati',
        'trotoar_rusak': 'Trotoar / Bahu Jalan Rusak',
        'rambu_rusak': 'Rambu Rusak / Hilang',
        'drainase_tersumbat': 'Drainase Tersumbat',
        'lainnya': 'Kerusakan Jalan Lainnya'
    };

    function initMap() {
        if (!document.getElementById('map')) return;
        if (map) return;

        // Koordinat default: Kabupaten Subang
        map = L.map('map').setView([-6.5627, 107.7613], 12);

        layerStandar = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        });

        layerSatelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: '&copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        });

        layerStandar.addTo(map);
        markerGroup = L.layerGroup().addTo(map);
        renderMarkers();

        setTimeout(() => { map.invalidateSize(); }, 300);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        initMap();
    }

    // Utility: cegah XSS pada popup
    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        const entities = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
        return String(text).replace(/[&<>"']/g, function(m) { return entities[m]; });
    }

    // Toggle deskripsi singkat <-> penuh pada popup
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
    window.toggleDeskripsi = toggleDeskripsi;

    // Render titik marker pada peta
    function renderMarkers() {
        if (!markerGroup) return;
        markerGroup.clearLayers();

        let filterAktif = Array.from(document.querySelectorAll('.filter-cb:checked')).map(cb => cb.value);
        let validCoords = [];

        if (Array.isArray(dataLaporan) && dataLaporan.length > 0) {
            dataLaporan.forEach(function(laporan) {
                if (filterAktif.includes(laporan.status) && laporan.lokasi_gps && laporan.lokasi_gps !== 'tidak diketahui') {
                    let koordinat = laporan.lokasi_gps.split(',');
                    if (koordinat.length === 2) {
                        let lat = parseFloat(koordinat[0].trim());
                        let lng = parseFloat(koordinat[1].trim());

                        if (isNaN(lat) || isNaN(lng)) return;
                        validCoords.push([lat, lng]);

                        // 1. Warna Ikon & Badge Status Alur Baru
                        let warnaIkon = 'text-red-500';
                        let warnaBadge = 'bg-red-50 text-red-700';
                        let teksStatus = 'MENUNGGU VERIFIKASI';

                        if (laporan.status === 'menunggu') {
                            warnaIkon = 'text-amber-500';
                            warnaBadge = 'bg-amber-50 text-amber-700';
                            teksStatus = 'MENUNGGU';
                        } else if (laporan.status === 'diverifikasi') {
                            warnaIkon = 'text-blue-500';
                            warnaBadge = 'bg-blue-50 text-blue-700';
                            teksStatus = 'DIVERIFIKASI';
                        } else if (laporan.status === 'dalam_perbaikan') {
                            warnaIkon = 'text-orange-500';
                            warnaBadge = 'bg-orange-50 text-orange-700';
                            teksStatus = 'DALAM PERBAIKAN';
                        } else if (laporan.status === 'selesai') {
                            warnaIkon = 'text-emerald-500';
                            warnaBadge = 'bg-emerald-50 text-emerald-700';
                            teksStatus = 'SELESAI';
                        } else if (laporan.status === 'ditolak') {
                            warnaIkon = 'text-red-500';
                            warnaBadge = 'bg-red-50 text-red-700';
                            teksStatus = 'DITOLAK';
                        }

                        // 2. Pin Icon Marker
                        let ikonCustom = L.divIcon({
                            className: 'bg-transparent',
                            html: `<div style="text-shadow: 2px 2px 4px rgba(0,0,0,0.4); font-size:28px; line-height:1; display:inline-block;" class="${warnaIkon} hover:scale-110 transition-transform cursor-pointer drop-shadow">
                                       <i class="fas fa-map-marker-alt"></i>
                                   </div>`,
                            iconSize: [30, 42],
                            iconAnchor: [15, 40],
                            popupAnchor: [0, -35]
                        });

                        // 3. Link Detail & Google Maps
                        let urlMaps = `https://www.google.com/maps?q=${lat},${lng}`;
                        let urlDetail = `{{ url('/admin-jalan/laporan/detail') }}/${laporan.id}`;

                        // 4. Data Pelapor & Foto
                        let namaPelapor = (laporan.pelapor && laporan.pelapor.nama_lengkap) ? escapeHtml(laporan.pelapor.nama_lengkap) : 'Masyarakat';
                        let fotoSrc = (laporan.pelapor && laporan.pelapor.foto_profil) ? ('/storage/' + laporan.pelapor.foto_profil) : null;

                        let imgHtml = fotoSrc
                            ? `<img src="${fotoSrc}" alt="Profil" class="w-9 h-9 rounded-full object-cover border border-gray-200 shrink-0">`
                            : `<div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold border border-blue-200 uppercase shrink-0">${namaPelapor.substring(0, 1)}</div>`;

                        // 5. Jenis Kerusakan
                        let labelJenis = JENIS_KERUSAKAN_MAP[laporan.jenis_kerusakan] || escapeHtml(laporan.judul_laporan || 'Kerusakan Jalan');

                        // 6. Deskripsi ringkas + tombol toggle
                        let deskripsiAsli = laporan.deskripsi_laporan || 'Laporan kerusakan jalan dari masyarakat.';
                        let teksAman = escapeHtml(deskripsiAsli);
                        let batasKarakter = 65;
                        let teksSingkatAman = teksAman.length > batasKarakter ? escapeHtml(deskripsiAsli.substring(0, batasKarakter)) + '...' : teksAman;
                        let btnSelengkapnya = teksAman.length > batasKarakter ? `<button type="button" onclick="event.stopPropagation(); window.toggleDeskripsi(this)" class="text-blue-500 hover:text-blue-700 font-bold ml-1 transition">selengkapnya</button>` : '';

                        // 7. Bind Popup Leaflet
                        let marker = L.marker([lat, lng], {icon: ikonCustom});
                        marker.bindPopup(`
                            <div class="flex flex-col items-center">
                                <div class="px-4 pt-4 pb-2 text-center w-full">
                                    <p class="text-[10px] font-bold text-gray-400 mb-0.5 tracking-wider">ID: #${escapeHtml(laporan.id_laporan)}</p>
                                    <h3 class="font-extrabold text-gray-950 text-sm leading-tight uppercase mb-1.5 flex items-center justify-center flex-wrap">
                                        ${escapeHtml(labelJenis)}
                                    </h3>

                                    <div class="${warnaBadge} px-3 py-1 text-[9px] rounded-full font-extrabold my-1.5 uppercase tracking-widest border border-gray-100 inline-block shadow-inner">
                                        STATUS: ${teksStatus}
                                    </div>
                                </div>

                                <div class="w-full text-left bg-gray-50/70 p-3.5 border-y border-gray-100">
                                    <div class="flex items-start space-x-2.5 mb-1">
                                        ${imgHtml}
                                        <div class="flex-1 text-[11px] font-medium leading-relaxed break-words relative w-full text-left">
                                            <div class="text-gray-800 font-bold mb-0.5">${namaPelapor}</div>
                                            <span class="deskripsi-short text-gray-600">${teksSingkatAman}</span>
                                            <span class="deskripsi-full hidden text-gray-600">${teksAman}</span>
                                            ${btnSelengkapnya}
                                        </div>
                                    </div>
                                    ${laporan.alamat_map ? `<p class="text-[10px] text-gray-400 mt-1 truncate"><i class="fas fa-map-marker-alt mr-1 text-red-400"></i>${escapeHtml(laporan.alamat_map)}</p>` : ''}
                                </div>

                                <div class="w-full p-3.5 space-y-2 bg-white">
                                    <a href="${urlDetail}" class="w-full bg-[#1E3A8A] hover:bg-blue-800 text-white text-xs font-bold py-2.5 rounded-xl flex items-center justify-center transition shadow focus:outline-none hover:shadow-lg transform hover:-translate-y-0.5" style="text-decoration: none; color: white !important;">
                                        <i class="fas fa-eye mr-2"></i> Buka Detail Laporan
                                    </a>
                                    <a href="${urlMaps}" target="_blank" class="w-full bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 text-xs font-bold py-2 rounded-xl flex items-center justify-center transition shadow-sm focus:outline-none" style="text-decoration: none;">
                                        <i class="fas fa-map-marked-alt mr-2 text-green-600"></i> Buka Google Maps
                                    </a>
                                </div>
                            </div>
                        `, { maxWidth: 290, minWidth: 290 });

                        markerGroup.addLayer(marker);
                    }
                }
            });

            // Otomatis pusatkan kamera jika ada titik koordinat yang aktif
            if (validCoords.length > 0) {
                let bounds = L.latLngBounds(validCoords);
                map.fitBounds(bounds.pad(0.15), { maxZoom: 15 });
            }
        }
    }

    // Fungsi Ganti Layer Peta
    function gantiLayerPeta(jenis) {
        if (jenis === 'standar') {
            map.removeLayer(layerSatelit);
            layerStandar.addTo(map);
        } else {
            map.removeLayer(layerStandar);
            layerSatelit.addTo(map);
        }
        document.getElementById('menu-layer').classList.add('hidden');
    }

    // Toggle Dropdown Menu Peta
    function toggleMenuPeta(menuId) {
        let menu = document.getElementById(menuId);
        if (menu.classList.contains('hidden')) {
            document.getElementById('menu-layer').classList.add('hidden');
            document.getElementById('menu-filter').classList.add('hidden');
            menu.classList.remove('hidden');
        } else {
            menu.classList.add('hidden');
        }
    }

    // Fitur Layar Penuh (Fullscreen API)
    function toggleFullscreen() {
        let container = document.getElementById('peta-container');
        if (!document.fullscreenElement) {
            container.requestFullscreen().catch(err => {
                alert(`Gagal membuka mode layar penuh: ${err.message}`);
            });
        } else {
            document.exitFullscreen();
        }
    }

    document.addEventListener('fullscreenchange', (event) => {
        let wrapper = document.getElementById('map-wrapper');
        let teksBtn = document.getElementById('text-fs');
        let iconBtn = document.getElementById('icon-fs');

        if (document.fullscreenElement) {
            wrapper.style.height = 'calc(100vh - 120px)';
            teksBtn.innerText = "Keluar Penuh";
            iconBtn.classList.remove('fa-expand');
            iconBtn.classList.add('fa-compress');
        } else {
            wrapper.style.height = '440px';
            teksBtn.innerText = "Layar Penuh";
            iconBtn.classList.remove('fa-compress');
            iconBtn.classList.add('fa-expand');
        }
        setTimeout(() => { map.invalidateSize(); }, 300);
    });

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#btn-layer') && !event.target.closest('#menu-layer')) {
            let menuL = document.getElementById('menu-layer');
            if (menuL) menuL.classList.add('hidden');
        }
        if (!event.target.closest('#btn-filter') && !event.target.closest('#menu-filter')) {
            let menuF = document.getElementById('menu-filter');
            if (menuF) menuF.classList.add('hidden');
        }
    });
</script>
@endpush
