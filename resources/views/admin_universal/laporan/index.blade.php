@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    /* Kustomisasi Popup Leaflet agar Modern */
    .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.15); border: 1px solid #e2e8f0; padding: 0; overflow: hidden;}
    .leaflet-popup-content { margin: 0; width: 280px !important; }
    .leaflet-container a.leaflet-popup-close-button { color: #94a3b8; top: 8px; right: 8px; font-size: 16px;}
    .leaflet-container a.leaflet-popup-close-button:hover { color: #ef4444; }

    /* Scrollbar untuk Aktivitas Terbaru */
    .activity-scroll::-webkit-scrollbar { width: 4px; }
    .activity-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

    /* Animasi Halus Berurutan (Cascade Entry) */
    @keyframes fadeUpMasuk {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animasi-masuk {
        animation: fadeUpMasuk 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }
    .jeda-1 { animation-delay: 0.1s; }
    .jeda-2 { animation-delay: 0.2s; }
    .jeda-3 { animation-delay: 0.3s; }
    .jeda-4 { animation-delay: 0.4s; }
    .jeda-5 { animation-delay: 0.5s; }
    .jeda-6 { animation-delay: 0.6s; }

    /* Custom Input Date Styling */
    input[type="date"]::-webkit-calendar-picker-indicator {
        background: transparent; bottom: 0; color: transparent; cursor: pointer; height: auto; left: 0; position: absolute; right: 0; top: 0; width: auto;
    }
</style>
@endpush

@section('konten')

<div class="max-w-7xl mx-auto pb-10">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 animasi-masuk">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Kelola Laporan Masuk</h2>
            <p class="text-sm text-gray-500 font-medium mt-1">Pantau, validasi, dan disposisikan laporan infrastruktur di seluruh wilayah melalui sistem SIGAP.</p>
        </div>
        <div class="flex space-x-2 shrink-0">
            <a href="{{ route('admin_universal.laporan.ekspor') ?? '#' }}" class="bg-white border border-gray-200 text-green-700 hover:bg-green-50 px-3 py-2.5 rounded-lg text-sm font-bold shadow-sm transition flex items-center hover:-translate-y-0.5 transform">
                <i class="fas fa-file-excel mr-2"></i> Ekspor CSV
            </a>
            <a href="{{ route('admin_universal.laporan.ekspor_pdf') ?? '#' }}" target="_blank" class="bg-white border border-gray-200 text-red-600 hover:bg-red-50 px-3 py-2.5 rounded-lg text-sm font-bold shadow-sm transition flex items-center hover:-translate-y-0.5 transform">
                <i class="fas fa-file-pdf mr-2"></i> Ekspor PDF
            </a>
            <button onclick="toggleModalLaporan()" class="bg-yellow-400 hover:bg-yellow-500 text-white px-4 py-2.5 rounded-lg text-sm font-bold shadow-sm transition flex items-center hover:-translate-y-0.5 transform">
                <i class="fas fa-plus mr-2"></i> Laporan Baru
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 relative z-10">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start space-x-4 animasi-masuk jeda-1 group hover:border-blue-200 transition duration-300">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Total Laporan</p>
                <h4 class="text-3xl font-extrabold text-gray-800 leading-none mb-1.5">{{ number_format($statistik['total'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded-full inline-block">+12% Bulan Ini</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start space-x-4 animasi-masuk jeda-2 group hover:border-indigo-200 transition duration-300">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="fas fa-tools"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Dalam Pengerjaan</p>
                <h4 class="text-3xl font-extrabold text-gray-800 leading-none mb-1.5">{{ number_format($statistik['proses'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded-full inline-block">SDA & Bina Marga</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start space-x-4 animasi-masuk jeda-3 group hover:border-green-200 transition duration-300">
            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Tuntas Diselesaikan</p>
                <h4 class="text-3xl font-extrabold text-gray-800 leading-none mb-1.5">{{ number_format($statistik['selesai'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-green-500 font-bold bg-green-50 px-2 py-0.5 rounded-full inline-block">98.2% Kepuasan</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start space-x-4 animasi-masuk jeda-4 group hover:border-red-200 transition duration-300">
            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="fas fa-ban"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Laporan Ditolak</p>
                <h4 class="text-3xl font-extrabold text-gray-800 leading-none mb-1.5">{{ number_format($statistik['ditolak'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-red-500 font-bold bg-red-50 px-2 py-0.5 rounded-full inline-block">Duplikasi Data</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-8 animasi-masuk jeda-5 overflow-visible relative z-20">

        <form action="{{ route('admin_universal.laporan') }}" method="GET" id="form-filter" class="p-5 border-b border-gray-100 bg-gray-50/50 rounded-t-2xl">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

                <div class="md:col-span-3">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Filter Status Laporan</label>
                    <div class="relative custom-dropdown w-full" data-name="status">
                        <select name="status" class="hidden">
                            <option value="Semua Status" {{ request('status') == 'Semua Status' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Validasi</option>
                            <option value="diteruskan" {{ request('status') == 'diteruskan' ? 'selected' : '' }}>Menunggu Penugasan</option>
                            <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Sedang Proses</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        <div class="dropdown-btn flex items-center justify-between bg-white border border-gray-200 rounded-lg pl-4 pr-3 py-2.5 text-sm text-gray-700 font-bold cursor-pointer hover:border-pupr-blue hover:bg-blue-50 transition shadow-sm w-full">
                            <span class="dropdown-text">Semua Status</span>
                            <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                        </div>
                        <ul class="dropdown-menu hidden absolute z-[6000] w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] overflow-hidden transform scale-95 opacity-0 transition-all duration-200 origin-top">
                            <li data-value="Semua Status" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition flex items-center justify-between {{ request('status') == 'Semua Status' || !request('status') ? 'bg-blue-50 text-pupr-blue' : '' }}">
                                Semua Status {!! request('status') == 'Semua Status' || !request('status') ? '<i class="fas fa-check text-pupr-blue"></i>' : '' !!}
                            </li>
                            <li data-value="pending" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition flex items-center justify-between border-t border-gray-50 {{ request('status') == 'pending' ? 'bg-blue-50 text-pupr-blue' : '' }}">
                                Menunggu Validasi {!! request('status') == 'pending' ? '<i class="fas fa-check text-pupr-blue"></i>' : '' !!}
                            </li>
                            <li data-value="diteruskan" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition flex items-center justify-between border-t border-gray-50 {{ request('status') == 'diteruskan' ? 'bg-blue-50 text-pupr-blue' : '' }}">
                                Menunggu Penugasan {!! request('status') == 'diteruskan' ? '<i class="fas fa-check text-pupr-blue"></i>' : '' !!}
                            </li>
                            <li data-value="proses" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition flex items-center justify-between border-t border-gray-50 {{ request('status') == 'proses' ? 'bg-blue-50 text-pupr-blue' : '' }}">
                                Sedang Proses {!! request('status') == 'proses' ? '<i class="fas fa-check text-pupr-blue"></i>' : '' !!}
                            </li>
                            <li data-value="selesai" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition flex items-center justify-between border-t border-gray-50 {{ request('status') == 'selesai' ? 'bg-blue-50 text-pupr-blue' : '' }}">
                                Selesai {!! request('status') == 'selesai' ? '<i class="fas fa-check text-pupr-blue"></i>' : '' !!}
                            </li>
                            <li data-value="ditolak" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition flex items-center justify-between border-t border-gray-50 {{ request('status') == 'ditolak' ? 'bg-blue-50 text-pupr-blue' : '' }}">
                                Ditolak {!! request('status') == 'ditolak' ? '<i class="fas fa-check text-pupr-blue"></i>' : '' !!}
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Kategori Bidang</label>
                    <div class="relative custom-dropdown w-full" data-name="bidang">
                        <select name="bidang" class="hidden">
                            <option value="Semua Bidang" {{ request('bidang') == 'Semua Bidang' || !request('bidang') ? 'selected' : '' }}>Semua Bidang</option>
                            @foreach($daftar_bidang ?? \App\Models\Bidang::pluck('nama_bidang') as $b)
                                <option value="{{ $b }}" {{ request('bidang') == $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                        <div class="dropdown-btn flex items-center justify-between bg-white border border-gray-200 rounded-lg pl-4 pr-3 py-2.5 text-sm text-gray-700 font-bold cursor-pointer hover:border-pupr-blue hover:bg-blue-50 transition shadow-sm w-full">
                            <span class="dropdown-text truncate">Semua Bidang</span>
                            <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300 ml-2"></i>
                        </div>
                        <ul class="dropdown-menu hidden absolute z-[6000] w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] max-h-56 overflow-y-auto custom-scrollbar transform scale-95 opacity-0 transition-all duration-200 origin-top">
                            <li data-value="Semua Bidang" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition flex items-center justify-between {{ request('bidang') == 'Semua Bidang' || !request('bidang') ? 'bg-blue-50 text-pupr-blue' : '' }}">
                                Semua Bidang {!! request('bidang') == 'Semua Bidang' || !request('bidang') ? '<i class="fas fa-check text-pupr-blue"></i>' : '' !!}
                            </li>
                            @foreach($daftar_bidang ?? \App\Models\Bidang::pluck('nama_bidang') as $b)
                                <li data-value="{{ $b }}" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition flex items-center justify-between border-t border-gray-50 {{ request('bidang') == $b ? 'bg-blue-50 text-pupr-blue' : '' }}">
                                    {{ $b }} {!! request('bidang') == $b ? '<i class="fas fa-check text-pupr-blue"></i>' : '' !!}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="md:col-span-3 relative">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Rentang Tanggal Masuk</label>
                    <div class="relative w-full">
                        <i class="far fa-calendar-alt absolute left-3 top-3.5 text-gray-400 pointer-events-none text-xs"></i>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}" onchange="document.getElementById('form-filter').submit()" class="w-full bg-white border border-gray-200 rounded-lg pl-9 pr-3 py-2.5 text-sm outline-none focus:border-pupr-blue focus:ring-1 focus:ring-pupr-blue text-gray-700 font-bold shadow-sm transition hover:border-pupr-blue cursor-pointer relative z-10">
                        @if(request('tanggal'))
                            <a href="{{ route('admin_universal.laporan', request()->except('tanggal')) }}" class="absolute right-3 top-3 z-20 text-gray-300 hover:text-red-500 transition" title="Hapus Filter Tanggal"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </div>

                <div class="md:col-span-3 flex justify-end space-x-2 relative z-10">
                    <button type="submit" class="flex-1 h-11 bg-pupr-blue text-white hover:bg-blue-800 font-bold text-sm rounded-lg shadow flex items-center justify-center transition focus:outline-none hover:shadow-lg transform hover:-translate-y-0.5">
                        <i class="fas fa-filter mr-2"></i> Terapkan
                    </button>
                    @if(request()->has('status') || request()->has('bidang') || request()->has('tanggal'))
                        <a href="{{ route('admin_universal.laporan') }}" class="w-11 h-11 bg-white hover:bg-gray-100 text-gray-400 hover:text-gray-600 border border-gray-200 rounded-lg shadow flex items-center justify-center transition focus:outline-none" title="Reset Semua Filter">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <div class="overflow-x-auto relative z-10">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-gray-100 text-gray-400 text-[11px] uppercase font-bold tracking-wider bg-white">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">ID Laporan</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Bidang / Kategori</th>
                        <th class="px-6 py-4">Pelapor</th>
                        <th class="px-6 py-4">Lokasi</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm font-medium text-gray-700 bg-white">
                    @forelse($semua_laporan ?? [] as $index => $item)
                    <tr class="hover:bg-blue-50/30 transition duration-150">
                        <td class="px-6 py-5 text-gray-500">{{ $semua_laporan->firstItem() + $index }}</td>
                        <td class="px-6 py-5 font-bold text-pupr-blue hover:underline cursor-pointer" onclick="window.location.href='{{ route('admin_universal.laporan.detail', $item->id) }}'">#{{ $item->id_laporan }}</td>
                        <td class="px-6 py-5 text-gray-500">
                            {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M') }}<br>
                            <span class="text-xs">{{ \Carbon\Carbon::parse($item->created_at)->format('Y') }}</span>
                        </td>
                        <td class="px-6 py-5">
                            @if(strtolower($item->status) === 'pending')
                                <span class="px-3 py-1 border border-gray-200 text-gray-400 bg-gray-50 rounded-full text-[10px] font-bold italic flex items-center w-max">
                                    <i class="fas fa-question-circle mr-1"></i> Menunggu Validasi
                                </span>
                            @else
                                <span class="px-3 py-1 border border-blue-200 text-blue-600 bg-blue-50 rounded-full text-[10px] font-bold uppercase tracking-wider inline-block">
                                    {{ $item->kategori_bidang }}
                                </span>
                            @endif
                        </td>
                        <!-- ========================================== -->
                        <td class="px-6 py-5">
                            <div class="flex items-center space-x-3">
                                @if(isset($item->pelapor->foto_profil) && $item->pelapor->foto_profil)
                                    <img src="{{ asset('storage/' . $item->pelapor->foto_profil) }}" alt="Profil" class="w-9 h-9 rounded-full object-cover shrink-0 border border-gray-200 shadow-sm">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs font-bold shrink-0 border border-gray-200 shadow-sm uppercase">
                                        {{ substr($item->pelapor->nama_lengkap ?? 'A', 0, 1) }}
                                    </div>
                                @endif

                                <span class="font-bold text-gray-800 leading-tight">
                                    {{ explode(' ', $item->pelapor->nama_lengkap ?? 'Anonim')[0] }}<br>
                                    <span class="font-normal text-gray-500 text-xs">{{ count(explode(' ', $item->pelapor->nama_lengkap ?? '')) > 1 ? explode(' ', $item->pelapor->nama_lengkap)[1] : '' }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-gray-600 leading-tight">
                            <p class="font-bold text-gray-800">{{ explode(',', $item->alamat_map)[0] ?? 'Lokasi' }}</p>
                            <p class="text-[10px] text-gray-400 truncate w-32">{{ Str::limit(implode(',', array_slice(explode(',', $item->alamat_map), 1)), 25) }}</p>
                        </td>
                        <td class="px-6 py-5">
                            @if($item->status == 'selesai')
                                <div class="flex items-center text-green-700 bg-green-50 px-2.5 py-1.5 rounded-full w-max text-[10px] font-bold border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Selesai
                                </div>
                            @elseif($item->status == 'proses')
                                <div class="flex items-center text-indigo-700 bg-indigo-50 px-2.5 py-1.5 rounded-full w-max text-[10px] font-bold border border-indigo-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-1.5 animate-pulse"></span> Pengerjaan Bidang
                                </div>
                            @elseif($item->status == 'diteruskan')
                                <div class="flex items-center text-yellow-700 bg-yellow-50 px-2.5 py-1.5 rounded-full w-max text-[10px] font-bold border border-yellow-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span> Menunggu Penugasan
                                </div>
                            @elseif($item->status == 'ditolak')
                                <div class="flex items-center text-red-700 bg-red-50 px-2.5 py-1.5 rounded-full w-max text-[10px] font-bold border border-red-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Ditolak
                                </div>
                            @else
                                <div class="flex items-center text-gray-600 bg-gray-100 px-2.5 py-1.5 rounded-full w-max text-[10px] font-bold border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5 animate-pulse opacity-75"></span> Menunggu Validasi
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-center">
                            <a href="{{ route('admin_universal.laporan.detail', $item->id) }}" class="bg-gray-50 text-gray-500 hover:bg-pupr-blue/10 hover:text-pupr-blue px-3 py-2 rounded-lg transition" title="Lihat Detail & Disposisi Laporan">
                                <i class="fas fa-share-square text-lg"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-gray-400 font-medium bg-gray-50/20">
                            <i class="fas fa-inbox text-5xl mb-4 text-gray-200 block"></i>
                            Tidak ada data laporan yang ditemukan dalam kriteria filter Anda.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-5 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between text-sm text-gray-500 gap-4 bg-white rounded-b-2xl relative z-10">
            @if(isset($semua_laporan) && $semua_laporan->total() > 0)
                <div>
                    Menampilkan <span class="font-bold text-gray-800">{{ $semua_laporan->firstItem() }}-{{ $semua_laporan->lastItem() }}</span> dari <span class="font-bold text-gray-800">{{ number_format($semua_laporan->total(), 0, ',', '.') }}</span> laporan masuk.
                </div>
                {{ $semua_laporan->links('pagination::tailwind') }}
            @else
                <div class="w-full text-center">Menampilkan <span class="font-bold text-gray-800">0</span> laporan</div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-10 animasi-masuk jeda-6 relative z-10">

        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col relative overflow-hidden group hover:border-blue-200 transition duration-300">
            <div class="absolute -right-4 -top-4 opacity-[0.02] pointer-events-none group-hover:scale-110 transition duration-700">
                <i class="fas fa-map-marked-alt text-9xl"></i>
            </div>
            <div class="flex justify-between items-center mb-4 px-1 relative z-10">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <i class="fas fa-map-marked-alt text-blue-600 mr-2 text-xl animate-pulse"></i> Visualisasi Lokasi Laporan Terbaru
                </h3>
                <a href="{{ route('admin_universal.peta') ?? '#' }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Peta Lengkap</a>
            </div>

            <div class="relative w-full h-[350px] rounded-xl overflow-hidden border border-gray-200 z-10 shadow-inner bg-gray-50">
                <div id="mapDashboard" class="w-full h-full absolute inset-0 z-0"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col h-[450px]">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-800 flex items-center text-sm">
                    <i class="fas fa-history text-blue-600 mr-2"></i> Log Aktivitas Terbaru
                </h3>
                <button onclick="bukaModalLogLaporan()" class="text-xs font-bold text-blue-600 hover:underline focus:outline-none">
                    Lihat Semua
                </button>
            </div>

            <div class="flex-1 overflow-y-auto activity-scroll pr-2 space-y-6">
                @forelse($aktivitas_terbaru ?? [] as $log)
                    <div class="relative pl-6 border-l-2 border-blue-100 hover:bg-gray-50/50 p-2 rounded-r-lg transition -ml-2">
                        <div class="absolute -left-[7px] top-3 w-3 h-3 rounded-full bg-blue-500 ring-4 ring-white shadow-sm transition group-hover:scale-110"></div>
                        <p class="text-[10px] font-bold text-gray-400 mb-1 flex items-center"><i class="far fa-clock mr-1 text-[9px]"></i>{{ $log->created_at->diffForHumans() }}</p>
                        <p class="text-sm font-bold text-gray-800 mb-0.5">{{ $log->aktivitas }}</p>
                        <p class="text-[11px] text-gray-500 px-1.5 py-0.5 bg-gray-100 rounded w-max">{{ $log->kategori ?? 'Laporan' }}</p>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <i class="fas fa-history text-gray-200 text-5xl mb-3 block"></i>
                        <p class="text-sm text-gray-400 font-medium">Belum ada aktivitas tercatat hari ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div id="modal-log-laporan" class="fixed inset-0 z-[4000] hidden items-center justify-center transition-opacity duration-300">
    <div class="absolute inset-0 bg-gray-900/70 backdrop-blur-sm" onclick="tutupModalLogLaporan()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden flex flex-col max-h-[85vh] animasi-masuk transform transition-all duration-300 scale-100">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 relative z-10">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Seluruh Riwayat Aktivitas Laporan</h3>
                <p class="text-xs text-gray-500">Mencatat jejak rekam validasi, disposisi, dan penyelesaian laporan.</p>
            </div>
            <button onclick="tutupModalLogLaporan()" class="text-gray-400 hover:text-red-500 transition text-2xl focus:outline-none p-1">×</button>
        </div>

        <div class="p-6 overflow-y-auto flex-1 custom-scrollbar relative z-0">
            <ul class="space-y-4 border-l-2 border-blue-100 ml-3 py-2 relative">
                @forelse($semua_aktivitas ?? [] as $log)
                <li class="relative pl-6 hover:bg-blue-50/50 p-3 rounded-r-lg transition duration-300 flex justify-between items-start group">
                    <div>
                        <span class="absolute -left-[11px] top-4 w-5 h-5 rounded-full bg-pupr-blue border-4 border-white shadow-sm transition group-hover:scale-110"></span>
                        <p class="text-sm font-bold text-gray-800">{{ $log->aktivitas }}</p>
                        <p class="text-[10px] font-semibold text-gray-500 mt-1 uppercase tracking-wider"><i class="far fa-clock mr-1"></i> {{ $log->created_at->format('d M Y - H:i') }} WIB <span class="ml-2 px-1.5 py-0.5 bg-blue-100 text-blue-600 rounded text-[9px]">{{ $log->kategori ?? 'Laporan' }}</span></p>
                    </div>

                    <form action="{{ Route::has('admin_universal.laporan.log.hapus_satu') ? route('admin_universal.laporan.log.hapus_satu', $log->id) : '#' }}" method="POST" class="opacity-0 group-hover:opacity-100 transition duration-300 shrink-0 ml-4">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="konfirmasiHapusSatuLog(this)" class="w-8 h-8 rounded text-gray-300 hover:bg-red-100 hover:text-red-600 flex items-center justify-center transition focus:outline-none" title="Hapus catatan ini">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                </li>
                @empty
                <div class="text-center py-10">
                    <i class="fas fa-history text-gray-200 text-4xl mb-3 block"></i>
                    <p class="text-sm text-gray-500 mt-3">Riwayat aktivitas bersih.</p>
                </div>
                @endforelse
            </ul>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-3 relative z-10">
            <p class="text-xs text-gray-500"><i class="fas fa-info-circle mr-1 text-blue-500"></i> Data log yang dihapus akan hilang permanen.</p>

            <form id="form-hapus-semua-log-laporan" action="{{ Route::has('admin_universal.laporan.log.hapus') ? route('admin_universal.laporan.log.hapus') : '#' }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" onclick="konfirmasiHapusSemuaLogLaporan()" class="px-4 py-2.5 bg-red-50 text-red-600 hover:bg-red-500 hover:text-white border border-red-100 hover:border-red-500 rounded-lg text-xs font-bold transition shadow-sm flex items-center transform hover:-translate-y-0.5 hover:shadow-md focus:outline-none w-full md:w-auto justify-center">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus Seluruh Riwayat
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dropdowns = document.querySelectorAll('.custom-dropdown');

        dropdowns.forEach(dropdown => {
            const select = dropdown.querySelector('select');
            const btn = dropdown.querySelector('.dropdown-btn');
            const text = dropdown.querySelector('.dropdown-text');
            const menu = dropdown.querySelector('.dropdown-menu');
            const icon = btn.querySelector('.fa-chevron-down');
            const listItems = menu.querySelectorAll('li');

            const activeOpt = select.options[select.selectedIndex];
            if(activeOpt) text.innerText = activeOpt.text;

            btn.addEventListener('click', (e) => {
                e.stopPropagation();
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

            listItems.forEach(item => {
                item.addEventListener('click', () => {
                    if(select.value !== item.getAttribute('data-value')) {
                        select.value = item.getAttribute('data-value');
                        text.innerText = item.innerText;
                        closeMenu();
                        document.getElementById('form-filter').submit();
                    } else {
                        closeMenu();
                    }
                });
            });

            function closeMenu() {
                menu.classList.add('opacity-0', 'scale-95');
                icon.classList.remove('rotate-180');
                setTimeout(() => { menu.classList.add('hidden'); }, 200);
            }
        });

        document.addEventListener('click', () => {
            document.querySelectorAll('.custom-dropdown .dropdown-menu').forEach(menu => {
                menu.classList.add('opacity-0', 'scale-95');
                menu.previousElementSibling.querySelector('.fa-chevron-down').classList.remove('rotate-180');
                setTimeout(() => { menu.classList.add('hidden'); }, 200);
            });
        });
    });

    function bukaModalLogLaporan() {
        let modal = document.getElementById('modal-log-laporan');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function tutupModalLogLaporan() {
        let modal = document.getElementById('modal-log-laporan');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function konfirmasiHapusSemuaLogLaporan() {
        tutupModalLogLaporan();
        Swal.fire({
            title: 'Hapus Seluruh Riwayat?',
            text: "Apakah Anda yakin ingin MENGHAPUS SELURUH riwayat aktivitas laporan dari database? Tindakan ini permanen.",
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Hapus Permanen!', cancelButtonText: 'Batal', reverseButtons: true,
            customClass: { popup: 'rounded-2xl shadow-xl', title: 'font-bold', confirmButton: 'rounded-lg text-sm font-bold', cancelButton: 'rounded-lg text-sm font-bold' }
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('form-hapus-semua-log-laporan').submit();
            else bukaModalLogLaporan();
        });
    }

    function konfirmasiHapusSatuLog(btn) {
        tutupModalLogLaporan();
        Swal.fire({
            title: 'Hapus Catatan Ini?',
            text: "Catatan aktivitas ini akan dihapus permanen.",
            icon: 'question', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal', reverseButtons: true,
            customClass: { popup: 'rounded-2xl shadow-xl', title: 'font-bold', confirmButton: 'rounded-lg text-sm font-bold', cancelButton: 'rounded-lg text-sm font-bold' }
        }).then((result) => {
            if (result.isConfirmed) btn.closest('form').submit();
            else bukaModalLogLaporan();
        });
    }

    window.toggleDeskripsi = function(btn) {
        let parent = btn.parentElement;
        let shortSpan = parent.querySelector('.deskripsi-short');
        let fullSpan = parent.querySelector('.deskripsi-full');

        if (fullSpan.classList.contains('hidden')) {
            shortSpan.classList.add('hidden');
            fullSpan.classList.remove('hidden');
            btn.innerText = 'Sembunyikan';
            btn.className = 'text-gray-400 hover:text-gray-600 font-bold ml-1 transition hover:bg-gray-200 p-0.5 rounded mt-1 block';
        } else {
            fullSpan.classList.add('hidden');
            shortSpan.classList.remove('hidden');
            btn.innerText = 'selengkapnya';
            btn.className = 'text-blue-500 hover:text-blue-700 font-bold ml-1 transition';
        }
    }

    @if(session('sukses'))
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{!! session('sukses') !!}",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-2xl shadow-xl border border-gray-100' }
                });
            }, 500);
        });
    @endif

    let map, layerStandar, markerGroup;
    let dataLaporan = @json($sebaran_laporan ?? []);

    document.addEventListener("DOMContentLoaded", function() {

        // Memastikan Leaflet diinisialisasi setelah container punya tinggi pasti
        setTimeout(() => {
            console.log('[SIGAP-laporan] init map start');
            map = L.map('mapDashboard', {zoomControl: false}).setView([-6.5627, 107.7613], 12);
            L.control.zoom({ position: 'bottomright' }).addTo(map);

            layerStandar = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' });
            layerStandar.addTo(map);

            markerGroup = L.layerGroup().addTo(map);
            renderMarkers();
            console.log('[SIGAP-laporan] markers data length:', Array.isArray(dataLaporan) ? dataLaporan.length : 0);

            // Memaksa Leaflet mengecek ulang ukuran container agar tidak sepotong (Fragmented Fix)
            map.invalidateSize();
        }, 1000); // Tunggu sampai animasi CSS selesai sepenuhnya
    });

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        const entities = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
        return String(text).replace(/[&<>"']/g, function(m) { return entities[m]; });
    }

    function renderMarkers() {
        markerGroup.clearLayers();

        if(dataLaporan.length > 0) {
            dataLaporan.forEach(function(laporan) {
                if(laporan.lokasi_gps) {
                    let koordinat = laporan.lokasi_gps.split(',');
                    if(koordinat.length == 2) {
                        let lat = parseFloat(koordinat[0].trim());
                        let lng = parseFloat(koordinat[1].trim());

                        // Logika Warna Berdasarkan Legenda:
                        let warnaIkon = 'text-red-500';
                        let warnaBadge = 'bg-red-50 text-red-700';
                        let teksStatus = 'VALIDASI';

                        if(laporan.status === 'pending') {
                            warnaIkon = 'text-red-500';
                            warnaBadge = 'bg-red-50 text-red-700';
                            teksStatus = 'VALIDASI';
                        }
                        else if(laporan.status === 'diteruskan' || laporan.status === 'proses') {
                            warnaIkon = 'text-yellow-500';
                            warnaBadge = 'bg-yellow-50 text-yellow-700';
                            teksStatus = 'PENUGASAN/PROSES';
                        }
                        else if(laporan.status === 'selesai') {
                            warnaIkon = 'text-green-500';
                            warnaBadge = 'bg-green-50 text-green-700';
                            teksStatus = 'SELESAI';
                        }
                        else if(laporan.status === 'ditolak') {
                            warnaIkon = 'text-red-500';
                            warnaBadge = 'bg-red-50 text-red-700';
                            teksStatus = 'DITOLAK';
                        }

                                                let ikonCustom = L.divIcon({
                                                        className: 'bg-transparent',
                                                        html: `<div style="text-shadow: 2px 2px 4px rgba(0,0,0,0.4); font-size:30px; line-height:1; display:inline-block;" class="${warnaIkon} hover:scale-125 transition-transform cursor-pointer drop-shadow">
                                                                         <i class="fas fa-map-marker-alt"></i>
                                                                     </div>`,
                                                        iconSize: [30, 42], iconAnchor: [15, 40], popupAnchor: [0, -35]
                                                });

                        let namaPelapor = (laporan.pelapor && laporan.pelapor.nama_lengkap) ? escapeHtml(laporan.pelapor.nama_lengkap) : 'Masyarakat Subang';
                        let kategori = (laporan.status === 'pending' || !laporan.kategori_bidang) ? 'Menunggu Disposisi' : escapeHtml(laporan.kategori_bidang);

                        let deskripsiAsli = laporan.deskripsi_laporan || 'Laporan infrastruktur dari masyarakat.';
                        let batasKarakter = 70;

                        let teksAman = escapeHtml(deskripsiAsli);
                        let teksSingkatAman = teksAman.length > batasKarakter ? escapeHtml(deskripsiAsli.substring(0, batasKarakter)) + '...' : teksAman;

                        let btnSelengkapnya = teksAman.length > batasKarakter
                            ? `<button type="button" onclick="event.stopPropagation(); window.toggleDeskripsi(this)" class="text-blue-500 hover:text-blue-700 font-bold ml-1 transition">selengkapnya</button>`
                            : '';

                        let urlMaps = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
                        let urlDetail = `/admin-universal/laporan/detail/${laporan.id}`;

                        let marker = L.marker([lat, lng], {icon: ikonCustom});

                        marker.bindPopup(`
                            <div class="flex flex-col items-center">
                                <div class="px-4 pt-4 pb-2 text-center w-full">
                                    <p class="text-[10px] font-bold text-gray-400 mb-0.5 tracking-wider">ID: #${laporan.id_laporan}</p>
                                    <h3 class="font-extrabold text-gray-950 text-base leading-tight uppercase mb-1.5">${kategori}</h3>

                                    <div class="${warnaBadge} px-3 py-1 text-[9px] rounded-full font-extrabold my-2 uppercase tracking-widest border border-gray-100 inline-block shadow-inner">
                                        STATUS: ${teksStatus}
                                    </div>
                                </div>

                                <div class="w-full text-left bg-gray-50/50 p-3.5 border-y border-gray-100 hover:bg-gray-50 transition duration-300">
                                    <div class="flex items-start justify-between mb-1">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wide mt-0.5 w-16 shrink-0">PELAPOR</p>
                                        <div class="text-[11px] font-medium leading-relaxed break-words relative w-full text-left ml-2 flex-1">
                                            <div class="flex items-center text-gray-800 font-bold mb-1">
                                                <i class="fas fa-user-circle text-gray-400 mr-1.5 text-sm"></i> ${namaPelapor}
                                            </div>
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
                                        <i class="fas fa-map-marked-alt mr-2 text-green-600"></i> Lihat Di Google Maps
                                    </a>
                                </div>
                            </div>
                        `, { maxWidth: 280, minWidth: 280 });

                        markerGroup.addLayer(marker);
                    }
                }
            });
        }
    }
</script>
