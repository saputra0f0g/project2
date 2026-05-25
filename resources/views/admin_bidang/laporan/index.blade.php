@extends('layouts.app_bidang')

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    .leaflet-popup-content { margin: 12px; }

    /* Animasi Masuk */
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

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 animasi-masuk">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Kelola Laporan & Penugasan</h2>
            <p class="text-sm text-gray-500 font-medium">Manajemen penugasan tim lapangan (UPTD) dan pemantauan infrastruktur.</p>
        </div>

        <div class="flex space-x-3">
            <a href="{{ route('admin_bidang.laporan.ekspor_excel') ?? '#' }}" class="bg-green-50 border border-green-200 text-green-700 hover:bg-green-100 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center hover:shadow-md transform hover:-translate-y-0.5">
                <i class="fas fa-file-excel mr-2"></i> Rekap Excel
            </a>
            <a href="{{ route('admin_bidang.laporan.ekspor_pdf') ?? '#' }}" target="_blank" class="bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center hover:shadow-md transform hover:-translate-y-0.5">
                <i class="fas fa-file-pdf mr-2"></i> Rekap PDF
            </a>
        </div>
    </div>

    @if(session('sukses'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center animasi-masuk">
            <i class="fas fa-check-circle mr-3 text-lg"></i> {{ session('sukses') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 animasi-masuk delay-1">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-4 hover:border-blue-200 transition cursor-pointer group">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform"><i class="fas fa-file-invoice"></i></div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Total Laporan</p>
                <h4 class="text-2xl font-extrabold text-gray-800 leading-none">{{ number_format($statistik['total'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-4 hover:border-yellow-200 transition cursor-pointer group">
            <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Menunggu UPTD</p>
                <h4 class="text-2xl font-extrabold text-gray-800 leading-none">{{ number_format($statistik['menunggu'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-4 hover:border-indigo-200 transition cursor-pointer group">
            <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform"><i class="fas fa-tools"></i></div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Sedang Dikerjakan</p>
                <h4 class="text-2xl font-extrabold text-gray-800 leading-none">{{ number_format($statistik['proses'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-4 hover:border-green-200 transition cursor-pointer group">
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform"><i class="fas fa-check-circle"></i></div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Selesai</p>
                <h4 class="text-2xl font-extrabold text-gray-800 leading-none">{{ number_format($statistik['selesai'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-8 overflow-visible animasi-masuk delay-2">

        <form method="GET" action="{{ route('admin_bidang.laporan') }}" id="form-filter" class="p-5 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4 bg-white relative z-20 rounded-t-2xl">

            <div class="flex items-center gap-3">
                <div class="flex items-center text-sm font-bold text-gray-700">
                    <i class="fas fa-filter mr-2 text-pupr-blue"></i> Filter Status:
                </div>
                <div class="relative custom-dropdown w-52" data-name="status">
                    <select name="status" class="hidden">
                        <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="diteruskan" {{ request('status') == 'diteruskan' ? 'selected' : '' }}>Menunggu Penugasan</option>
                        <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <div class="dropdown-btn flex items-center justify-between bg-white border border-gray-200 rounded-xl pl-4 pr-3 py-2 text-sm text-gray-600 font-bold cursor-pointer hover:border-pupr-blue hover:bg-blue-50 hover:text-pupr-blue transition shadow-sm w-full">
                        <span class="dropdown-text text-gray-700">Semua Status</span>
                        <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                    </div>
                    <ul class="dropdown-menu hidden absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] overflow-hidden transform scale-95 opacity-0 transition-all duration-200 origin-top">
                        <li data-value="semua" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition flex items-center justify-between {{ request('status') == 'semua' || !request('status') ? 'bg-blue-50 text-pupr-blue' : '' }}">
                            Semua Status {!! request('status') == 'semua' || !request('status') ? '<i class="fas fa-check text-pupr-blue"></i>' : '' !!}
                        </li>
                        <li data-value="diteruskan" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition flex items-center justify-between border-t border-gray-50 {{ request('status') == 'diteruskan' ? 'bg-blue-50 text-pupr-blue' : '' }}">
                            Menunggu Penugasan {!! request('status') == 'diteruskan' ? '<i class="fas fa-check text-pupr-blue"></i>' : '' !!}
                        </li>
                        <li data-value="proses" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition flex items-center justify-between border-t border-gray-50 {{ request('status') == 'proses' ? 'bg-blue-50 text-pupr-blue' : '' }}">
                            Sedang Dikerjakan {!! request('status') == 'proses' ? '<i class="fas fa-check text-pupr-blue"></i>' : '' !!}
                        </li>
                        <li data-value="selesai" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition flex items-center justify-between border-t border-gray-50 {{ request('status') == 'selesai' ? 'bg-blue-50 text-pupr-blue' : '' }}">
                            Selesai {!! request('status') == 'selesai' ? '<i class="fas fa-check text-pupr-blue"></i>' : '' !!}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex items-center gap-2 text-sm text-gray-700 font-bold">
                Urutkan:
                <div class="relative ml-1 custom-dropdown w-36" data-name="sort">
                    <select name="sort" class="hidden">
                        <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                        <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                    </select>
                    <div class="dropdown-btn flex items-center justify-between bg-white border border-gray-200 rounded-xl pl-4 pr-3 py-2 text-sm text-gray-600 font-bold cursor-pointer hover:border-pupr-blue hover:bg-blue-50 hover:text-pupr-blue transition shadow-sm w-full">
                        <span class="dropdown-text text-gray-700">Terbaru</span>
                        <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                    </div>
                    <ul class="dropdown-menu hidden absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] overflow-hidden transform scale-95 opacity-0 transition-all duration-200 origin-top">
                        <li data-value="terbaru" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition flex items-center justify-between {{ request('sort') == 'terbaru' || !request('sort') ? 'bg-blue-50 text-pupr-blue' : '' }}">
                            Terbaru {!! request('sort') == 'terbaru' || !request('sort') ? '<i class="fas fa-check text-pupr-blue"></i>' : '' !!}
                        </li>
                        <li data-value="terlama" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition flex items-center justify-between border-t border-gray-50 {{ request('sort') == 'terlama' ? 'bg-blue-50 text-pupr-blue' : '' }}">
                            Terlama {!! request('sort') == 'terlama' ? '<i class="fas fa-check text-pupr-blue"></i>' : '' !!}
                        </li>
                    </ul>
                </div>
            </div>

        </form>

        <div class="overflow-x-auto relative z-10">
            <table class="w-full text-left border-collapse">
                <thead class="text-gray-400 text-[11px] uppercase font-bold tracking-wider border-b border-gray-100 bg-white">
                    <tr>
                        <th class="px-6 py-4">NO</th>
                        <th class="px-6 py-4">ID LAPORAN</th>
                        <th class="px-6 py-4">LOKASI</th>
                        <th class="px-6 py-4">TANGGAL MASUK</th>
                        <th class="px-6 py-4">STATUS</th>
                        <th class="px-6 py-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm font-medium text-gray-700 bg-white">
                    @forelse($laporan_masuk as $index => $item)
                    <tr class="hover:bg-blue-50/50 transition duration-300">
                        <td class="px-6 py-5 text-gray-500">{{ $laporan_masuk->firstItem() + $index }}</td>
                        <td class="px-6 py-5 font-bold text-pupr-blue hover:underline cursor-pointer" onclick="window.location.href='{{ route('admin_bidang.laporan.detail', $item->id) }}'">{{ $item->id_laporan }}</td>
                        <td class="px-6 py-5">
                            <p class="text-gray-800 font-bold">{{ explode(',', $item->alamat_map)[0] ?? 'Lokasi' }}</p>
                            <p class="text-[10px] text-gray-400">{{ Str::limit($item->alamat_map, 40) }}</p>
                        </td>
                        <td class="px-6 py-5 text-gray-500">
                            {{ \Carbon\Carbon::parse($item->updated_at)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-6 py-5">
                            @if($item->status == 'diteruskan')
                                <span class="flex items-center text-yellow-600 bg-yellow-50 px-2.5 py-1.5 rounded-full w-max text-[10px] font-bold border border-yellow-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5 animate-pulse"></span> Menunggu Penugasan
                                </span>
                            @elseif($item->status == 'proses')
                                <span class="flex items-center text-indigo-600 bg-indigo-50 px-2.5 py-1.5 rounded-full w-max text-[10px] font-bold border border-indigo-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-1.5"></span> Dalam Pengerjaan
                                </span>
                            @else
                                <span class="flex items-center text-green-600 bg-green-50 px-2.5 py-1.5 rounded-full w-max text-[10px] font-bold border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Selesai
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 flex items-center justify-center">
                            <a href="{{ route('admin_bidang.laporan.detail', $item->id) }}" class="bg-blue-50 text-blue-600 hover:bg-pupr-blue hover:text-white px-4 py-2 rounded-lg text-xs font-bold transition flex items-center border border-blue-100 hover:border-pupr-blue hover:shadow-md">
                                @if($item->status == 'diteruskan')
                                    <i class="fas fa-paper-plane mr-2"></i> Tugaskan
                                @else
                                    <i class="fas fa-info-circle mr-2"></i> Detail Laporan
                                @endif
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium bg-gray-50/30">
                            <i class="fas fa-search text-3xl mb-3 text-gray-300 block"></i>
                            Tidak ada laporan yang sesuai dengan pencarian atau filter Anda.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-5 border-t border-gray-100 bg-white">
            {{ $laporan_masuk->links('pagination::tailwind') }}
        </div>
    </div>

    <div class="w-full bg-white rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden h-[400px] animasi-masuk delay-3">
        <div class="absolute top-4 left-4 z-[1000] bg-white/95 backdrop-blur px-3 py-1.5 rounded-md border border-gray-200 shadow-sm flex items-center">
            <i class="far fa-map text-pupr-blue mr-2"></i>
            <span class="text-[10px] font-extrabold text-gray-700 tracking-wider">SEBARAN LAPORAN TERBARU</span>
        </div>
        <div id="mapDashboard" class="w-full h-full bg-gray-200 z-0"></div>
    </div>

</div>

@if(session('sukses'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Dtugaskan!',
            text: "{{ session('sukses') }}",
            timer: 3000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-2xl shadow-xl border border-gray-100' }
        });
    });
</script>
@endif

@endsection

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // --- SCRIPT CUSTOM SELECT DROPDOWN (UI PREMIUM) ---
    document.addEventListener("DOMContentLoaded", function() {
        const dropdowns = document.querySelectorAll('.custom-dropdown');

        dropdowns.forEach(dropdown => {
            const select = dropdown.querySelector('select');
            const btn = dropdown.querySelector('.dropdown-btn');
            const text = dropdown.querySelector('.dropdown-text');
            const menu = dropdown.querySelector('.dropdown-menu');
            const icon = btn.querySelector('.fa-chevron-down');
            const listItems = menu.querySelectorAll('li');

            // Set teks awal sesuai opsi yang terpilih di HTML
            const activeOpt = select.options[select.selectedIndex];
            if(activeOpt) text.innerText = activeOpt.text;

            // Fungsi Buka Tutup
            btn.addEventListener('click', (e) => {
                e.stopPropagation();

                // Tutup menu dropdown lain yang mungkin sedang terbuka
                document.querySelectorAll('.custom-dropdown .dropdown-menu').forEach(m => {
                    if(m !== menu) {
                        m.classList.add('hidden', 'opacity-0', 'scale-95');
                        m.previousElementSibling.querySelector('.fa-chevron-down').classList.remove('rotate-180');
                    }
                });

                if(menu.classList.contains('hidden')) {
                    menu.classList.remove('hidden');
                    setTimeout(() => {
                        menu.classList.remove('opacity-0', 'scale-95');
                        icon.classList.add('rotate-180');
                    }, 10);
                } else {
                    closeMenu();
                }
            });

            // Saat opsi di list diklik
            listItems.forEach(item => {
                item.addEventListener('click', () => {
                    let value = item.getAttribute('data-value');
                    select.value = value;
                    text.innerText = item.innerText;
                    closeMenu();

                    // Submit form secara otomatis
                    document.getElementById('form-filter').submit();
                });
            });

            function closeMenu() {
                menu.classList.add('opacity-0', 'scale-95');
                icon.classList.remove('rotate-180');
                setTimeout(() => { menu.classList.add('hidden'); }, 200);
            }
        });

        // Tutup jika klik sembarang tempat
        document.addEventListener('click', () => {
            document.querySelectorAll('.custom-dropdown .dropdown-menu').forEach(menu => {
                menu.classList.add('opacity-0', 'scale-95');
                menu.previousElementSibling.querySelector('.fa-chevron-down').classList.remove('rotate-180');
                setTimeout(() => { menu.classList.add('hidden'); }, 200);
            });
        });
    });

    // --- SCRIPT PETA LEAFLET ---
    let map, markerGroup;
    let dataLaporan = @json($sebaran_laporan ?? []);

    document.addEventListener("DOMContentLoaded", function() {
        map = L.map('mapDashboard', { zoomControl: false }).setView([-6.5627, 107.7613], 12);
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19, attribution: '© OpenStreetMap'
        }).addTo(map);

        markerGroup = L.layerGroup().addTo(map);

        if(dataLaporan.length > 0) {
            dataLaporan.forEach(function(laporan) {
                if(laporan.lokasi_gps) {
                    let koordinat = laporan.lokasi_gps.split(',');
                    if(koordinat.length == 2) {
                        let lat = parseFloat(koordinat[0].trim());
                        let lng = parseFloat(koordinat[1].trim());

                        let warnaBg = 'bg-yellow-500';
                        if(laporan.status === 'proses') warnaBg = 'bg-indigo-500';
                        if(laporan.status === 'selesai') warnaBg = 'bg-green-500';

                        let ikonCustom = L.divIcon({
                            className: 'bg-transparent',
                            html: `
                                <div class="relative w-6 h-6 flex items-center justify-center hover:scale-125 transition-transform duration-300">
                                    <div class="absolute inset-0 ${warnaBg} rounded-full opacity-40 animate-ping"></div>
                                    <div class="w-3 h-3 ${warnaBg} border-2 border-white rounded-full relative z-10 shadow-md"></div>
                                </div>
                            `,
                            iconSize: [24, 24], iconAnchor: [12, 12], popupAnchor: [0, -10]
                        });

                        let marker = L.marker([lat, lng], {icon: ikonCustom});
                        marker.bindPopup(`
                            <div class="text-center p-1">
                                <p class="text-[10px] font-bold text-gray-500 mb-1">${laporan.id_laporan}</p>
                                <p class="font-bold text-gray-800 text-xs mb-2 uppercase">${laporan.status}</p>
                                <a href="/admin-bidang/laporan/detail/${laporan.id}" class="text-[10px] bg-blue-50 text-blue-600 px-3 py-1.5 rounded-md font-bold block hover:bg-blue-100 border border-blue-100 transition shadow-sm">Buka Detail</a>
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
