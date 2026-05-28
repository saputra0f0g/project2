@extends('layouts.app_bidang')

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #mapDetail { border-radius: 0.75rem; z-index: 10; transition: all 0.3s ease; }
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

    /* Custom Scrollbar untuk Dropdown Select */
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
@endpush

@section('konten')
<div class="max-w-7xl mx-auto pb-10">

    <div class="mb-6 animasi-masuk">
        <div class="flex items-center text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">
            <a href="{{ route('admin_bidang.beranda') }}" class="hover:text-pupr-blue transition">DASHBOARD</a>
            <span class="mx-2">/</span>
            <a href="{{ route('admin_bidang.laporan') }}" class="hover:text-pupr-blue transition">KELOLA LAPORAN</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800">DETAIL LAPORAN</span>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-3 md:space-x-4">
                <a href="{{ route('admin_bidang.laporan') }}" class="w-10 h-10 bg-white border border-gray-200 text-gray-500 hover:text-pupr-blue hover:border-pupr-blue hover:bg-blue-50 rounded-xl flex items-center justify-center transition shadow-sm focus:outline-none transform hover:-translate-x-1" title="Kembali ke Daftar Laporan">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>

                <div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ $laporan->id_laporan }}</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Diteruskan pada: {{ \Carbon\Carbon::parse($laporan->updated_at)->translatedFormat('d M Y, H:i') }} WIB</p>
                </div>
            </div>

            <div>
                @if($laporan->status == 'diteruskan')
                    <span class="bg-yellow-400 text-white text-xs font-extrabold px-4 py-2 rounded-full uppercase tracking-wider shadow-sm flex items-center shadow-yellow-200">
                        <i class="fas fa-clock mr-2"></i> Menunggu Penugasan
                    </span>
                @elseif($laporan->status == 'proses')
                    <span class="bg-indigo-500 text-white text-xs font-extrabold px-4 py-2 rounded-full uppercase tracking-wider shadow-sm flex items-center shadow-indigo-200">
                        <i class="fas fa-tools mr-2 animate-pulse"></i> Dalam Pengerjaan
                    </span>
                @elseif($laporan->status == 'selesai')
                    <span class="bg-green-500 text-white text-xs font-extrabold px-4 py-2 rounded-full uppercase tracking-wider shadow-sm flex items-center shadow-green-200">
                        <i class="fas fa-check-circle mr-2"></i> Menunggu Konfirmasi Progres
                    </span>
                @elseif($laporan->status == 'terkendala' || $laporan->status == 'perlu_perbaikan')
                     <span class="bg-red-500 text-white text-xs font-extrabold px-4 py-2 rounded-full uppercase tracking-wider shadow-sm flex items-center shadow-red-200">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Terkendala / Perlu Perbaikan
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden animasi-masuk delay-1">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-white relative">
                    <h3 class="font-bold text-gray-800 flex items-center text-sm">
                        <i class="fas fa-map-marker-alt text-red-500 mr-2 text-lg"></i> Lokasi Laporan
                    </h3>

                    <div class="flex space-x-2 text-gray-400">
                        <button id="btn-fs-detail" onclick="toggleFullscreenDetail()" class="hover:text-pupr-blue border border-gray-200 hover:bg-blue-50 rounded p-1.5 transition focus:outline-none" title="Layar Penuh">
                            <i class="fas fa-expand text-xs" id="icon-fs-detail"></i>
                        </button>

                        <div class="relative">
                            <button id="btn-layer-detail" onclick="toggleLayerMenuDetail()" class="hover:text-pupr-blue border border-gray-200 hover:bg-blue-50 rounded p-1.5 transition focus:outline-none" title="Ganti Tampilan Peta">
                                <i class="fas fa-layer-group text-xs"></i>
                            </button>
                            <div id="menu-layer-detail" class="hidden absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-xl border border-gray-100 z-[2000] p-2 origin-top-right transition-all">
                                <button onclick="gantiLayerDetail('standar')" class="w-full text-left px-3 py-2 text-[11px] font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue rounded-lg transition">
                                    <i class="fas fa-map mr-2"></i> Peta Jalan
                                </button>
                                <button onclick="gantiLayerDetail('satelit')" class="w-full text-left px-3 py-2 text-[11px] font-bold text-gray-600 hover:bg-blue-50 hover:text-pupr-blue rounded-lg transition">
                                    <i class="fas fa-satellite mr-2"></i> Citra Satelit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative w-full h-[350px] bg-gray-100 z-0" id="mapDetail">
                    <div class="absolute top-4 right-4 bg-white/95 backdrop-blur rounded-lg p-3 shadow-lg border border-gray-200 z-[1000] flex items-center space-x-4 transition-transform hover:scale-105">
                        <div>
                            <p class="text-[9px] font-extrabold text-pupr-blue uppercase tracking-widest mb-0.5">Titik Koordinat</p>
                            <p class="text-sm font-mono font-bold text-gray-800" id="teks-koordinat">{{ $laporan->lokasi_gps ?? '-6.5627, 107.7613' }}</p>
                        </div>
                        <button onclick="salinKoordinat()" class="w-9 h-9 rounded bg-gray-50 border border-gray-200 text-gray-500 hover:bg-blue-50 hover:text-pupr-blue hover:border-blue-200 flex items-center justify-center transition focus:outline-none active:scale-95" title="Salin Koordinat">
                            <i class="far fa-copy" id="icon-salin"></i>
                        </button>
                    </div>
                </div>

                <div class="bg-blue-50/50 p-4 flex items-start border-t border-gray-100 transition hover:bg-blue-50">
                    <i class="fas fa-location-arrow text-blue-500 mt-1 mr-3 animate-pulse"></i>
                    <p class="text-sm text-gray-700 font-medium">{{ $laporan->alamat_map }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden animasi-masuk delay-2">
                <div class="px-5 py-4 border-b border-gray-100 bg-white">
                    <h3 class="font-bold text-gray-800 flex items-center text-sm">
                        <i class="fas fa-images text-pupr-blue mr-2 text-lg"></i> Bukti Foto & Video Lapangan
                    </h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="galleryContainer">

                        @php
                            $urlFoto1 = \Illuminate\Support\Str::startsWith($laporan->foto_bukti, ['http://', 'https://']) ? $laporan->foto_bukti : asset('storage/' . $laporan->foto_bukti);
                            $urlFoto2 = \Illuminate\Support\Str::startsWith($laporan->video_bukti, ['http://', 'https://']) ? $laporan->video_bukti : asset('storage/' . $laporan->video_bukti);
                        @endphp

                        @if($laporan->foto_bukti)
                            <div onclick="openLightbox('{{ $urlFoto1 }}', 'image')" class="aspect-square rounded-xl overflow-hidden bg-gray-100 border border-gray-200 shadow-sm cursor-pointer relative group">
                                <img src="{{ $urlFoto1 }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Foto Bukti 1">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center">
                                    <i class="fas fa-search-plus text-white text-3xl opacity-0 group-hover:opacity-100 transform scale-50 group-hover:scale-100 transition-all drop-shadow-lg"></i>
                                </div>
                                <div class="absolute bottom-2 left-2 bg-black/60 backdrop-blur text-white text-[9px] font-bold px-2 py-1 rounded">Foto 1</div>
                            </div>
                        @else
                            <div class="aspect-square rounded-xl overflow-hidden bg-gray-50 border border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-image text-3xl mb-2"></i>
                                <span class="text-xs font-medium">Tidak ada foto</span>
                            </div>
                        @endif

                        @if($laporan->video_bukti)
                            <div onclick="openLightbox('{{ $urlFoto2 }}', 'image')" class="aspect-square rounded-xl overflow-hidden bg-gray-100 border border-gray-200 shadow-sm cursor-pointer relative group">
                                <img src="{{ $urlFoto2 }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Foto Bukti 2">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center">
                                    <i class="fas fa-search-plus text-white text-3xl opacity-0 group-hover:opacity-100 transform scale-50 group-hover:scale-100 transition-all drop-shadow-lg"></i>
                                </div>
                                <div class="absolute bottom-2 left-2 bg-black/60 backdrop-blur text-white text-[9px] font-bold px-2 py-1 rounded">Foto 2</div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 animasi-masuk delay-2">
                <h3 class="font-bold text-gray-800 flex items-center text-sm mb-5">
                    <i class="fas fa-align-left text-pupr-blue mr-2 text-lg"></i> Deskripsi & Informasi Pelapor
                </h3>

                <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4 rounded-r-xl mb-5 hover:bg-yellow-100/50 transition">
                    <p class="text-[10px] font-bold text-yellow-800 uppercase tracking-widest mb-1">Catatan Disposisi dari Pusat</p>
                    <p class="text-sm font-bold text-yellow-900">{{ $laporan->catatan_disposisi ?? 'Tidak ada instruksi khusus dari Admin Universal.' }}</p>
                </div>

                <div class="mb-5">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Deskripsi Aduan Masyarakat</p>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $laporan->deskripsi_laporan }}</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 hover:bg-gray-100/50 transition">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Nama Pelapor</p>
                    <p class="text-sm font-bold text-gray-800"><i class="fas fa-user-circle text-gray-400 mr-1"></i> {{ $laporan->pelapor->nama_lengkap ?? 'Anonim / Masyarakat Umum' }}</p>
                </div>
            </div>

        </div>

        <div class="lg:col-span-1 animasi-masuk delay-3">

            @if($laporan->status == 'selesai' || $laporan->status == 'terkendala')
                <div class="bg-green-600 rounded-t-2xl p-5 text-white shadow-md relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 opacity-10"><i class="fas fa-check-double text-8xl"></i></div>
                    <h3 class="text-sm font-bold flex items-center relative z-10"><i class="fas fa-clipboard-check mr-2"></i> Formulir Konfirmasi Pekerjaan</h3>
                    <p class="text-[10px] text-green-200 mt-1 uppercase tracking-wider relative z-10">Validasi Hasil Pekerjaan UPTD</p>
                </div>

                <div class="bg-white border border-gray-100 border-t-0 rounded-b-2xl shadow-sm p-6">
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Tim Pelaksana / Pekerja UPTD</label>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-800 font-semibold">
                            <i class="fas fa-hard-hat text-gray-400 mr-2"></i> {{ $laporan->pekerja->nama_lengkap ?? 'Nama Pekerja Tidak Ditemukan' }}
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Keterangan Hasil Pekerjaan</label>
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-700 leading-relaxed min-h-[80px]">
                            {{ $laporan->keterangan_hasil_pekerja ?? 'Pekerja tidak memberikan keterangan.' }}
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Foto/Video Progres</label>
                        <div class="grid grid-cols-2 gap-3">
                            @if(isset($laporan->foto_progres) && $laporan->foto_progres)
                                @php $urlFotoProgres = \Illuminate\Support\Str::startsWith($laporan->foto_progres, ['http://', 'https://']) ? $laporan->foto_progres : asset('storage/' . $laporan->foto_progres); @endphp
                                <div onclick="openLightbox('{{ $urlFotoProgres }}', 'image')" class="aspect-video rounded-lg overflow-hidden bg-gray-100 border border-gray-200 shadow-sm cursor-pointer relative group">
                                    <img src="{{ $urlFotoProgres }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Foto Progres">
                                </div>
                            @else
                                <div class="col-span-2 p-4 text-center border-2 border-dashed border-gray-300 rounded-lg text-gray-400 text-xs font-medium">
                                    Tidak ada lampiran progres
                                </div>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('admin_bidang.laporan.setujui_progres', $laporan->id) ?? '#' }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-extrabold text-sm py-3.5 rounded-lg shadow-sm transition transform hover:-translate-y-0.5 flex justify-center items-center">
                            Setujui Progres <i class="fas fa-check-circle ml-2"></i>
                        </button>
                    </form>

                    <form id="form-tolak-progres" action="{{ route('admin_bidang.laporan.tolak_progres', $laporan->id) ?? '#' }}" method="POST">
                        @csrf
                        <input type="hidden" name="alasan_penolakan" id="alasan_penolakan_progres">
                        <button type="button" onclick="konfirmasiTolakProgres()" class="w-full bg-red-50 hover:bg-red-500 text-red-600 hover:text-white border border-red-200 font-extrabold text-sm py-3 rounded-lg transition transform hover:-translate-y-0.5 flex justify-center items-center shadow-sm">
                            Tolak Progres (Minta Revisi) <i class="fas fa-times-circle ml-2"></i>
                        </button>
                    </form>
                </div>

            @else
                <div class="bg-[#1E3A8A] rounded-t-2xl p-5 text-white shadow-md relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 opacity-10"><i class="fas fa-file-signature text-8xl"></i></div>
                    <h3 class="text-sm font-bold flex items-center relative z-10"><i class="fas fa-clipboard-check mr-2"></i> Formulir Disposisi UPTD</h3>
                    <p class="text-[10px] text-blue-200 mt-1 uppercase tracking-wider relative z-10">Kirim tugas langsung ke Aplikasi Mobile</p>
                </div>

                <div class="bg-white border border-gray-100 border-t-0 rounded-b-2xl shadow-sm p-6">

                    <form id="form-disposisi" action="{{ route('admin_bidang.laporan.tugaskan', $laporan->id) }}" method="POST">
                        @csrf

                        <div class="mb-5 relative">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Pilih Tim Pelaksana / Pegawai UPTD <span class="text-red-500">*</span></label>

                            <select id="id_pekerja" name="id_pekerja" class="hidden" required {{ $laporan->status != 'diteruskan' ? 'disabled' : '' }}>
                                <option value="">-- Cari Tim Berdasarkan Wilayah... --</option>
                                @foreach($pekerja as $tim)
                                    <option value="{{ $tim->id }}"
                                            data-nama="{{ $tim->nama_lengkap }}"
                                            data-wilayah="{{ $tim->kantor_wilayah ?? 'Seluruh Wilayah Subang' }}"
                                            {{ $laporan->id_pekerja == $tim->id ? 'selected' : '' }}>
                                        {{ $tim->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="relative" id="custom-select-container">
                                <div id="custom-select-button" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-lg p-3 shadow-sm flex justify-between items-center transition duration-200 {{ $laporan->status != 'diteruskan' ? 'opacity-60 cursor-not-allowed bg-gray-100' : 'cursor-pointer hover:border-pupr-blue hover:bg-white' }}">
                                    <span id="custom-select-text" class="font-semibold text-gray-500 truncate mr-2">-- Pilih Pekerja UPTD... --</span>
                                    <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300" id="custom-select-icon"></i>
                                </div>

                                <div id="custom-select-dropdown" class="hidden absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] overflow-hidden transform opacity-0 transition-opacity duration-200">
                                    <div class="p-2 bg-gray-50 border-b border-gray-100">
                                        <div class="relative">
                                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                                            <input type="text" id="custom-select-search" class="w-full bg-white border border-gray-200 text-xs rounded-lg pl-8 pr-3 py-2 focus:outline-none focus:border-pupr-blue focus:ring-1 focus:ring-pupr-blue transition shadow-sm" placeholder="Cari nama atau wilayah kerja...">
                                        </div>
                                    </div>
                                    <ul id="custom-select-list" class="max-h-56 overflow-y-auto custom-scrollbar py-1">
                                        </ul>
                                </div>
                            </div>

                            @if($pekerja->isEmpty())
                                <p class="text-[10px] text-red-500 font-bold mt-2"><i class="fas fa-exclamation-triangle"></i> Belum ada akun Pekerja UPTD yang aktif di sistem.</p>
                            @endif
                        </div>

                        <div class="mb-5">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Prioritas Tugas</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="prioritas" value="Tinggi" class="peer sr-only" {{ ($laporan->prioritas ?? 'Tinggi') == 'Tinggi' ? 'checked' : '' }} {{ $laporan->status != 'diteruskan' ? 'disabled' : '' }}>
                                    <div class="text-center px-1 py-2.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-500 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-600 transition shadow-sm hover:bg-gray-50">
                                        <i class="fas fa-exclamation mr-1"></i> Tinggi
                                    </div>
                                </label>
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="prioritas" value="Sedang" class="peer sr-only" {{ ($laporan->prioritas ?? '') == 'Sedang' ? 'checked' : '' }} {{ $laporan->status != 'diteruskan' ? 'disabled' : '' }}>
                                    <div class="text-center px-1 py-2.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-500 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 peer-checked:text-yellow-600 transition shadow-sm hover:bg-gray-50">
                                        <i class="fas fa-angle-up mr-1"></i> Sedang
                                    </div>
                                </label>
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="prioritas" value="Rendah" class="peer sr-only" {{ ($laporan->prioritas ?? '') == 'Rendah' ? 'checked' : '' }} {{ $laporan->status != 'diteruskan' ? 'disabled' : '' }}>
                                    <div class="text-center px-1 py-2.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-500 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-600 transition shadow-sm hover:bg-gray-50">
                                        <i class="fas fa-angle-down mr-1"></i> Rendah
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Instruksi Tambahan</label>
                            <textarea name="instruksi_tambahan" rows="4" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-lg focus:ring-pupr-blue focus:border-pupr-blue block p-3 outline-none shadow-sm resize-none text-xs font-medium transition hover:border-blue-200" placeholder="Tulis rincian teknis pengerjaan lapangan..." {{ $laporan->status != 'diteruskan' ? 'readonly' : '' }}>{{ $laporan->instruksi_tambahan ?? '' }}</textarea>
                        </div>

                        @if($laporan->status == 'diteruskan')
                            <button type="button" onclick="konfirmasiDisposisi()" class="w-full bg-pupr-yellow hover:bg-yellow-500 text-white font-extrabold text-sm py-3.5 rounded-lg shadow-sm transition transform hover:-translate-y-0.5 flex justify-center items-center mb-3">
                                Kirim Penugasan UPTD <i class="fas fa-paper-plane ml-2"></i>
                            </button>
                        @else
                            <button type="button" disabled class="w-full bg-gray-100 text-gray-400 font-extrabold text-sm py-3.5 rounded-lg flex justify-center items-center mb-3 cursor-not-allowed border border-gray-200">
                                Laporan Sedang Dikerjakan Pekerja <i class="fas fa-lock ml-2"></i>
                            </button>
                        @endif
                    </form>

                    <div class="mt-4 pt-4 border-t border-gray-100">

                        @if($laporan->status == 'diteruskan')
                            <form id="form-kembalikan-pusat" action="{{ route('admin_bidang.laporan.kembalikan', $laporan->id) ?? '#' }}" method="POST">
                                @csrf
                                <input type="hidden" name="alasan_pengembalian" id="alasan_pengembalian_pusat">
                                <button type="button" onclick="konfirmasiKembalikanPusat()" class="w-full bg-[#313C59] hover:bg-[#1E293B] text-white font-bold text-sm py-3 rounded-lg transition transform hover:-translate-y-0.5 flex justify-center items-center shadow-sm">
                                    <i class="fas fa-reply mr-2"></i> Kembalikan ke Admin Universal
                                </button>
                            </form>
                            <p class="text-[9px] text-center text-gray-400 mt-2">Gunakan jika laporan ini bukan wewenang bidang Anda.</p>

                        @elseif($laporan->status == 'proses')
                            <form id="form-batalkan-tugas" action="{{ route('admin_bidang.laporan.batal_tugas', $laporan->id) ?? '#' }}" method="POST">
                                @csrf
                                <input type="hidden" name="alasan_pembatalan" id="alasan_pembatalan_tugas">
                                <button type="button" onclick="konfirmasiBatalkanTugas()" class="w-full bg-red-50 text-red-600 border border-red-200 hover:bg-red-500 hover:text-white font-bold text-sm py-3 rounded-lg transition transform hover:-translate-y-0.5 flex justify-center items-center shadow-sm">
                                    <i class="fas fa-times-circle mr-2"></i> Batalkan Penugasan Pekerja
                                </button>
                            </form>
                            <p class="text-[9px] text-center text-gray-400 mt-2">Menarik kembali tugas dari Pekerja UPTD.</p>
                        @endif

                    </div>

                </div>
            @endif
        </div>

    </div>
</div>

<div id="lightboxModal" class="fixed inset-0 z-[4000] hidden flex items-center justify-center transition-opacity">
    <div class="absolute inset-0 bg-gray-900/90 backdrop-blur-sm cursor-zoom-out" onclick="closeLightbox()"></div>
    <button onclick="closeLightbox()" class="absolute top-5 right-5 md:top-8 md:right-8 text-white hover:text-red-500 transition z-10 p-3 bg-black/20 rounded-full hover:bg-black/50 focus:outline-none">
        <i class="fas fa-times text-2xl drop-shadow-lg"></i>
    </button>
    <div class="relative z-10 max-w-5xl w-full max-h-[90vh] flex items-center justify-center p-4 pointer-events-none">
        <img id="lightboxImage" src="" class="hidden max-w-full max-h-[85vh] rounded-lg shadow-[0_0_40px_rgba(0,0,0,0.5)] pointer-events-auto border border-gray-700 transform scale-95 transition-transform duration-300">
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    // --- SCRIPT CUSTOM SELECT DROPDOWN (UI PREMIUM) ---
    document.addEventListener("DOMContentLoaded", function() {
        const nativeSelect = document.getElementById('id_pekerja');
        if(!nativeSelect) return; // Keluar jika form select tidak ada di halaman

        const customBtn = document.getElementById('custom-select-button');
        const customText = document.getElementById('custom-select-text');
        const customDropdown = document.getElementById('custom-select-dropdown');
        const customList = document.getElementById('custom-select-list');
        const customSearch = document.getElementById('custom-select-search');
        const customIcon = document.getElementById('custom-select-icon');

        let isSelectDisabled = nativeSelect.disabled;

        function initCustomSelect() {
            if(nativeSelect.value !== "") {
                let selectedOpt = nativeSelect.options[nativeSelect.selectedIndex];
                customText.innerHTML = `<span class="text-gray-800 font-bold">${selectedOpt.getAttribute('data-nama')}</span>`;
                customBtn.classList.add('border-blue-300', 'bg-white');
            }

            populateList('');

            if(isSelectDisabled) return;

            customBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleDropdown();
            });

            customSearch.addEventListener('input', function(e) {
                populateList(e.target.value.toLowerCase());
            });

            document.addEventListener('click', function(e) {
                if(!customBtn.contains(e.target) && !customDropdown.contains(e.target)) {
                    closeDropdown();
                }
            });
        }

        function toggleDropdown() {
            if(customDropdown.classList.contains('hidden')) {
                customDropdown.classList.remove('hidden');
                setTimeout(() => {
                    customDropdown.classList.remove('opacity-0');
                    customIcon.classList.add('rotate-180');
                    customSearch.focus();
                }, 10);
            } else {
                closeDropdown();
            }
        }

        function closeDropdown() {
            customDropdown.classList.add('opacity-0');
            customIcon.classList.remove('rotate-180');
            setTimeout(() => { customDropdown.classList.add('hidden'); }, 200);
        }

        function populateList(filterText) {
            customList.innerHTML = '';
            let hasResult = false;

            Array.from(nativeSelect.options).forEach((opt, index) => {
                if(index === 0) return;

                let val = opt.value;
                let nama = opt.getAttribute('data-nama');
                let wilayah = opt.getAttribute('data-wilayah');

                if(nama.toLowerCase().includes(filterText) || wilayah.toLowerCase().includes(filterText)) {
                    hasResult = true;
                    let li = document.createElement('li');
                    li.className = 'px-4 py-3 cursor-pointer transition border-b border-gray-50 last:border-0 relative flex flex-col group';

                    if(nativeSelect.value === val) {
                        li.classList.add('bg-blue-50');
                    } else {
                        li.classList.add('hover:bg-gray-50');
                    }

                    li.innerHTML = `
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold ${nativeSelect.value === val ? 'text-pupr-blue' : 'text-gray-800 group-hover:text-pupr-blue'}">${nama}</span>
                            ${nativeSelect.value === val ? '<i class="fas fa-check text-pupr-blue text-xs"></i>' : ''}
                        </div>
                        <span class="text-[10px] text-gray-500 font-medium mt-1 flex items-center"><i class="fas fa-map-marker-alt text-gray-300 mr-1.5"></i> ${wilayah}</span>
                    `;

                    li.addEventListener('click', () => {
                        nativeSelect.value = val;
                        customText.innerHTML = `<span class="text-gray-800 font-bold">${nama}</span>`;
                        customBtn.classList.add('border-blue-300', 'bg-white');
                        closeDropdown();
                        customSearch.value = '';
                        populateList('');
                    });

                    customList.appendChild(li);
                }
            });

            if(!hasResult) {
                customList.innerHTML = `
                    <div class="py-6 text-center text-gray-400 flex flex-col items-center">
                        <i class="fas fa-search-minus mb-2 text-lg"></i>
                        <span class="text-xs font-medium">Tim tidak ditemukan</span>
                    </div>`;
            }
        }

        initCustomSelect();
    });

    // 1. FUNGSI KONFIRMASI DISPOSISI TUGAS
    function konfirmasiDisposisi() {
        let selectPekerja = document.getElementById('id_pekerja');
        let nilaiPekerja = selectPekerja.value;

        if(nilaiPekerja === "") {
            Swal.fire({
                icon: 'warning', title: 'Tunggu Dulu!', text: 'Silakan pilih Tim Pelaksana / Pegawai UPTD terlebih dahulu.',
                confirmButtonColor: '#1E3A8A', customClass: { popup: 'rounded-2xl shadow-xl' }
            });
            return false;
        }

        let namaPekerja = selectPekerja.options[selectPekerja.selectedIndex].getAttribute('data-nama');

        Swal.fire({
            title: 'Kirim Penugasan?',
            html: `Anda akan menugaskan laporan ini kepada <br><b>${namaPekerja}</b>.`,
            icon: 'question', showCancelButton: true, confirmButtonColor: '#eab308', cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Kirim Tugas!', cancelButtonText: 'Batal', reverseButtons: true,
            customClass: { popup: 'rounded-2xl shadow-xl border border-gray-100' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Memproses...', text: 'Sedang mengirim instruksi penugasan...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => { Swal.showLoading(); }});
                document.getElementById('form-disposisi').submit();
            }
        });
    }

    // 2. FUNGSI KEMBALIKAN KE ADMIN UNIVERSAL (SALAH KAMAR)
    function konfirmasiKembalikanPusat() {
        Swal.fire({
            title: 'Kembalikan Laporan?',
            text: 'Tuliskan alasan mengapa laporan ini dikembalikan ke Admin Universal (Pusat):',
            input: 'textarea',
            inputPlaceholder: 'Contoh: Laporan ini bukan wewenang bidang SDA, mohon dialihkan...',
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#313C59', cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Kembalikan', cancelButtonText: 'Batal', reverseButtons: true,
            customClass: { popup: 'rounded-2xl shadow-xl' },
            inputValidator: (value) => { if (!value) return 'Alasan pengembalian wajib diisi!'; }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('alasan_pengembalian_pusat').value = result.value;
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => { Swal.showLoading(); }});
                document.getElementById('form-kembalikan-pusat').submit();
            }
        });
    }

    // 3. FUNGSI BATALKAN PENUGASAN PEKERJA (TARIK TUGAS)
    function konfirmasiBatalkanTugas() {
        Swal.fire({
            title: 'Batalkan Penugasan?',
            text: 'Pekerja akan menerima notifikasi bahwa tugas ini dibatalkan. Berikan alasannya:',
            input: 'textarea',
            inputPlaceholder: 'Contoh: Cuaca sedang ekstrem, tugas ditunda sementara...',
            icon: 'error', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Batalkan Tugas', cancelButtonText: 'Tutup', reverseButtons: true,
            customClass: { popup: 'rounded-2xl shadow-xl' },
            inputValidator: (value) => { if (!value) return 'Alasan pembatalan wajib diisi agar pekerja tahu!'; }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('alasan_pembatalan_tugas').value = result.value;
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => { Swal.showLoading(); }});
                document.getElementById('form-batalkan-tugas').submit();
            }
        });
    }

    // 4. FUNGSI TOLAK PROGRES PEKERJA
    function konfirmasiTolakProgres() {
        Swal.fire({
            title: 'Tolak Progres Pekerjaan?',
            text: 'Pekerja UPTD harus memperbarui progresnya. Berikan alasan penolakan:',
            input: 'textarea',
            inputPlaceholder: 'Contoh: Foto bukti kurang jelas, mohon lampirkan ulang dari sudut lain...',
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Tolak & Revisi', cancelButtonText: 'Batal', reverseButtons: true,
            customClass: { popup: 'rounded-2xl shadow-xl' },
            inputValidator: (value) => { if (!value) return 'Alasan penolakan wajib diisi agar pekerja bisa memperbaikinya!'; }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('alasan_penolakan_progres').value = result.value;
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => { Swal.showLoading(); }});
                document.getElementById('form-tolak-progres').submit();
            }
        });
    }

    // --- SCRIPT PETA LEAFLET ---
    let mapDetail, layerStandarDetail, layerSatelitDetail;
    document.addEventListener("DOMContentLoaded", function() {
        let koorString = "{{ $laporan->lokasi_gps ?? '-6.5627, 107.7613' }}";
        let koorArr = koorString.split(',');
        let lat = parseFloat(koorArr[0]); let lng = parseFloat(koorArr[1]);

        mapDetail = L.map('mapDetail').setView([lat, lng], 16);
        layerStandarDetail = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 20 });
        layerSatelitDetail = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });
        layerStandarDetail.addTo(mapDetail);

        let redPinIcon = L.divIcon({ className: 'bg-transparent', html: `<div style="text-shadow: 3px 5px 6px rgba(0,0,0,0.4);" class="text-red-500 text-[50px] hover:scale-110 transition-transform"><i class="fas fa-map-marker-alt"></i></div>`, iconSize: [40, 50], iconAnchor: [20, 50] });
        L.marker([lat, lng], {icon: redPinIcon}).addTo(mapDetail);
    });

    function toggleLayerMenuDetail() { document.getElementById('menu-layer-detail').classList.toggle('hidden'); }
    function gantiLayerDetail(jenis) {
        if(jenis === 'standar') { mapDetail.removeLayer(layerSatelitDetail); layerStandarDetail.addTo(mapDetail); }
        else { mapDetail.removeLayer(layerStandarDetail); layerSatelitDetail.addTo(mapDetail); }
        document.getElementById('menu-layer-detail').classList.add('hidden');
    }
    function toggleFullscreenDetail() {
        let elemenPeta = document.getElementById('mapDetail');
        if (!document.fullscreenElement) { elemenPeta.requestFullscreen().catch(err => { alert(`Gagal: ${err.message}`); }); }
        else { document.exitFullscreen(); }
    }
    document.addEventListener('fullscreenchange', (event) => {
        let iconBtn = document.getElementById('icon-fs-detail'); let divPeta = document.getElementById('mapDetail');
        if (document.fullscreenElement) { iconBtn.classList.replace('fa-expand', 'fa-compress'); divPeta.classList.remove('h-[350px]', 'rounded-2xl'); divPeta.style.height = '100vh'; }
        else { iconBtn.classList.replace('fa-compress', 'fa-expand'); divPeta.style.height = ''; divPeta.classList.add('h-[350px]', 'rounded-2xl'); }
        setTimeout(() => { mapDetail.invalidateSize(); }, 300);
    });
    document.addEventListener('click', function(event) {
        if(!event.target.closest('#btn-layer-detail') && !event.target.closest('#menu-layer-detail')) {
            let menu = document.getElementById('menu-layer-detail'); if(menu) menu.classList.add('hidden');
        }
    });

    function salinKoordinat() {
        let koordinat = document.getElementById('teks-koordinat').innerText;
        navigator.clipboard.writeText(koordinat).then(() => {
            let iconSalin = document.getElementById('icon-salin'); let tombolSalin = iconSalin.parentElement;
            iconSalin.className = 'fas fa-check text-green-600'; tombolSalin.classList.add('bg-green-50', 'border-green-200');
            setTimeout(() => { iconSalin.className = 'far fa-copy text-gray-500'; tombolSalin.classList.remove('bg-green-50', 'border-green-200'); }, 2000);
        });
    }

    function openLightbox(mediaUrl, type) {
        const modal = document.getElementById('lightboxModal'); const img = document.getElementById('lightboxImage');
        modal.classList.remove('hidden');
        if(type === 'image') { img.src = mediaUrl; img.classList.remove('hidden'); setTimeout(() => { img.classList.remove('scale-95'); img.classList.add('scale-100'); }, 10); }
    }
    function closeLightbox() {
        const modal = document.getElementById('lightboxModal'); const img = document.getElementById('lightboxImage');
        modal.classList.add('hidden'); img.className = 'hidden max-w-full max-h-[85vh] rounded-lg transform scale-95 transition-transform duration-300';
        setTimeout(() => { img.src = ""; }, 300);
    }
</script>
@endpush
