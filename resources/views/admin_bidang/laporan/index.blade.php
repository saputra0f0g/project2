@extends('layouts.app_bidang')

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    .leaflet-popup-content { margin: 12px; }
</style>
@endpush

@section('konten')
<div class="max-w-7xl mx-auto pb-10">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Kelola Laporan</h2>
            <p class="text-sm text-gray-500 font-medium">Manajemen pengaduan masyarakat dan pemantauan infrastruktur.</p>
        </div>

        <div class="flex space-x-3">
            <a href="{{ route('admin_bidang.laporan.ekspor_excel') }}" class="bg-green-50 border border-green-200 text-green-700 hover:bg-green-100 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center">
                <i class="fas fa-file-excel mr-2"></i> Rekap Excel
            </a>
            <a href="{{ route('admin_bidang.laporan.ekspor_pdf') }}" target="_blank" class="bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center">
                <i class="fas fa-file-pdf mr-2"></i> Rekap PDF
            </a>
        </div>
    </div>

    @if(session('sukses'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center">
            <i class="fas fa-check-circle mr-3 text-lg"></i> {{ session('sukses') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Total Laporan</p>
                <h4 class="text-2xl font-extrabold text-gray-800 leading-none">{{ number_format($statistik['total'], 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-500 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Menunggu</p>
                <h4 class="text-2xl font-extrabold text-gray-800 leading-none">{{ number_format($statistik['menunggu'], 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-tools"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Dalam Proses</p>
                <h4 class="text-2xl font-extrabold text-gray-800 leading-none">{{ number_format($statistik['proses'], 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Selesai</p>
                <h4 class="text-2xl font-extrabold text-gray-800 leading-none">{{ number_format($statistik['selesai'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-8 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4 bg-white">
            <div class="flex gap-3">
                <div class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 font-medium flex items-center cursor-pointer hover:bg-gray-50">
                    Semua Kategori <i class="fas fa-chevron-down ml-3 text-gray-400 text-xs"></i>
                </div>
                <div class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 font-medium flex items-center cursor-pointer hover:bg-gray-50">
                    Status: Semua <i class="fas fa-filter ml-3 text-gray-400 text-xs"></i>
                </div>
            </div>
            <div class="text-sm text-gray-600 font-medium flex items-center">
                Urutkan:
                <span class="ml-2 border border-gray-200 rounded-lg px-3 py-2 cursor-pointer hover:bg-gray-50">
                    Terbaru <i class="fas fa-chevron-down ml-2 text-gray-400 text-xs"></i>
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="text-gray-400 text-[11px] uppercase font-bold tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">NO</th>
                        <th class="px-6 py-4">ID LAPORAN</th>
                        <th class="px-6 py-4">LOKASI</th>
                        <th class="px-6 py-4">KATEGORI</th>
                        <th class="px-6 py-4">TANGGAL</th>
                        <th class="px-6 py-4">STATUS</th>
                        <th class="px-6 py-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm font-medium text-gray-700">
                    @forelse($laporan_masuk as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-5 text-gray-500">{{ $laporan_masuk->firstItem() + $index }}</td>
                        <td class="px-6 py-5 font-bold text-pupr-blue">{{ $item->id_laporan }}</td>
                        <td class="px-6 py-5">
                            <p class="text-gray-800 font-bold">{{ explode(',', $item->alamat_map)[0] ?? 'Lokasi' }}</p>
                            <p class="text-[10px] text-gray-400">{{ Str::limit($item->alamat_map, 30) }}</p>
                        </td>
                        <td class="px-6 py-5">
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-extrabold uppercase border border-blue-100">
                                {{ $item->kategori_bidang }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-gray-500">
                            {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-6 py-5">
                            @if($item->status == 'diteruskan')
                                <span class="flex items-center text-yellow-600 bg-yellow-50 px-2.5 py-1 rounded-full w-max text-[10px] font-bold border border-yellow-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span> Menunggu
                                </span>
                            @elseif($item->status == 'proses')
                                <span class="flex items-center text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full w-max text-[10px] font-bold border border-indigo-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-1.5"></span> Proses
                                </span>
                            @else
                                <span class="flex items-center text-green-600 bg-green-50 px-2.5 py-1 rounded-full w-max text-[10px] font-bold border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Selesai
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 flex items-center justify-center space-x-3">
                            <a href="{{ route('admin_bidang.laporan.detail', $item->id) }}" class="text-blue-500 hover:text-blue-700" title="Lihat Detail">
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('admin_bidang.laporan.detail', $item->id) }}" class="text-green-500 hover:text-green-700" title="Tugaskan Pekerja">
                                <i class="fas fa-user-plus"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-400 font-medium">Belum ada laporan yang masuk ke bidang Anda.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-5 border-t border-gray-100">
            {{ $laporan_masuk->links('pagination::tailwind') }}
        </div>
    </div>

    <div class="w-full bg-white rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden h-[400px]">
        <div class="absolute top-4 left-4 z-[1000] bg-white/95 backdrop-blur px-3 py-1.5 rounded-md border border-gray-200 shadow-sm flex items-center">
            <i class="far fa-map text-pupr-blue mr-2"></i>
            <span class="text-[10px] font-extrabold text-gray-700 tracking-wider">SEBARAN LAPORAN TERBARU</span>
        </div>

        <div id="mapDashboard" class="w-full h-full bg-gray-200 z-0"></div>
    </div>

</div>
@endsection

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    let map, markerGroup;
    let dataLaporan = @json($sebaran_laporan ?? []);

    document.addEventListener("DOMContentLoaded", function() {
        // Inisialisasi Peta
        map = L.map('mapDashboard', {
            zoomControl: false
        }).setView([-6.5627, 107.7613], 12);

        // Tambahkan Zoom control di pojok kanan bawah
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Gaya Dark Mode / Voyager
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        markerGroup = L.layerGroup().addTo(map);

        // Render Titik Laporan
        if(dataLaporan.length > 0) {
            dataLaporan.forEach(function(laporan) {
                if(laporan.lokasi_gps) {
                    let koordinat = laporan.lokasi_gps.split(',');
                    if(koordinat.length == 2) {
                        let lat = parseFloat(koordinat[0].trim());
                        let lng = parseFloat(koordinat[1].trim());

                        // Tentukan Warna sesuai Status
                        let warnaBg = 'bg-yellow-500'; // Menunggu
                        if(laporan.status === 'proses') warnaBg = 'bg-indigo-500';
                        if(laporan.status === 'selesai') warnaBg = 'bg-green-500';

                        // Membuat Ikon Titik Minimalis
                        let ikonCustom = L.divIcon({
                            className: 'bg-transparent',
                            html: `
                                <div class="relative w-6 h-6 flex items-center justify-center">
                                    <div class="absolute inset-0 ${warnaBg} rounded-full opacity-40 animate-ping"></div>
                                    <div class="w-3 h-3 ${warnaBg} border-2 border-white rounded-full relative z-10 shadow-md"></div>
                                </div>
                            `,
                            iconSize: [24, 24],
                            iconAnchor: [12, 12],
                            popupAnchor: [0, -10]
                        });

                        let marker = L.marker([lat, lng], {icon: ikonCustom});
                        marker.bindPopup(`
                            <div class="text-center p-1">
                                <p class="text-[10px] font-bold text-gray-500 mb-1">${laporan.id_laporan}</p>
                                <p class="font-bold text-gray-800 text-xs mb-2">${laporan.kategori_bidang}</p>
                                <a href="/admin-bidang/laporan/detail/${laporan.id_laporan}" class="text-[10px] bg-blue-50 text-blue-600 px-3 py-1 rounded-md font-bold block hover:bg-blue-100">Buka Detail</a>
                            </div>
                        `);
                        markerGroup.addLayer(marker);
                    }
                }
            });
        }
    });
</script>
@endpush
