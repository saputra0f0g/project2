@extends('layouts.app_bidang')

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    /* Transisi Peta */
    #mapLokasi { transition: all 0.3s ease; }
    .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }

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

    <div class="flex justify-between items-center mb-8 animasi-masuk">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Monitoring Kerja Real-time</h2>
            <p class="text-sm text-gray-500 font-medium mt-1">Pantau progres dan lokasi personil lapangan secara langsung.</p>
        </div>
        <div class="flex items-center space-x-3">
            <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-xs font-bold flex items-center border border-green-200">
                <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span> {{ $laporan_berjalan->count() }} Petugas Aktif
            </span>
            <a href="{{ route('admin_bidang.laporan') }}" class="bg-yellow-400 hover:bg-yellow-500 text-white px-5 py-2 rounded-lg text-sm font-bold shadow transition hover:shadow-md transform hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i> Tugas Baru
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 animasi-masuk delay-1">
                @forelse($laporan_berjalan as $laporan)
                <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm relative hover:border-blue-200 transition-colors">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-[10px] font-extrabold text-pupr-yellow uppercase tracking-wider bg-yellow-50 px-2 py-1 rounded">{{ $laporan->kategori_bidang }}</span>

                        <div class="relative">
                            <button id="btn-opsi-{{ $laporan->id }}" onclick="toggleOpsiCard({{ $laporan->id }})" class="text-gray-400 hover:text-gray-800 p-1 focus:outline-none transition">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <div id="menu-opsi-{{ $laporan->id }}" class="hidden absolute right-0 top-6 mt-1 w-44 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-10 origin-top-right">
                                <a href="{{ route('admin_bidang.laporan.detail', $laporan->id) }}" class="block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-blue-50 hover:text-pupr-blue transition">
                                    <i class="fas fa-info-circle w-4 mr-2"></i> Detail Laporan
                                </a>
                                @if($laporan->lokasi_gps)
                                    <button onclick="fokusKePeta('{{ $laporan->lokasi_gps }}', '{{ $laporan->id_laporan }}')" class="w-full text-left block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-blue-50 hover:text-pupr-blue transition">
                                        <i class="fas fa-map-marker-alt w-4 mr-2"></i> Sorot di Peta
                                    </button>
                                @endif
                                @if($laporan->pekerja && $laporan->pekerja->nomor_hp)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $laporan->pekerja->nomor_hp) }}" target="_blank" class="block px-4 py-2 text-xs font-semibold text-green-600 hover:bg-green-50 transition border-t border-gray-50 mt-1 pt-2">
                                        <i class="fab fa-whatsapp w-4 mr-2"></i> Hubungi Petugas
                                    </a>
                                @else
                                    <button disabled class="w-full text-left block px-4 py-2 text-xs font-semibold text-gray-400 cursor-not-allowed border-t border-gray-50 mt-1 pt-2" title="Nomor HP Petugas tidak terdaftar">
                                        <i class="fab fa-whatsapp w-4 mr-2"></i> Hubungi Petugas
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <h3 class="font-bold text-gray-800 mb-4">{{ Str::limit($laporan->deskripsi_laporan, 30) }}</h3>

                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-pupr-blue flex items-center justify-center font-bold text-lg mr-3">
                            {{ substr($laporan->pekerja->nama_lengkap ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $laporan->pekerja->nama_lengkap ?? 'Petugas UPTD' }}</p>
                            <p class="text-[11px] text-gray-500"><i class="far fa-clock mr-1"></i> Mulai: {{ $laporan->updated_at->format('H:i') }} WIB</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="flex justify-between text-[11px] font-bold mb-1">
                            <span class="text-gray-500">Progres Kerja</span>
                            <span class="text-pupr-blue">Sedang Berjalan</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-pupr-blue h-1.5 rounded-full w-[65%]"></div>
                        </div>
                    </div>

                    <a href="{{ route('admin_bidang.laporan.detail', $laporan->id) }}" class="w-full text-center block bg-gray-50 border border-gray-200 text-gray-600 text-xs font-bold py-2 rounded-lg hover:bg-blue-50 hover:text-pupr-blue hover:border-blue-100 transition">Lihat Detail Laporan</a>
                </div>
                @empty
                <div class="col-span-2 bg-gray-50 border border-dashed border-gray-300 rounded-2xl p-8 text-center text-gray-500">
                    <i class="fas fa-hard-hat text-4xl mb-3 text-gray-300"></i>
                    <p class="font-bold">Tidak ada pekerjaan aktif saat ini.</p>
                </div>
                @endforelse
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 animasi-masuk delay-2">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-800">Daftar Antrean Tugas</h3>
                    <button onclick="bukaModalAntrean()" class="text-sm font-bold text-pupr-blue hover:underline">Lihat Semua</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="text-xs text-gray-400 font-bold border-b border-gray-100">
                            <tr>
                                <th class="pb-3 px-2">Tugas</th>
                                <th class="pb-3 px-2">Petugas</th>
                                <th class="pb-3 px-2">Status</th>
                                <th class="pb-3 px-2">Update</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="text-sm" id="tabel-antrean-dashboard">
                            @foreach($antrean_tugas->take(4) as $antrean)
                            <tr class="border-b border-gray-50 baris-antrean hover:bg-gray-50/50 transition group" data-id="{{ $antrean->id }}">
                                <td class="py-4 px-2 font-semibold text-gray-800">{{ Str::limit($antrean->deskripsi_laporan, 25) }}</td>
                                <td class="py-4 px-2 text-gray-600">
                                    <span class="flex items-center">
                                        <div class="w-6 h-6 rounded-full bg-blue-50 text-pupr-blue flex items-center justify-center text-[10px] font-bold mr-2">
                                            {{ substr($antrean->pekerja->nama_lengkap ?? 'U', 0, 2) }}
                                        </div>
                                        {{ explode(' ', $antrean->pekerja->nama_lengkap ?? 'Belum Ditentukan')[0] }}
                                    </span>
                                </td>
                                <td class="py-4 px-2">
                                    @if($antrean->status == 'proses')
                                        <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded text-[10px] font-bold">Sedang Dikerjakan</span>
                                    @else
                                        <span class="bg-yellow-50 text-yellow-600 px-2 py-1 rounded text-[10px] font-bold">Diteruskan</span>
                                    @endif
                                </td>
                                <td class="py-4 px-2 text-gray-500">{{ $antrean->updated_at->format('H:i') }} WIB</td>
                                <td class="py-4 px-2 text-right">
                                    <a href="{{ route('admin_bidang.laporan.detail', $antrean->id) }}" class="text-gray-400 group-hover:text-pupr-blue transition"><i class="fas fa-chevron-right"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 animasi-masuk delay-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-800 flex items-center"><i class="fas fa-map text-pupr-blue mr-2"></i> Lokasi Personil</h3>
                    <span class="text-[10px] text-gray-400 font-bold">Live Update</span>
                </div>
                <div class="relative w-full h-64 rounded-xl overflow-hidden border border-gray-200 z-0" id="mapLokasi">
                    <button onclick="toggleFullscreenMap()" class="absolute bottom-4 right-4 bg-white/90 backdrop-blur text-gray-800 px-3 py-2 rounded-lg text-xs font-bold shadow-lg border border-gray-200 z-[1000] hover:bg-gray-50 transition flex items-center">
                        <i class="fas fa-expand mr-2" id="icon-fs-map"></i> <span id="teks-fs-map">Perbesar Peta</span>
                    </button>
                </div>
            </div>

            <div class="bg-[#2B354E] rounded-2xl shadow-sm p-6 text-white animasi-masuk delay-3">
                <h3 class="font-bold text-sm mb-5 border-b border-gray-600 pb-3">Ringkasan Hari Ini</h3>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-[#1E273D] p-4 rounded-xl border border-gray-600">
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Total Aktif</p>
                        <p class="text-2xl font-bold">{{ $total_tugas }}</p>
                    </div>
                    <div class="bg-[#1E273D] p-4 rounded-xl border border-gray-600">
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Tugas Selesai</p>
                        <p class="text-2xl font-bold">{{ $tugas_selesai }}</p>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-bold mb-2">
                        <span class="text-gray-400">Efisiensi Tim</span>
                        <span class="text-yellow-400">{{ $efisiensi }}%</span>
                    </div>
                    <div class="w-full bg-[#1E273D] rounded-full h-2">
                        <div class="bg-yellow-400 h-2 rounded-full transition-all duration-1000" style="width: {{ $efisiensi }}%"></div>
                    </div>
                </div>
            </div>

            @if($peringatan->count() > 0)
            <div class="bg-red-50 rounded-2xl border border-red-200 shadow-sm p-5 animasi-masuk delay-4">
                <h3 class="font-bold text-red-800 flex items-center text-sm mb-4">
                    <i class="fas fa-exclamation-circle mr-2 text-lg animate-bounce"></i> Peringatan ({{ $peringatan->count() }})
                </h3>
                <div class="space-y-3">
                    @foreach($peringatan as $alert)
                    <div class="bg-white p-3 rounded-lg border border-red-100 shadow-sm">
                        <p class="text-xs font-bold text-red-600 mb-1">Laporan #{{ $alert->id_laporan }} Overdue</p>
                        <p class="text-[10px] text-gray-600">Petugas <b>{{ $alert->pekerja->nama_lengkap ?? 'UPTD' }}</b> belum menyelesaikan tugas lebih dari 24 jam.</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<div id="modal-antrean" class="fixed inset-0 z-[4000] hidden items-center justify-center">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="tutupModalAntrean()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 max-h-[85vh] flex flex-col overflow-hidden animasi-masuk">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Daftar Seluruh Antrean</h3>
                <p class="text-xs text-gray-500 mt-1">Daftar ini hanya dikelola pada tampilan perangkat Anda.</p>
            </div>
            <button onclick="tutupModalAntrean()" class="text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
        </div>

        <div class="p-0 overflow-y-auto flex-1 bg-white">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[10px] text-gray-400 font-bold uppercase tracking-wider sticky top-0 border-b border-gray-200 z-10">
                    <tr>
                        <th class="py-3 px-6">ID & Laporan</th>
                        <th class="py-3 px-6">Petugas</th>
                        <th class="py-3 px-6">Waktu</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100" id="tabel-semua-antrean">
                    @foreach($antrean_tugas as $antrean)
                    <tr class="hover:bg-gray-50 baris-antrean transition" data-id="{{ $antrean->id }}">
                        <td class="py-3 px-6">
                            <p class="font-bold text-pupr-blue hover:underline cursor-pointer" onclick="window.location.href='{{ route('admin_bidang.laporan.detail', $antrean->id) }}'">#{{ $antrean->id_laporan }}</p>
                            <p class="text-xs text-gray-600">{{ Str::limit($antrean->deskripsi_laporan, 30) }}</p>
                        </td>
                        <td class="py-3 px-6 font-semibold text-gray-700">{{ $antrean->pekerja->nama_lengkap ?? 'Menunggu...' }}</td>
                        <td class="py-3 px-6 text-xs text-gray-500">{{ $antrean->updated_at->diffForHumans() }}</td>
                        <td class="py-3 px-6 text-center">
                            <button onclick="sembunyikanAntrean({{ $antrean->id }})" class="w-8 h-8 rounded bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition" title="Sembunyikan dari antrean">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
            <button onclick="bersihkanSemuaAntrean()" class="text-xs font-bold text-red-500 hover:underline flex items-center">
                <i class="fas fa-trash-alt mr-1"></i> Bersihkan Antrean
            </button>
            <button onclick="tutupModalAntrean()" class="px-5 py-2 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-lg text-sm font-bold transition">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // --- LOGIKA TOGGLE MENU OPSI KARTU ---
    function toggleOpsiCard(id) {
        let menuId = 'menu-opsi-' + id;
        let elemenMenu = document.getElementById(menuId);
        document.querySelectorAll('[id^="menu-opsi-"]').forEach(el => {
            if(el.id !== menuId) el.classList.add('hidden');
        });
        elemenMenu.classList.toggle('hidden');
    }

    document.addEventListener('click', function(event) {
        if(!event.target.closest('[id^="btn-opsi-"]') && !event.target.closest('[id^="menu-opsi-"]')) {
            document.querySelectorAll('[id^="menu-opsi-"]').forEach(el => el.classList.add('hidden'));
        }
    });

    // --- LOGIKA LOCAL STORAGE ANTREAN ---
    document.addEventListener("DOMContentLoaded", function() { jalankanFilterAntrean(); });

    function jalankanFilterAntrean() {
        let hiddenIDs = JSON.parse(localStorage.getItem('hidden_antrean')) || [];
        document.querySelectorAll('.baris-antrean').forEach(row => {
            let id = parseInt(row.getAttribute('data-id'));
            if(hiddenIDs.includes(id)) row.style.display = 'none';
        });
    }

    function sembunyikanAntrean(id) {
        let hiddenIDs = JSON.parse(localStorage.getItem('hidden_antrean')) || [];
        if(!hiddenIDs.includes(id)) hiddenIDs.push(id);
        localStorage.setItem('hidden_antrean', JSON.stringify(hiddenIDs));
        jalankanFilterAntrean();
    }

    function bersihkanSemuaAntrean() {
        let semuaID = [];
        document.querySelectorAll('.baris-antrean').forEach(row => {
            semuaID.push(parseInt(row.getAttribute('data-id')));
        });
        localStorage.setItem('hidden_antrean', JSON.stringify(semuaID));
        jalankanFilterAntrean();
        tutupModalAntrean();
    }

    function bukaModalAntrean() {
        document.getElementById('modal-antrean').classList.remove('hidden');
        document.getElementById('modal-antrean').classList.add('flex');
    }
    function tutupModalAntrean() {
        document.getElementById('modal-antrean').classList.add('hidden');
        document.getElementById('modal-antrean').classList.remove('flex');
    }

    // --- LEAFLET PETA PERSONIL ---
    let mapLokasi;
    let dataPekerja = @json($laporan_berjalan);
    let markerCollection = {};

    document.addEventListener("DOMContentLoaded", function() {
        mapLokasi = L.map('mapLokasi').setView([-6.5627, 107.7613], 12);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 20 }).addTo(mapLokasi);

        let iconPetugas = L.divIcon({
            className: 'bg-transparent',
            html: `<div class="relative w-8 h-8 transition-transform hover:scale-110">
                     <i class="fas fa-map-marker text-blue-600 text-4xl drop-shadow-md"></i>
                     <div class="absolute top-[6px] left-[9px] w-3 h-3 bg-white rounded-full"></div>
                   </div>`,
            iconSize: [32, 40], iconAnchor: [16, 40], popupAnchor: [0, -35]
        });

        if(dataPekerja.length > 0) {
            dataPekerja.forEach(function(lap) {
                if(lap.lokasi_gps) {
                    let koor = lap.lokasi_gps.split(',');
                    if(koor.length === 2) {
                        let lat = parseFloat(koor[0]);
                        let lng = parseFloat(koor[1]);
                        let marker = L.marker([lat, lng], {icon: iconPetugas}).addTo(mapLokasi);

                        let namaPekerja = lap.pekerja ? lap.pekerja.nama_lengkap : 'Petugas UPTD';
                        marker.bindPopup(`
                            <div class="text-center p-1 w-48">
                                <div class="w-10 h-10 mx-auto rounded-full bg-blue-100 text-pupr-blue font-bold flex items-center justify-center mb-2">${namaPekerja.substring(0,1)}</div>
                                <p class="text-xs font-bold text-gray-800 mb-1">${namaPekerja}</p>
                                <p class="text-[10px] text-gray-500 bg-gray-50 rounded py-1 px-2 mb-2 border border-gray-100">ID: #${lap.id_laporan}</p>
                                <a href="/admin-bidang/laporan/detail/${lap.id}" class="text-[10px] bg-pupr-blue text-white py-1.5 px-3 rounded-lg font-bold block hover:bg-blue-800 transition shadow-sm">Lihat Tugas</a>
                            </div>
                        `);

                        markerCollection[lap.id_laporan] = marker;
                    }
                }
            });
        }
    });

    function fokusKePeta(koordinat, idLaporan) {
        let koor = koordinat.split(',');
        if(koor.length === 2) {
            let lat = parseFloat(koor[0]);
            let lng = parseFloat(koor[1]);

            mapLokasi.flyTo([lat, lng], 16, { duration: 1.5 });

            setTimeout(() => {
                if(markerCollection[idLaporan]) markerCollection[idLaporan].openPopup();
            }, 1500);

            document.querySelectorAll('[id^="menu-opsi-"]').forEach(el => el.classList.add('hidden'));
        }
    }

    function toggleFullscreenMap() {
        let el = document.getElementById('mapLokasi');
        if (!document.fullscreenElement) el.requestFullscreen().catch(err => { alert(`Error: ${err.message}`); });
        else document.exitFullscreen();
    }

    document.addEventListener('fullscreenchange', (e) => {
        let icon = document.getElementById('icon-fs-map');
        let teks = document.getElementById('teks-fs-map');
        let container = document.getElementById('mapLokasi');

        if (document.fullscreenElement) {
            icon.classList.replace('fa-expand', 'fa-compress');
            teks.innerText = "Kecilkan Peta";
            container.classList.remove('h-64', 'rounded-xl');
            container.style.height = '100vh';
        } else {
            icon.classList.replace('fa-compress', 'fa-expand');
            teks.innerText = "Perbesar Peta";
            container.classList.add('h-64', 'rounded-xl');
            container.style.height = '';
        }
        setTimeout(() => { mapLokasi.invalidateSize(); }, 300);
    });
</script>
@endpush
