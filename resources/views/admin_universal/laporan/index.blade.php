@extends('layouts.app')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    .leaflet-popup-content { margin: 12px; }
    /* Kustomisasi scrollbar untuk Aktivitas Terbaru */
    .activity-scroll::-webkit-scrollbar { width: 4px; }
    .activity-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

    /* ========================================== */
    /* TAMBAHAN ANIMASI HALUS BERURUTAN (CASCADE) */
    /* ========================================== */
    @keyframes fadeUpMasuk {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animasi-masuk {
        animation: fadeUpMasuk 0.6s ease-out forwards;
        opacity: 0;
    }
    .jeda-1 { animation-delay: 0.1s; }
    .jeda-2 { animation-delay: 0.2s; }
    .jeda-3 { animation-delay: 0.3s; }
    .jeda-4 { animation-delay: 0.4s; }
    .jeda-5 { animation-delay: 0.5s; }
    .jeda-6 { animation-delay: 0.6s; }
</style>

@section('konten')
<div class="max-w-7xl mx-auto">

    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Kelola Laporan Masuk</h2>
            <p class="text-sm text-gray-500 font-medium mt-1">Pantau dan tindak lanjuti laporan infrastruktur di seluruh wilayah melalui sistem SIGAP.</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin_universal.laporan.ekspor') }}" class="bg-white border border-gray-200 text-green-700 hover:bg-green-50 px-3 py-2.5 rounded-lg text-sm font-bold shadow-sm transition flex items-center">
                <i class="fas fa-file-excel mr-2"></i> Ekspor Laporan Ke Excel
            </a>
            <a href="{{ route('admin_universal.laporan.ekspor_pdf') }}" class="bg-white border border-gray-200 text-red-600 hover:bg-red-50 px-3 py-2.5 rounded-lg text-sm font-bold shadow-sm transition flex items-center">
                <i class="fas fa-file-pdf mr-2"></i> Ekspor Laporan Ke PDF
            </a>
            <button onclick="toggleModalLaporan()" class="bg-yellow-400 hover:bg-yellow-500 text-white px-4 py-2.5 rounded-lg text-sm font-bold shadow-sm transition flex items-center">
                <i class="fas fa-plus mr-2"></i> Laporan Baru
            </button>
        </div>
    </div>

    @if(session('sukses'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center">
            <i class="fas fa-check-circle mr-3 text-lg"></i> {{ session('sukses') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-start space-x-4 animasi-masuk jeda-1">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Total Laporan</p>
                <h4 class="text-2xl font-bold text-gray-800 leading-none mb-2">{{ number_format($statistik['total'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-green-600 font-bold">+12% dari bulan lalu</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-start space-x-4 animasi-masuk jeda-2">
            <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-500 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Sedang Proses</p>
                <h4 class="text-2xl font-bold text-gray-800 leading-none mb-2">{{ number_format($statistik['proses'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-yellow-600 font-bold">Prioritas Tinggi: 8</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-start space-x-4 animasi-masuk jeda-3">
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Laporan Selesai</p>
                <h4 class="text-2xl font-bold text-gray-800 leading-none mb-2">{{ number_format($statistik['selesai'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-green-500 font-bold">98.2% Kepuasan Publik</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-start space-x-4 animasi-masuk jeda-4">
            <div class="w-12 h-12 rounded-full bg-red-50 text-red-400 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-ban"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Laporan Ditolak</p>
                <h4 class="text-2xl font-bold text-gray-800 leading-none mb-2">{{ number_format($statistik['ditolak'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-gray-400 font-bold">Duplikasi: 5</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-8 animasi-masuk jeda-5">
        <form action="{{ route('admin_universal.laporan') }}" method="GET" class="p-5 border-b border-gray-100 bg-gray-50/50 rounded-t-2xl">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Jenis Laporan</label>
                    <select name="jenis" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm outline-none focus:border-pupr-blue bg-white text-gray-600 font-medium">
                        <option value="Semua Jenis">Semua Jenis</option>
                        <option value="Infrastruktur" {{ request('jenis') == 'Infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                        <option value="Fasilitas Umum" {{ request('jenis') == 'Fasilitas Umum' ? 'selected' : '' }}>Fasilitas Umum</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Status</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm outline-none focus:border-pupr-blue bg-white text-gray-600 font-medium">
                        <option value="Semua Status">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Validasi</option>
                        <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Sedang Proses</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Bidang</label>
                    <select name="bidang" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm outline-none focus:border-pupr-blue bg-white text-gray-600 font-medium">
                        <option value="Semua Bidang">Semua Bidang</option>
                        @foreach($daftar_bidang ?? [] as $bidang)
                            <option value="{{ $bidang }}" {{ request('bidang') == $bidang ? 'selected' : '' }}>{{ $bidang }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Rentang Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm outline-none focus:border-pupr-blue bg-white text-gray-600 font-medium">
                </div>
                <div class="flex justify-end space-x-2">
                    @if(request()->has('status') || request()->has('bidang') || request()->has('tanggal'))
                        <a href="{{ route('admin_universal.laporan') }}" class="w-11 h-11 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-lg shadow-sm flex items-center justify-center transition" title="Reset Filter">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                    <button type="submit" class="w-11 h-11 bg-pupr-blue text-white hover:bg-blue-800 rounded-lg shadow-sm flex items-center justify-center transition" title="Terapkan Filter">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-gray-100 text-gray-400 text-[11px] uppercase font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">ID Laporan</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Jenis Laporan</th>
                        <th class="px-6 py-4">Pelapor</th>
                        <th class="px-6 py-4">Lokasi</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm font-medium text-gray-700">
                    @forelse($semua_laporan ?? [] as $index => $item)
                    <tr class="hover:bg-blue-50/30 transition animasi-masuk" style="animation-delay: {{ 0.5 + ($index * 0.05) }}s;">
                        <td class="px-6 py-5 text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-5 font-bold text-blue-700">#{{ $item->id_laporan }}</td>
                        <td class="px-6 py-5 text-gray-500">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M') }}<br>
                            <span class="text-xs">{{ \Carbon\Carbon::parse($item->created_at)->format('Y') }}</span>
                        </td>
                        <td class="px-6 py-5">
                            @if(strtolower($item->status) === 'pending')
                                <span class="px-3 py-1 border border-gray-200 text-gray-400 bg-gray-50 rounded-full text-[10px] font-bold italic">
                                    <i class="fas fa-clock mr-1"></i> Belum Ditentukan
                                </span>
                            @else
                                @if(str_contains(strtolower($item->kategori_bidang), 'marga'))
                                    <span class="px-3 py-1 border border-blue-200 text-blue-600 bg-blue-50 rounded-full text-[10px] font-bold">Kerusakan Jalan</span>
                                @elseif(str_contains(strtolower($item->kategori_bidang), 'sda'))
                                    <span class="px-3 py-1 border border-pink-200 text-pink-600 bg-pink-50 rounded-full text-[10px] font-bold">Drainase</span>
                                @else
                                    <span class="px-3 py-1 border border-orange-200 text-orange-600 bg-orange-50 rounded-full text-[10px] font-bold">{{ $item->kategori_bidang }}</span>
                                @endif
                            @endif
                        </td>
<<<<<<< HEAD
                        <!-- ========================================== -->
=======
>>>>>>> 89410d36ef719b97e4827090be94728ead78c6e2
                        <td class="px-6 py-5">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ substr($item->pelapor->nama_lengkap ?? 'A', 0, 2) }}
                                </div>
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
                                <div class="flex items-center text-green-600 bg-green-50 px-2 py-1 rounded w-max text-xs font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2"></span> Selesai
                                </div>
                            @elseif($item->status == 'proses' || $item->status == 'diteruskan')
                                <div class="flex items-center text-yellow-600 bg-yellow-50 px-2 py-1 rounded w-max text-xs font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-2"></span> Proses
                                </div>
                            @elseif($item->status == 'ditolak')
                                <div class="flex items-center text-red-600 bg-red-50 px-2 py-1 rounded w-max text-xs font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-2"></span> Ditolak
                                </div>
                            @else
                                <div class="flex items-center text-gray-600 bg-gray-100 px-2 py-1 rounded w-max text-xs font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-2"></span> Ditunda
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-center">
                            <a href="{{ route('admin_universal.laporan.detail', $item->id) }}" class="text-gray-400 hover:text-pupr-blue transition p-2" title="Lihat Detail">
                                <i class="far fa-eye text-lg"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-gray-400 font-medium">Belum ada data laporan yang masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-5 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between text-sm text-gray-500 gap-4">
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

        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col">
            <div class="flex justify-between items-center mb-4 px-1">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <i class="fas fa-map-marked-alt text-blue-600 mr-2"></i> Visualisasi Lokasi Laporan Terbaru (SIGAP)
                </h3>
                <a href="{{ route('admin_universal.beranda') }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Peta Lengkap</a>
            </div>
            <div class="relative w-full flex-1 min-h-[300px] rounded-xl overflow-hidden border border-gray-200">
                <div id="map" class="w-full h-full bg-gray-100"></div>
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
                    <div class="relative pl-6 border-l-2 border-blue-100">
                        <div class="absolute -left-[5px] top-1 w-2 h-2 rounded-full bg-blue-500 ring-4 ring-white"></div>
                        <p class="text-[10px] font-bold text-gray-400 mb-1">{{ $log->created_at->diffForHumans() }}</p>
                        <p class="text-sm font-bold text-gray-800 mb-0.5">{{ $log->aktivitas }}</p>
                        <p class="text-[11px] text-gray-500">{{ $log->kategori ?? 'Aktivitas Laporan' }}</p>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <i class="fas fa-clipboard-check text-gray-300 text-3xl mb-3"></i>
                        <p class="text-sm text-gray-400 font-medium">Belum ada aktivitas tercatat.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div id="modal-log-laporan" class="fixed inset-0 z-[4000] hidden">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="tutupModalLogLaporan()"></div>

    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-3xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-fadeUpMasuk">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Seluruh Riwayat Aktivitas Laporan</h3>
                <p class="text-xs text-gray-500">Mencatat seluruh jejak rekam perubahan status laporan di dalam sistem.</p>
            </div>
            <button onclick="tutupModalLogLaporan()" class="text-gray-400 hover:text-red-500 transition text-xl">×</button>
        </div>

        <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
            <ul class="space-y-4 border-l-2 border-blue-100 ml-3">
                @forelse($semua_aktivitas ?? [] as $log)
                <li class="relative pl-6">
                    <span class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-pupr-blue border-4 border-white shadow-sm"></span>
                    <p class="text-sm font-bold text-gray-800">{{ $log->aktivitas }}</p>
                    <p class="text-[10px] font-semibold text-gray-500 mt-1 uppercase tracking-wider">{{ $log->created_at->format('d M Y - H:i') }} WIB <span class="ml-2 text-blue-500">{{ $log->kategori ?? 'Laporan' }}</span></p>
                </li>
                @empty
                <p class="text-sm text-gray-500 ml-6">Riwayat aktivitas bersih.</p>
                @endforelse
            </ul>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
            <p class="text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i> Data yang dihapus akan hilang dari database.</p>

            <form id="form-hapus-semua-log-laporan" action="{{ Route::has('admin_universal.laporan.log.hapus') ? route('admin_universal.laporan.log.hapus') : '#' }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" onclick="konfirmasiHapusSemuaLogLaporan()" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-500 hover:text-white border border-red-100 hover:border-red-500 rounded-lg text-xs font-bold transition shadow-sm flex items-center">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus Seluruh Riwayat
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // ========================================================
    // SCRIPT JAVASCRIPT KHUSUS UNTUK AKTIVITAS LOG LAPORAN
    // ========================================================

    // KOMENTAR: Menampilkan Modal (Menghapus class 'hidden')
    function bukaModalLogLaporan() {
        document.getElementById('modal-log-laporan').classList.remove('hidden');
    }

    // KOMENTAR: Menyembunyikan Modal (Menambahkan class 'hidden')
    function tutupModalLogLaporan() {
        document.getElementById('modal-log-laporan').classList.add('hidden');
    }

    // KOMENTAR: Fungsi ini akan memanggil desain peringatan menggunakan SweetAlert2
    function konfirmasiHapusSemuaLogLaporan() {
        tutupModalLogLaporan(); // Sembunyikan modal log dulu agar layar tidak kepenuhan pop-up

        Swal.fire({
            title: 'Hapus Seluruh Riwayat?',
            text: "Apakah Anda yakin ingin MENGHAPUS SELURUH riwayat aktivitas laporan dari database? Tindakan ini permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // Warna merah untuk tombol hapus
            cancelButtonColor: '#9ca3af',  // Warna abu-abu untuk batal
            confirmButtonText: 'Ya, Hapus Permanen!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl shadow-xl border border-gray-100',
                title: 'text-gray-800 font-bold',
                confirmButton: 'px-6 py-2.5 rounded-lg text-sm font-bold shadow-md',
                cancelButton: 'px-6 py-2.5 rounded-lg text-sm font-bold shadow-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // KOMENTAR: Jika admin mengklik "Ya, Hapus", sistem akan mengeksekusi form-hapus-semua-log-laporan di atas
                document.getElementById('form-hapus-semua-log-laporan').submit();
            } else {
                // KOMENTAR: Jika dibatalkan, modal log aktivitas akan terbuka kembali
                bukaModalLogLaporan();
            }
        });
    }
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    let map, layerStandar, layerSatelit, markerGroup;
    let dataLaporan = @json($sebaran_laporan ?? []);

    document.addEventListener("DOMContentLoaded", function() {
        map = L.map('map').setView([-6.5627, 107.7613], 12);
        layerStandar = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 20 });
        layerSatelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });

        layerStandar.addTo(map);
        markerGroup = L.layerGroup().addTo(map);
        renderMarkers();
    });

    function renderMarkers() {
        markerGroup.clearLayers();

        // KETERANGAN: Baris filterAktif dihapus karena di halaman ini tidak ada checkbox filter peta.
        // Kita langsung melooping (menampilkan) semua data laporan yang dikirim dari Controller.

        if(dataLaporan.length > 0) {
            dataLaporan.forEach(function(laporan) {
                // KETERANGAN: Hanya mengecek apakah laporan tersebut punya titik koordinat GPS atau tidak
                if(laporan.lokasi_gps) {
                    let koordinat = laporan.lokasi_gps.split(',');
                    if(koordinat.length == 2) {
                        let lat = parseFloat(koordinat[0].trim());
                        let lng = parseFloat(koordinat[1].trim());

                        // KETERANGAN: Menentukan warna ikon pin berdasarkan status
                        let warnaIkon = 'text-red-500'; // Default merah untuk laporan baru/ditolak
                        if(laporan.status === 'proses' || laporan.status === 'diteruskan') warnaIkon = 'text-yellow-500'; // Kuning
                        if(laporan.status === 'selesai') warnaIkon = 'text-green-500'; // Hijau

                        // KETERANGAN: Membuat ikon pin kustom menggunakan FontAwesome
                        let ikonCustom = L.divIcon({
                            className: 'bg-transparent',
                            html: `<div style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);" class="${warnaIkon} text-[35px] hover:scale-110 transition-transform cursor-pointer">
                                     <i class="fas fa-map-marker-alt"></i>
                                   </div>`,
                            iconSize: [30, 42],
                            iconAnchor: [15, 40],
                            popupAnchor: [0, -35]
                        });

                        // KETERANGAN: URL untuk tombol pop-up Google Maps
                        let urlStreetView = `https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=$$${lat},${lng}`;
                        let urlMaps = `https://www.google.com/maps/search/?api=1&query=$$${lat},${lng}`;

                        // KETERANGAN: Memasang titik marker ke dalam peta beserta pop-up informasi detailnya
                        let marker = L.marker([lat, lng], {icon: ikonCustom});
                        marker.bindPopup(`
                            <div class="p-2 w-56 text-center">
                                <p class="text-xs font-bold text-gray-500 mb-1">ID: ${laporan.id_laporan}</p>
                                <p class="font-extrabold text-gray-800 text-sm mb-2 leading-tight">${laporan.kategori_bidang}</p>
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 text-[10px] rounded-full font-bold mb-4 inline-block">STATUS: ${laporan.status.toUpperCase()}</span>
                                <div class="space-y-2">
                                    <a href="${urlStreetView}" target="_blank" class="w-full bg-blue-600 hover:bg-blue-700 !text-white text-xs font-bold py-2.5 px-3 rounded-lg flex items-center justify-center transition shadow-md" style="color: white !important;">
                                        <i class="fas fa-street-view mr-2 text-sm"></i> Lihat Sekitar
                                    </a>
                                    <a href="${urlMaps}" target="_blank" class="w-full border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-blue-600 text-xs font-bold py-2 px-3 rounded-lg flex items-center justify-center transition" style="text-decoration: none;">
                                        <i class="fas fa-map-marked-alt mr-2"></i> Buka Google Maps
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

    // Fungsi Peta Tambahan
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
            document.getElementById('menu-layer').classList.add('hidden');
        }
        if(!event.target.closest('#btn-filter') && !event.target.closest('#menu-filter')) {
            document.getElementById('menu-filter').classList.add('hidden');
        }
    });
</script>
@endsection
