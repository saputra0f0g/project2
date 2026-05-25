@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    .leaflet-popup-content { margin: 12px; }
    .activity-scroll::-webkit-scrollbar { width: 4px; }
    .activity-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

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
        background: transparent;
        bottom: 0; color: transparent; cursor: pointer; height: auto; left: 0; position: absolute; right: 0; top: 0; width: auto;
    }
</style>
@endpush

@section('konten')
<div class="max-w-7xl mx-auto pb-10">

    <div class="flex justify-between items-end mb-8 animasi-masuk">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Kelola Laporan Masuk</h2>
            <p class="text-sm text-gray-500 font-medium mt-1">Pantau dan tindak lanjuti laporan infrastruktur di seluruh wilayah melalui sistem SIGAP.</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin_universal.laporan.ekspor') ?? '#' }}" class="bg-white border border-gray-200 text-green-700 hover:bg-green-50 px-3 py-2.5 rounded-lg text-sm font-bold shadow-sm transition flex items-center hover:-translate-y-0.5 transform">
                <i class="fas fa-file-excel mr-2"></i> Ekspor Laporan Ke Excel
            </a>
            <a href="{{ route('admin_universal.laporan.ekspor_pdf') ?? '#' }}" target="_blank" class="bg-white border border-gray-200 text-red-600 hover:bg-red-50 px-3 py-2.5 rounded-lg text-sm font-bold shadow-sm transition flex items-center hover:-translate-y-0.5 transform">
                <i class="fas fa-file-pdf mr-2"></i> Ekspor Laporan Ke PDF
            </a>
            <button onclick="toggleModalLaporan()" class="bg-yellow-400 hover:bg-yellow-500 text-white px-4 py-2.5 rounded-lg text-sm font-bold shadow-sm transition flex items-center hover:-translate-y-0.5 transform">
                <i class="fas fa-plus mr-2"></i> Laporan Baru
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-start space-x-4 animasi-masuk jeda-1 group hover:border-blue-200 transition">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Total Laporan</p>
                <h4 class="text-2xl font-bold text-gray-800 leading-none mb-2">{{ number_format($statistik['total'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-green-600 font-bold">+12% dari bulan lalu</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-start space-x-4 animasi-masuk jeda-2 group hover:border-yellow-200 transition">
            <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Sedang Proses</p>
                <h4 class="text-2xl font-bold text-gray-800 leading-none mb-2">{{ number_format($statistik['proses'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-yellow-600 font-bold">Prioritas Tinggi: 8</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-start space-x-4 animasi-masuk jeda-3 group hover:border-green-200 transition">
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Laporan Selesai</p>
                <h4 class="text-2xl font-bold text-gray-800 leading-none mb-2">{{ number_format($statistik['selesai'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-green-500 font-bold">98.2% Kepuasan Publik</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-start space-x-4 animasi-masuk jeda-4 group hover:border-red-200 transition">
            <div class="w-12 h-12 rounded-full bg-red-50 text-red-400 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="fas fa-ban"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Laporan Ditolak</p>
                <h4 class="text-2xl font-bold text-gray-800 leading-none mb-2">{{ number_format($statistik['ditolak'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-gray-400 font-bold">Duplikasi: 5</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-8 animasi-masuk jeda-5 overflow-visible">

        <form action="{{ route('admin_universal.laporan') }}" method="GET" id="form-filter" class="p-5 border-b border-gray-100 bg-gray-50/50 rounded-t-2xl">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

                <div class="md:col-span-3">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Filter Status</label>
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
                            <li data-value="Semua Status" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition">Semua Status</li>
                            <li data-value="pending" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition border-t border-gray-50">Menunggu Validasi</li>
                            <li data-value="diteruskan" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition border-t border-gray-50">Menunggu Penugasan</li>
                            <li data-value="proses" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition border-t border-gray-50">Sedang Proses</li>
                            <li data-value="selesai" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition border-t border-gray-50">Selesai</li>
                            <li data-value="ditolak" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition border-t border-gray-50">Ditolak</li>
                        </ul>
                    </div>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Bidang</label>
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
                            <li data-value="Semua Bidang" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition">Semua Bidang</li>
                            @foreach($daftar_bidang ?? \App\Models\Bidang::pluck('nama_bidang') as $b)
                                <li data-value="{{ $b }}" class="px-4 py-3 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue cursor-pointer transition border-t border-gray-50">{{ $b }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="md:col-span-3 relative">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Rentang Tanggal</label>
                    <div class="relative w-full">
                        <i class="far fa-calendar-alt absolute left-3 top-3 text-gray-400 pointer-events-none"></i>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}" onchange="document.getElementById('form-filter').submit()" class="w-full bg-white border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 text-sm outline-none focus:border-pupr-blue focus:ring-1 focus:ring-pupr-blue text-gray-700 font-bold shadow-sm transition hover:border-pupr-blue cursor-pointer">
                    </div>
                </div>

                <div class="md:col-span-3 flex justify-end space-x-2">
                    @if(request()->has('status') || request()->has('bidang') || request()->has('tanggal'))
                        <a href="{{ route('admin_universal.laporan') }}" class="w-11 h-11 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white border border-red-100 rounded-lg shadow-sm flex items-center justify-center transition focus:outline-none" title="Reset Filter">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    @endif
                    <button type="submit" class="flex-1 h-11 bg-pupr-blue text-white hover:bg-blue-800 font-bold text-sm rounded-lg shadow-sm flex items-center justify-center transition focus:outline-none hover:shadow-md transform hover:-translate-y-0.5">
                        <i class="fas fa-filter mr-2"></i> Terapkan Filter
                    </button>
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
                    <tr class="hover:bg-blue-50/30 transition">
                        <td class="px-6 py-5 text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-5 font-bold text-pupr-blue hover:underline cursor-pointer" onclick="window.location.href='{{ route('admin_universal.laporan.detail', $item->id) }}'">#{{ $item->id_laporan }}</td>
                        <td class="px-6 py-5 text-gray-500">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M') }}<br>
                            <span class="text-xs">{{ \Carbon\Carbon::parse($item->created_at)->format('Y') }}</span>
                        </td>
                        <td class="px-6 py-5">
                            @if(strtolower($item->status) === 'pending')
                                <span class="px-3 py-1 border border-gray-200 text-gray-400 bg-gray-50 rounded-full text-[10px] font-bold italic flex items-center w-max">
                                    <i class="fas fa-question-circle mr-1"></i> Belum Dipilih
                                </span>
                            @else
                                <span class="px-3 py-1 border border-blue-200 text-blue-600 bg-blue-50 rounded-full text-[10px] font-bold uppercase tracking-wider inline-block">
                                    {{ $item->kategori_bidang }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center space-x-3">
                                @if(isset($item->pelapor->foto_profil) && $item->pelapor->foto_profil)
                                    <img src="{{ asset('storage/' . $item->pelapor->foto_profil) }}" alt="Profil" class="w-8 h-8 rounded-full object-cover shrink-0 border border-gray-200 shadow-sm">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold shrink-0 border border-gray-200 shadow-sm">
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
                            {{ Str::limit($item->alamat_map, 25) }}
                        </td>
                        <td class="px-6 py-5">
                            @if($item->status == 'selesai')
                                <div class="flex items-center text-green-600 bg-green-50 px-2.5 py-1.5 rounded-full w-max text-[10px] font-bold border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Selesai
                                </div>
                            @elseif($item->status == 'proses')
                                <div class="flex items-center text-indigo-600 bg-indigo-50 px-2.5 py-1.5 rounded-full w-max text-[10px] font-bold border border-indigo-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-1.5"></span> Sedang Proses
                                </div>
                            @elseif($item->status == 'diteruskan')
                                <div class="flex items-center text-yellow-600 bg-yellow-50 px-2.5 py-1.5 rounded-full w-max text-[10px] font-bold border border-yellow-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5 animate-pulse"></span> Di Bidang
                                </div>
                            @elseif($item->status == 'ditolak')
                                <div class="flex items-center text-red-600 bg-red-50 px-2.5 py-1.5 rounded-full w-max text-[10px] font-bold border border-red-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Ditolak
                                </div>
                            @else
                                <div class="flex items-center text-gray-600 bg-gray-100 px-2.5 py-1.5 rounded-full w-max text-[10px] font-bold border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5 animate-ping opacity-75"></span> Menunggu
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-center">
                            <a href="{{ route('admin_universal.laporan.detail', $item->id) }}" class="text-gray-400 hover:text-pupr-blue hover:bg-blue-50 transition p-2 rounded-lg inline-block" title="Lihat Detail">
                                <i class="far fa-eye text-lg"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400 font-medium bg-gray-50/50">
                            <i class="fas fa-search text-3xl mb-3 text-gray-300 block"></i>
                            Tidak ada data laporan yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-5 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between text-sm text-gray-500 gap-4 bg-white rounded-b-2xl">
            @if(isset($semua_laporan) && $semua_laporan->total() > 0)
                <div>
                    Menampilkan <span class="font-bold text-gray-800">{{ $semua_laporan->firstItem() }}-{{ $semua_laporan->lastItem() }}</span> dari <span class="font-bold text-gray-800">{{ number_format($semua_laporan->total(), 0, ',', '.') }}</span> laporan
                </div>
                @if($semua_laporan->hasPages())
                <div class="flex space-x-1">
                    @if ($semua_laporan->onFirstPage())
                        <span class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed"><i class="fas fa-chevron-left text-xs"></i></span>
                    @else
                        <a href="{{ $semua_laporan->appends(request()->query())->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 hover:bg-gray-50 text-gray-600 transition shadow-sm"><i class="fas fa-chevron-left text-xs"></i></a>
                    @endif

                    @foreach ($semua_laporan->appends(request()->query())->links()->elements as $element)
                        @if (is_string($element))
                            <span class="w-8 h-8 flex items-center justify-center text-gray-400">{{ $element }}</span>
                        @endif
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $semua_laporan->currentPage())
                                    <span class="w-8 h-8 flex items-center justify-center rounded bg-yellow-400 text-white font-bold shadow-sm">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 hover:bg-gray-50 font-bold text-gray-600 transition shadow-sm">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($semua_laporan->hasMorePages())
                        <a href="{{ $semua_laporan->appends(request()->query())->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 hover:bg-gray-50 text-gray-600 transition shadow-sm"><i class="fas fa-chevron-right text-xs"></i></a>
                    @else
                        <span class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed"><i class="fas fa-chevron-right text-xs"></i></span>
                    @endif
                </div>
                @endif
            @else
                <div class="w-full text-center">Menampilkan <span class="font-bold text-gray-800">0</span> laporan</div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-10 animasi-masuk jeda-6">

        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col relative overflow-hidden group hover:border-blue-200 transition">
            <div class="absolute -right-4 -top-4 opacity-[0.03] pointer-events-none group-hover:scale-110 transition duration-700">
                <i class="fas fa-map-marked-alt text-9xl"></i>
            </div>
            <div class="flex justify-between items-center mb-4 px-1 relative z-10">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <i class="fas fa-map-marked-alt text-blue-600 mr-2"></i> Visualisasi Lokasi Laporan Terbaru
                </h3>
                <a href="{{ route('admin_universal.peta') ?? '#' }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Peta Lengkap</a>
            </div>
            <div class="relative w-full flex-1 min-h-[300px] rounded-xl overflow-hidden border border-gray-200 z-10">
                <div id="mapDashboard" class="w-full h-full bg-gray-100"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col h-[400px]">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-800 flex items-center text-sm">
                    <i class="fas fa-history text-blue-600 mr-2"></i> Aktivitas Terbaru
                </h3>
                <button onclick="bukaModalLogLaporan()" class="text-xs font-bold text-blue-600 hover:underline focus:outline-none">
                    Lihat Semua Riwayat
                </button>
            </div>

            <div class="flex-1 overflow-y-auto activity-scroll pr-2 space-y-6">
                @forelse($aktivitas_terbaru ?? [] as $log)
                    <div class="relative pl-6 border-l-2 border-blue-100 hover:bg-gray-50 p-2 rounded-r-lg transition -ml-2">
                        <div class="absolute -left-[7px] top-3 w-3 h-3 rounded-full bg-blue-500 ring-4 ring-white"></div>
                        <p class="text-[10px] font-bold text-gray-400 mb-1"><i class="far fa-clock mr-1"></i>{{ $log->created_at->diffForHumans() }}</p>
                        <p class="text-sm font-bold text-gray-800 mb-0.5">{{ $log->aktivitas }}</p>
                        <p class="text-[11px] text-gray-500">{{ $log->kategori ?? 'Laporan' }}</p>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <i class="fas fa-clipboard-check text-gray-200 text-4xl mb-3 block"></i>
                        <p class="text-sm text-gray-400 font-medium">Belum ada aktivitas tercatat.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div id="modal-log-laporan" class="fixed inset-0 z-[4000] hidden items-center justify-center">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="tutupModalLogLaporan()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden flex flex-col max-h-[85vh] animasi-masuk">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Seluruh Riwayat Aktivitas Laporan</h3>
                <p class="text-xs text-gray-500">Mencatat seluruh jejak rekam perubahan status laporan di dalam sistem.</p>
            </div>
            <button onclick="tutupModalLogLaporan()" class="text-gray-400 hover:text-red-500 transition text-2xl focus:outline-none">×</button>
        </div>

        <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
            <ul class="space-y-4 border-l-2 border-blue-100 ml-3 py-2">
                @forelse($semua_aktivitas ?? [] as $log)
                <li class="relative pl-6 hover:bg-blue-50/50 p-3 rounded-r-lg transition duration-300 flex justify-between items-start group">
                    <div>
                        <span class="absolute -left-[11px] top-4 w-5 h-5 rounded-full bg-pupr-blue border-4 border-white shadow-sm transition group-hover:scale-110"></span>
                        <p class="text-sm font-bold text-gray-800">{{ $log->aktivitas }}</p>
                        <p class="text-[10px] font-semibold text-gray-500 mt-1 uppercase tracking-wider"><i class="far fa-clock mr-1"></i> {{ $log->created_at->format('d M Y - H:i') }} WIB <span class="ml-2 px-1.5 py-0.5 bg-blue-100 text-blue-600 rounded">{{ $log->kategori ?? 'Laporan' }}</span></p>
                    </div>

                    <form action="{{ Route::has('admin_universal.laporan.log.hapus_satu') ? route('admin_universal.laporan.log.hapus_satu', $log->id) : '#' }}" method="POST" class="opacity-0 group-hover:opacity-100 transition duration-300">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="konfirmasiHapusSatuLog(this)" class="w-8 h-8 rounded text-gray-300 hover:bg-red-100 hover:text-red-600 flex items-center justify-center transition focus:outline-none" title="Hapus catatan ini">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                </li>
                @empty
                <p class="text-sm text-gray-500 ml-6"><i class="fas fa-history mr-2"></i> Riwayat aktivitas bersih.</p>
                @endforelse
            </ul>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
            <p class="text-xs text-gray-500"><i class="fas fa-info-circle mr-1 text-blue-500"></i> Data yang dihapus akan hilang dari database secara permanen.</p>

            <form id="form-hapus-semua-log-laporan" action="{{ Route::has('admin_universal.laporan.log.hapus') ? route('admin_universal.laporan.log.hapus') : '#' }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" onclick="konfirmasiHapusSemuaLogLaporan()" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-500 hover:text-white border border-red-100 hover:border-red-500 rounded-lg text-xs font-bold transition shadow-sm flex items-center transform hover:-translate-y-0.5 hover:shadow-md focus:outline-none">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus Seluruh Riwayat
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // --- SCRIPT CUSTOM SELECT DROPDOWN ---
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
                    select.value = item.getAttribute('data-value');
                    text.innerText = item.innerText;
                    closeMenu();

                    // Otomatis submit setelah pilih filter (Opsional, agar instan)
                    document.getElementById('form-filter').submit();
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

    // --- SCRIPT MODAL LOG AKTIVITAS ---
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

    // Fungsi Global Teks "Selengkapnya" Peta
    window.toggleDeskripsi = function(btn) {
        let parent = btn.parentElement;
        let isExpanded = btn.getAttribute('data-expanded') === 'true';
        let fullText = btn.getAttribute('data-full');
        let shortText = btn.getAttribute('data-short');

        if (isExpanded) {
            parent.innerHTML = `${shortText} <button type="button" onclick="event.stopPropagation(); window.toggleDeskripsi(this)" data-full="${fullText}" data-short="${shortText}" data-expanded="false" class="text-blue-500 hover:text-blue-700 font-bold ml-1 transition">selengkapnya</button>`;
        } else {
            parent.innerHTML = `${fullText} <button type="button" onclick="event.stopPropagation(); window.toggleDeskripsi(this)" data-full="${fullText}" data-short="${shortText}" data-expanded="true" class="text-gray-400 hover:text-gray-600 font-bold ml-1 transition">Sembunyikan</button>`;
        }
    }

    // PENTING: SWEETALERT NOTIFIKASI
    @if(session('sukses'))
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{!! session('sukses') !!}",
                timer: 3000,
                showConfirmButton: false,
                customClass: { popup: 'rounded-2xl shadow-xl border border-gray-100' }
            });
        });
    @endif
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    let map, layerStandar, markerGroup;
    let dataLaporan = @json($sebaran_laporan ?? []);

    document.addEventListener("DOMContentLoaded", function() {
        // ID sudah disamakan dengan ID div di HTML
        map = L.map('mapDashboard', {zoomControl: false}).setView([-6.5627, 107.7613], 12);
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        layerStandar = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 20 });
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

                        let warnaIkon = 'text-gray-400';
                        if(laporan.status === 'pending') warnaIkon = 'text-gray-400';
                        if(laporan.status === 'diteruskan' || laporan.status === 'proses') warnaIkon = 'text-yellow-500';
                        if(laporan.status === 'selesai') warnaIkon = 'text-green-500';
                        if(laporan.status === 'ditolak') warnaIkon = 'text-red-500';

                        let ikonCustom = L.divIcon({
                            className: 'bg-transparent',
                            html: `<div style="text-shadow: 2px 2px 4px rgba(0,0,0,0.4);" class="${warnaIkon} text-[38px] hover:scale-110 transition-transform cursor-pointer">
                                     <i class="fas fa-map-marker-alt"></i>
                                   </div>`,
                            iconSize: [30, 42], iconAnchor: [15, 40], popupAnchor: [0, -35]
                        });

                        // Ambil Data untuk Popup Peta Gaya Baru
                        let namaPelapor = (laporan.pelapor && laporan.pelapor.nama_lengkap) ? laporan.pelapor.nama_lengkap : 'Masyarakat Umum';
                        let kategori = (laporan.status === 'pending') ? 'Belum Ditentukan' : (laporan.kategori_bidang || 'Laporan');
                        let deskripsi = laporan.deskripsi_laporan || 'Tidak ada deskripsi.';

                        let teksAman = deskripsi.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                        let batasKarakter = 60;
                        let deskripsiSingkat = deskripsi.length > batasKarakter ? deskripsi.substring(0, batasKarakter) + '...' : deskripsi;
                        let teksSingkatAman = deskripsiSingkat.replace(/"/g, '&quot;').replace(/'/g, '&#39;');

                        let btnSelengkapnya = deskripsi.length > batasKarakter
                            ? `<button type="button" onclick="event.stopPropagation(); window.toggleDeskripsi(this)" data-full="${teksAman}" data-short="${teksSingkatAman}" data-expanded="false" class="text-blue-500 hover:text-blue-700 font-bold ml-1 transition">selengkapnya</button>`
                            : '';

                        let urlMaps = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
                        let urlDetail = `/admin-universal/laporan/detail/${laporan.id}`;

                        let marker = L.marker([lat, lng], {icon: ikonCustom});

                        marker.bindPopup(`
                            <div class="w-60 text-center flex flex-col items-center">
                                <p class="text-[10px] font-bold text-gray-400 mb-1 tracking-wider">ID: ${laporan.id_laporan}</p>
                                <h3 class="font-extrabold text-gray-900 text-base leading-tight uppercase">${kategori}</h3>

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
</script>
@endpush
