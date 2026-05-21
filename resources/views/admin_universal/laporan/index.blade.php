@extends('layouts.app')

<!-- CSS Leaflet untuk Peta -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    .leaflet-popup-content { margin: 12px; }
    /* Kustomisasi scrollbar untuk Aktivitas Terbaru */
    .activity-scroll::-webkit-scrollbar { width: 4px; }
    .activity-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>

@section('konten')
<div class="max-w-7xl mx-auto">

    <!-- HEADER -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Kelola Laporan Masuk</h2>
            <p class="text-sm text-gray-500 font-medium mt-1">Pantau dan tindak lanjuti laporan infrastruktur di seluruh wilayah melalui sistem SIGAP.</p>
        </div>
        <div class="flex space-x-2">
            <!-- Tombol Ekspor Excel (CSV) -->
            <a href="{{ route('admin_universal.laporan.ekspor') }}" class="bg-white border border-gray-200 text-green-700 hover:bg-green-50 px-3 py-2.5 rounded-lg text-sm font-bold shadow-sm transition flex items-center">
                <i class="fas fa-file-excel mr-2"></i> Ekspor Laporan Ke Excel
            </a>

            <!-- Tombol Ekspor PDF (Baru) -->
            <a href="{{ route('admin_universal.laporan.ekspor_pdf') }}" class="bg-white border border-gray-200 text-red-600 hover:bg-red-50 px-3 py-2.5 rounded-lg text-sm font-bold shadow-sm transition flex items-center">
                <i class="fas fa-file-pdf mr-2"></i> Ekspor Laporan Ke PDF
            </a>

            <!-- Tombol Tambah Laporan -->
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

    <!-- 4 KARTU STATISTIK UTAMA -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <!-- Total Laporan -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-start space-x-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Total Laporan</p>
                <h4 class="text-2xl font-bold text-gray-800 leading-none mb-2">{{ number_format($statistik['total'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-green-600 font-bold">+12% dari bulan lalu</p>
            </div>
        </div>

        <!-- Sedang Proses -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-start space-x-4">
            <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-500 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Sedang Proses</p>
                <h4 class="text-2xl font-bold text-gray-800 leading-none mb-2">{{ number_format($statistik['proses'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-yellow-600 font-bold">Prioritas Tinggi: 8</p>
            </div>
        </div>

        <!-- Selesai -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-start space-x-4">
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Laporan Selesai</p>
                <h4 class="text-2xl font-bold text-gray-800 leading-none mb-2">{{ number_format($statistik['selesai'] ?? 0, 0, ',', '.') }}</h4>
                <p class="text-[10px] text-green-500 font-bold">98.2% Kepuasan Publik</p>
            </div>
        </div>

        <!-- Ditolak -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-start space-x-4">
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

    <!-- AREA FILTER & TABEL -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-8">

        <!-- Filter Bar (Berfungsi Penuh) -->
        <form action="{{ route('admin_universal.laporan') }}" method="GET" class="p-5 border-b border-gray-100 bg-gray-50/50 rounded-t-2xl">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

                <!-- Filter Jenis Laporan (Opsional) -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Jenis Laporan</label>
                    <select name="jenis" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm outline-none focus:border-pupr-blue bg-white text-gray-600 font-medium">
                        <option value="Semua Jenis">Semua Jenis</option>
                        <option value="Infrastruktur" {{ request('jenis') == 'Infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                        <option value="Fasilitas Umum" {{ request('jenis') == 'Fasilitas Umum' ? 'selected' : '' }}>Fasilitas Umum</option>
                    </select>
                </div>

                <!-- Filter Status -->
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

                <!-- Filter Bidang (Dinamis dari Database) -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Bidang</label>
                    <select name="bidang" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm outline-none focus:border-pupr-blue bg-white text-gray-600 font-medium">
                        <option value="Semua Bidang">Semua Bidang</option>
                        @foreach($daftar_bidang as $bidang)
                            <option value="{{ $bidang }}" {{ request('bidang') == $bidang ? 'selected' : '' }}>{{ $bidang }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Tanggal -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Rentang Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm outline-none focus:border-pupr-blue bg-white text-gray-600 font-medium">
                </div>

                <!-- Tombol Aksi Filter -->
                <div class="flex justify-end space-x-2">
                    <!-- Tombol Reset (Muncul jika sedang menggunakan filter) -->
                    @if(request()->has('status') || request()->has('bidang') || request()->has('tanggal'))
                        <a href="{{ route('admin_universal.laporan') }}" class="w-11 h-11 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-lg shadow-sm flex items-center justify-center transition" title="Reset Filter">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif

                    <!-- Tombol Terapkan Filter -->
                    <button type="submit" class="w-11 h-11 bg-pupr-blue text-white hover:bg-blue-800 rounded-lg shadow-sm flex items-center justify-center transition" title="Terapkan Filter">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </form>

        <!-- Tabel Data -->
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
                    @forelse($semua_laporan as $index => $item)
                    <tr class="hover:bg-blue-50/30 transition">
                        <td class="px-6 py-5 text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-5 font-bold text-blue-700">#{{ $item->id_laporan }}</td>
                        <td class="px-6 py-5 text-gray-500">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M') }}<br>
                            <span class="text-xs">{{ \Carbon\Carbon::parse($item->created_at)->format('Y') }}</span>
                        </td>

                        <!-- ========================================== -->
                        <!-- LOGIKA BARU UNTUK KOLOM JENIS LAPORAN      -->
                        <!-- ========================================== -->
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
                        <!-- ========================================== -->
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
                            <!-- Ikon Aksi Disatukan menjadi Detail (Ikon Mata) -->
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

        <!-- Footer / Pagination Dinamis -->
        <div class="p-5 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between text-sm text-gray-500 gap-4">

            @if($semua_laporan->total() > 0)
                <div>
                    Menampilkan <span class="font-bold text-gray-800">{{ $semua_laporan->firstItem() }}-{{ $semua_laporan->lastItem() }}</span> dari <span class="font-bold text-gray-800">{{ number_format($semua_laporan->total(), 0, ',', '.') }}</span> laporan
                </div>

                @if($semua_laporan->hasPages())
                <div class="flex space-x-1">
                    {{-- Tombol Previous --}}
                    @if ($semua_laporan->onFirstPage())
                        <span class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed"><i class="fas fa-chevron-left text-xs"></i></span>
                    @else
                        <a href="{{ $semua_laporan->appends(request()->query())->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 hover:bg-gray-50 text-gray-600 transition shadow-sm"><i class="fas fa-chevron-left text-xs"></i></a>
                    @endif

                    {{-- Looping Nomor Halaman --}}
                    @foreach ($semua_laporan->appends(request()->query())->links()->elements as $element)
                        {{-- Pemisah Tiga Titik (...) --}}
                        @if (is_string($element))
                            <span class="w-8 h-8 flex items-center justify-center text-gray-400">{{ $element }}</span>
                        @endif

                        {{-- Deretan Angka --}}
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

                    {{-- Tombol Next --}}
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

    <!-- PETA & AKTIVITAS BAWAH -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-10">

        <!-- PETA LEAFLET (KIRI) -->
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

        <!-- AKTIVITAS TERBARU (KANAN) -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col h-[400px]">
            <h3 class="font-bold text-gray-800 flex items-center mb-6">
                <i class="fas fa-history text-blue-600 mr-2"></i> Aktivitas Terbaru
            </h3>

            <div class="flex-1 overflow-y-auto activity-scroll pr-2 space-y-6">
                <!-- Item Timeline 1 -->
                <div class="relative pl-6 border-l-2 border-blue-100">
                    <div class="absolute -left-[5px] top-1 w-2 h-2 rounded-full bg-blue-500 ring-4 ring-white"></div>
                    <p class="text-[10px] font-bold text-gray-400 mb-1">10 Menit yang lalu</p>
                    <p class="text-sm font-bold text-gray-800 mb-0.5">Status Laporan #REP-001 diubah</p>
                    <p class="text-[11px] text-gray-500">Oleh Admin Utama: Selesai</p>
                </div>

                <!-- Item Timeline 2 -->
                <div class="relative pl-6 border-l-2 border-blue-100">
                    <div class="absolute -left-[5px] top-1 w-2 h-2 rounded-full bg-blue-500 ring-4 ring-white"></div>
                    <p class="text-[10px] font-bold text-gray-400 mb-1">1 Jam yang lalu</p>
                    <p class="text-sm font-bold text-gray-800 mb-0.5">Laporan Baru Masuk</p>
                    <p class="text-[11px] text-gray-500">ID: #REP-009 | Jenis: Infrastruktur Air</p>
                </div>

                <!-- Item Timeline 3 -->
                <div class="relative pl-6 border-l-2 border-blue-100">
                    <div class="absolute -left-[5px] top-1 w-2 h-2 rounded-full bg-blue-300 ring-4 ring-white"></div>
                    <p class="text-[10px] font-bold text-gray-400 mb-1">3 Jam yang lalu</p>
                    <p class="text-sm font-bold text-gray-800 mb-0.5">Ekspor Data Berhasil</p>
                    <p class="text-[11px] text-gray-500">Laporan Bulanan April 2026</p>
                </div>

                <!-- Item Timeline 4 -->
                <div class="relative pl-6 border-l-2 border-transparent">
                    <div class="absolute -left-[5px] top-1 w-2 h-2 rounded-full bg-gray-300 ring-4 ring-white"></div>
                    <p class="text-[10px] font-bold text-gray-400 mb-1">Kemarin</p>
                    <p class="text-sm font-bold text-gray-800 mb-0.5">Audit Sistem Mingguan</p>
                    <p class="text-[11px] text-gray-500">Selesai tanpa kendala teknis</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH LAPORAN BARU (Desain Desktop Profesional) -->
<div id="modalLaporan" class="fixed inset-0 z-[3000] hidden flex items-center justify-center p-4">
    <!-- Latar Belakang Gelap -->
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="toggleModalLaporan()"></div>

    <!-- Kotak Modal (Diperlebar menjadi max-w-4xl) -->
    <div class="bg-white shadow-2xl w-full max-w-4xl z-10 overflow-hidden transform transition-all rounded-2xl flex flex-col max-h-[95vh]">

        <!-- Header -->
        <div class="bg-[#2D3A54] text-white px-5 py-4 flex items-center justify-between shadow-md shrink-0">
            <div class="flex items-center">
                <i class="fas fa-file-signature text-xl mr-3"></i>
                <h3 class="text-lg font-bold">Formulir Laporan Baru</h3>
            </div>
            <button type="button" onclick="toggleModalLaporan()" class="text-white hover:text-red-400 transition bg-white/10 hover:bg-white/20 rounded-lg p-2">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Form Container -->
        <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
            <form action="{{ route('admin_universal.laporan.simpan') }}" method="POST" enctype="multipart/form-data" id="formLaporanManual">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
                    <!-- ========================================== -->
                    <!-- KOLOM KIRI: DATA LAPORAN & MEDIA           -->
                    <!-- ========================================== -->
                    <div class="space-y-5">

                        <!-- Kategori Bidang -->
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase mb-1.5 block">Kategori Infrastruktur <span class="text-red-500">*</span></label>
                            <select name="kategori_bidang" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:border-pupr-blue bg-gray-50 font-bold text-gray-700" required>
                                <option value="">-- Pilih Bidang Infrastruktur --</option>
                                <option value="Bina Marga">Bina Marga (Jalan & Jembatan)</option>
                                <option value="SDA">SDA (Irigasi & Sungai)</option>
                                <option value="Cipta Karya">Cipta Karya (Fasilitas Umum)</option>
                            </select>
                        </div>

                        <!-- Deskripsi Masalah -->
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase mb-1.5 block">Deskripsi Masalah <span class="text-red-500">*</span></label>
                            <textarea name="deskripsi_laporan" rows="4" placeholder="Contoh: Jalan berlubang parah menyebabkan kemacetan..." class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:border-pupr-blue outline-none resize-none shadow-sm" required></textarea>
                        </div>

                        <!-- Unggah Media (Desain Web) -->
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase mb-1.5 block">Unggah Bukti (Foto/Video) <span class="text-red-500">*</span></label>
                            <div class="w-full relative border-2 border-dashed border-gray-300 rounded-xl p-5 flex flex-col items-center justify-center bg-gray-50 hover:bg-blue-50 transition hover:border-pupr-blue cursor-pointer" onclick="document.getElementById('fileInput').click()">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm font-bold text-gray-600">Klik untuk memilih file media</p>
                                <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, atau MP4 (Maks 5MB)</p>
                                <input type="file" id="fileInput" name="foto_bukti" accept="image/*,video/*" class="hidden" onchange="previewMediaWeb(event)" required>
                            </div>

                            <!-- Tempat Preview -->
                            <div id="previewContainerWeb" class="mt-3 hidden w-full h-40 rounded-xl overflow-hidden relative border border-gray-200 shadow-sm bg-gray-100"></div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- KOLOM KANAN: PETA & KOORDINAT              -->
                    <!-- ========================================== -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-bold text-gray-500 uppercase block mb-1">Titik Lokasi Kejadian <span class="text-red-500">*</span></label>

                        <!-- PETA INTERAKTIF DENGAN TOMBOL -->
                        <div class="relative w-full h-64 md:h-[280px] bg-gray-200 rounded-xl border border-gray-300 z-0 overflow-hidden shadow-inner" id="mapPickerContainer">

                            <!-- Tombol Aksi Peta -->
                            <div class="absolute top-3 right-3 z-[1000] flex flex-col space-y-2">
                                <!-- Tombol Fullscreen -->
                                <button type="button" onclick="toggleFullscreenPicker()" class="bg-white text-gray-600 hover:text-pupr-blue border border-gray-200 hover:bg-blue-50 rounded-md w-8 h-8 flex items-center justify-center transition shadow-md focus:outline-none" title="Layar Penuh">
                                    <i class="fas fa-expand text-sm" id="icon-fs-picker"></i>
                                </button>

                                <!-- Tombol Ganti Layer -->
                                <div class="relative">
                                    <button type="button" id="btn-layer-picker" onclick="toggleLayerMenuPicker()" class="bg-white text-gray-600 hover:text-pupr-blue border border-gray-200 hover:bg-blue-50 rounded-md w-8 h-8 flex items-center justify-center transition shadow-md focus:outline-none" title="Ganti Tampilan Peta">
                                        <i class="fas fa-layer-group text-sm"></i>
                                    </button>

                                    <!-- Dropdown Layer -->
                                    <div id="menu-layer-picker" class="hidden absolute right-0 top-full mt-1 w-36 bg-white rounded-xl shadow-xl border border-gray-100 p-1.5 origin-top-right">
                                        <button type="button" onclick="gantiLayerPicker('standar')" class="w-full text-left px-3 py-2 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue rounded-lg transition mb-1">
                                            <i class="fas fa-map mr-2"></i> Peta Jalan
                                        </button>
                                        <button type="button" onclick="gantiLayerPicker('satelit')" class="w-full text-left px-3 py-2 text-xs font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue rounded-lg transition">
                                            <i class="fas fa-satellite mr-2"></i> Citra Satelit
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="mapPicker" class="w-full h-full"></div>
                        </div>

                        <!-- Kolom Input Alamat & Koordinat -->
                        <div class="grid grid-cols-1 gap-4 mt-4">
                            <div class="relative">
                                <label class="text-[10px] font-bold text-pupr-blue uppercase mb-1 block">Koordinat GPS</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                        <i class="fas fa-map-pin"></i>
                                    </span>
                                    <input type="text" id="lokasi_gps" name="lokasi_gps" placeholder="-6.5627, 107.7613" class="w-full border border-gray-300 rounded-r-lg p-2.5 text-sm focus:border-pupr-blue outline-none shadow-sm font-mono" oninput="updateMapFromInput()" required>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1">Klik di peta atau ketik manual.</p>
                            </div>

                            <div>
                                <label class="text-[10px] font-bold text-gray-500 uppercase mb-1 block">Nama Jalan / Patokan Terdekat</label>
                                <input type="text" id="alamat_map" name="alamat_map" placeholder="Contoh: Jl. Pejuang 45, depan Indomaret..." class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:border-pupr-blue outline-none shadow-sm" required>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="border-t border-gray-100 pt-5 mt-2 flex justify-end">
                    <button type="submit" class="w-full md:w-auto px-8 py-3 bg-[#102A63] hover:bg-[#1a3a8a] text-white rounded-lg text-sm font-bold transition shadow-md flex justify-center items-center">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Laporan ke Sistem
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Variabel global untuk peta modal
    let pickerMap, pickerMarker, layerStandarPicker, layerSatelitPicker;

    function toggleModalLaporan() {
        const modal = document.getElementById('modalLaporan');
        modal.classList.toggle('hidden');

        if(!modal.classList.contains('hidden')) {
            setTimeout(() => {
                if(!pickerMap) {
                    // 1. Inisialisasi Peta
                    pickerMap = L.map('mapPicker').setView([-6.5627, 107.7613], 15);

                    // 2. Siapkan 2 Jenis Layer
                    layerStandarPicker = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 20 });
                    layerSatelitPicker = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });

                    layerStandarPicker.addTo(pickerMap); // Set default

                    // 3. Buat Marker Interaktif
                    pickerMarker = L.marker([-6.5627, 107.7613], {draggable: true}).addTo(pickerMap);

                    pickerMarker.on('dragend', function(e) {
                        let pos = e.target.getLatLng();
                        document.getElementById('lokasi_gps').value = pos.lat.toFixed(6) + ', ' + pos.lng.toFixed(6);
                    });

                    pickerMap.on('click', function(e) {
                        pickerMarker.setLatLng(e.latlng);
                        document.getElementById('lokasi_gps').value = e.latlng.lat.toFixed(6) + ', ' + e.latlng.lng.toFixed(6);
                    });
                }
                pickerMap.invalidateSize(); // Wajib agar peta tidak abu-abu
            }, 300);
        }
    }

    // ============================================
    // FUNGSI KONTROL PETA MODAL (Layer & Fullscreen)
    // ============================================
    function toggleLayerMenuPicker() {
        document.getElementById('menu-layer-picker').classList.toggle('hidden');
    }

    function gantiLayerPicker(jenis) {
        if(jenis === 'standar') {
            pickerMap.removeLayer(layerSatelitPicker);
            layerStandarPicker.addTo(pickerMap);
        } else {
            pickerMap.removeLayer(layerStandarPicker);
            layerSatelitPicker.addTo(pickerMap);
        }
        document.getElementById('menu-layer-picker').classList.add('hidden');
    }

    function toggleFullscreenPicker() {
        let container = document.getElementById('mapPickerContainer');
        if (!document.fullscreenElement) {
            container.requestFullscreen().catch(err => { console.log(err); });
        } else {
            document.exitFullscreen();
        }
    }

    // Deteksi perubahan layar penuh
    document.addEventListener('fullscreenchange', (event) => {
        let iconBtn = document.getElementById('icon-fs-picker');
        let divContainer = document.getElementById('mapPickerContainer');

        if (document.fullscreenElement) {
            iconBtn.classList.remove('fa-expand');
            iconBtn.classList.add('fa-compress');
            divContainer.classList.remove('h-64', 'md:h-[280px]', 'rounded-xl');
            divContainer.classList.add('h-screen', 'rounded-none');
        } else {
            iconBtn.classList.remove('fa-compress');
            iconBtn.classList.add('fa-expand');
            divContainer.classList.remove('h-screen', 'rounded-none');
            divContainer.classList.add('h-64', 'md:h-[280px]', 'rounded-xl');
        }
        setTimeout(() => { if(pickerMap) pickerMap.invalidateSize(); }, 300);
    });

    // Tutup menu layer jika klik di luar
    document.addEventListener('click', function(event) {
        if(!event.target.closest('#btn-layer-picker') && !event.target.closest('#menu-layer-picker')) {
            let menu = document.getElementById('menu-layer-picker');
            if(menu) menu.classList.add('hidden');
        }
    });

    // ============================================
    // FUNGSI LAINNYA
    // ============================================
    function updateMapFromInput() {
        const inputVal = document.getElementById('lokasi_gps').value;
        const coords = inputVal.split(',');
        if (coords.length === 2) {
            const lat = parseFloat(coords[0].trim());
            const lng = parseFloat(coords[1].trim());
            if (!isNaN(lat) && !isNaN(lng)) {
                pickerMarker.setLatLng([lat, lng]);
                pickerMap.flyTo([lat, lng], 16);
            }
        }
    }

    function previewMediaWeb(event) {
        const container = document.getElementById('previewContainerWeb');
        const file = event.target.files[0];

        if(file) {
            container.classList.remove('hidden');
            const reader = new FileReader();
            reader.onload = function(e) {
                const btnHapus = `<button type="button" onclick="hapusFileWeb()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600 shadow-lg z-10 transition"><i class="fas fa-times"></i></button>`;
                if(file.type.startsWith('image/')) {
                    container.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">` + btnHapus;
                } else if(file.type.startsWith('video/')) {
                    container.innerHTML = `<video src="${e.target.result}" class="w-full h-full object-cover" controls autoplay muted></video>` + btnHapus;
                }
            }
            reader.readAsDataURL(file);
        }
    }

    function hapusFileWeb() {
        document.getElementById('fileInput').value = '';
        const container = document.getElementById('previewContainerWeb');
        container.innerHTML = '';
        container.classList.add('hidden');
    }
</script>

<!-- SCRIPT PETA LEAFLET -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inisialisasi Map
        let map = L.map('map').setView([-6.5627, 107.7613], 11);

        // Custom Style Peta (Sesuai Referensi Gambar yang agak kebiruan/gelap)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let dataLaporan = @json($sebaran_laporan ?? []);

        if(dataLaporan.length > 0) {
            dataLaporan.forEach(function(laporan) {
                if(laporan.lokasi_gps) {
                    let koordinat = laporan.lokasi_gps.split(',');
                    if(koordinat.length == 2) {
                        let lat = parseFloat(koordinat[0].trim());
                        let lng = parseFloat(koordinat[1].trim());

                        // Logika Warna Ikon Sama Seperti Dashboard
                        let warnaIkon = 'text-red-500';
                        if(laporan.status === 'proses') warnaIkon = 'text-yellow-500';
                        if(laporan.status === 'selesai') warnaIkon = 'text-green-500';

                        let ikonCustom = L.divIcon({
                            className: 'bg-transparent',
                            html: `<div style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);" class="${warnaIkon} text-[25px] hover:scale-110 transition-transform cursor-pointer">
                                     <i class="fas fa-map-marker-alt"></i>
                                   </div>`,
                            iconSize: [25, 35],
                            iconAnchor: [12, 35],
                            popupAnchor: [0, -30]
                        });

                        // 1. Tautan URL Google Maps & Street View
                        let urlStreetView = `https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${lat},${lng}`;
                        let urlMaps = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;

                        // 2. Bind Popup dengan Desain dan Tombol Lengkap seperti di Dasbor
                        let marker = L.marker([lat, lng], {icon: ikonCustom});
                        marker.bindPopup(`
                            <div class="p-2 w-56 text-center">
                                <p class="text-xs font-bold text-gray-500 mb-1">ID: ${laporan.id_laporan}</p>
                                <p class="font-extrabold text-gray-800 text-sm mb-2 leading-tight">${laporan.kategori_bidang}</p>
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 text-[10px] rounded-full font-bold mb-4 inline-block">STATUS: ${laporan.status.toUpperCase()}</span>

                                <div class="space-y-2">
                                    <a href="${urlStreetView}" target="_blank" class="w-full bg-blue-600 hover:bg-blue-700 !text-white text-xs font-bold py-2.5 px-3 rounded-lg flex items-center justify-center transition shadow-md" style="color: white !important;">
                                        <i class="fas fa-street-view mr-2 text-sm"></i> Lihat Sekitar (Street View)
                                    </a>
                                    <a href="${urlMaps}" target="_blank" class="w-full border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-blue-600 text-xs font-bold py-2 px-3 rounded-lg flex items-center justify-center transition" style="text-decoration: none;">
                                        <i class="fas fa-map-marked-alt mr-2"></i> Buka Google Maps
                                    </a>
                                </div>
                            </div>
                        `);
                        marker.addTo(map);
                    }
                }
            });
        }
    });
</script>
@endsection
