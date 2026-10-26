@extends('layouts.app_admin_jalan')

@section('title', 'Detail Laporan')
@section('page_title', 'Detail Laporan')
@section('page_subtitle', 'Informasi lengkap dan pengelolaan status laporan')

@section('content')

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin_jalan.beranda') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
    <i class="fas fa-chevron-right text-xs"></i>
    <a href="{{ route('admin_jalan.laporan.indeks') }}" class="hover:text-blue-600 transition-colors">Laporan</a>
    <i class="fas fa-chevron-right text-xs"></i>
    <span class="text-gray-700 font-semibold">{{ $laporan->id_laporan }}</span>
</nav>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ─── KOLOM KIRI: Info Laporan ────────────────────────────────────── --}}
    <div class="xl:col-span-2 space-y-6">

        {{-- Header Laporan --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-blue-700 font-bold text-sm font-mono bg-blue-50 px-2.5 py-1 rounded-lg">
                            {{ $laporan->id_laporan }}
                        </span>
                        <span class="text-sm font-bold px-3 py-1 rounded-full {{ $laporan->kelas_badge_status }}">
                            {{ $laporan->label_status }}
                        </span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $laporan->judul_laporan ?? 'Laporan Tanpa Judul' }}
                    </h2>
                    <p class="text-gray-400 text-sm mt-1">
                        <i class="fas fa-calendar-alt mr-1.5"></i>
                        Dilaporkan: {{ $laporan->created_at?->isoFormat('dddd, D MMMM Y HH:mm') }} WIB
                    </p>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-50">
                <div>
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Jenis Kerusakan</p>
                    <p class="text-gray-900 text-sm font-medium mt-1">
                        {{ \App\Models\LaporanKeluhan::JENIS_KERUSAKAN[$laporan->jenis_kerusakan] ?? $laporan->jenis_kerusakan }}
                    </p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide">Lokasi / Alamat</p>
                    <p class="text-gray-900 text-sm font-medium mt-1">
                        <i class="fas fa-map-marker-alt mr-1 text-red-400"></i>
                        {{ $laporan->alamat_map ?? 'Lokasi tidak tersedia' }}
                    </p>
                    @if($laporan->lokasi_gps && $laporan->lokasi_gps !== 'tidak diketahui')
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $laporan->lokasi_gps }}"
                       target="_blank"
                       class="text-blue-500 text-xs hover:underline">
                        <i class="fas fa-external-link-alt mr-1"></i>Buka di Google Maps
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Deskripsi Lengkap --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-gray-900 font-bold mb-3 flex items-center gap-2">
                <i class="fas fa-align-left text-blue-500"></i>
                Deskripsi Laporan
            </h3>
            <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">
                {{ $laporan->deskripsi_laporan ?? 'Tidak ada deskripsi.' }}
            </p>
        </div>

        {{-- Foto Bukti Pelapor --}}
        @if(count($laporan->foto_bukti_urls) > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-gray-900 font-bold mb-4 flex items-center gap-2">
                <i class="fas fa-image text-blue-500"></i>
                Foto Bukti Pelapor ({{ count($laporan->foto_bukti_urls) }})
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($laporan->foto_bukti_urls as $index => $fotoUrl)
                    <div class="rounded-xl overflow-hidden border border-gray-100">
                        <img src="{{ $fotoUrl }}"
                             alt="Foto bukti {{ $index + 1 }} laporan {{ $laporan->id_laporan }}"
                             class="w-full h-64 object-cover cursor-pointer hover:opacity-95 transition-opacity"
                             onclick="window.open(this.src, '_blank')">
                    </div>
                @endforeach
            </div>
            <p class="text-gray-400 text-xs mt-2 text-center">
                <i class="fas fa-info-circle mr-1"></i>Klik foto untuk memperbesar
            </p>
        </div>
        @endif

        {{-- Foto Survey dan Penanganan --}}
        @if(count($laporan->foto_penanganan_urls) > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-gray-900 font-bold mb-4 flex items-center gap-2">
                <i class="fas fa-tools text-emerald-500"></i>
                Foto Survey / Penanganan ({{ count($laporan->foto_penanganan_urls) }})
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($laporan->foto_penanganan_urls as $index => $fotoUrl)
                    <div class="rounded-xl overflow-hidden border border-gray-100">
                        <img src="{{ $fotoUrl }}"
                             alt="Foto survey atau penanganan {{ $index + 1 }}"
                             class="w-full h-64 object-cover cursor-pointer hover:opacity-95 transition-opacity"
                             onclick="window.open(this.src, '_blank')">
                    </div>
                @endforeach
            </div>
            <p class="text-gray-400 text-xs mt-2 text-center">
                <i class="fas fa-info-circle mr-1"></i>Klik foto untuk memperbesar
            </p>
        </div>
        @endif

        {{-- Catatan / Alasan Penolakan --}}
        @if($laporan->catatan_admin || $laporan->alasan_penolakan)
        @php
            $ditolak = $laporan->status === 'ditolak';
        @endphp
        <div class="{{ $ditolak ? 'bg-red-50 border-red-100' : 'bg-blue-50 border-blue-100' }} rounded-2xl border p-5">
            <h3 class="{{ $ditolak ? 'text-red-800' : 'text-blue-800' }} font-bold mb-2 flex items-center gap-2">
                <i class="fas {{ $ditolak ? 'fa-times-circle' : 'fa-comment-dots' }}"></i>
                {{ $ditolak ? 'Alasan Penolakan' : 'Catatan Admin' }}
            </h3>
            <p class="{{ $ditolak ? 'text-red-700' : 'text-blue-700' }} text-sm">
                {{ $laporan->alasan_penolakan ?? $laporan->catatan_admin }}
            </p>
        </div>
        @endif

        @if($laporan->lokasi_gps && $laporan->lokasi_gps !== 'tidak diketahui')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-gray-900 font-bold mb-4 flex items-center gap-2">
                <i class="fas fa-map-marked-alt text-blue-500"></i>
                Lokasi di Peta
            </h3>
            <div id="peta-detail" class="w-full h-64 rounded-xl overflow-hidden border border-gray-200 bg-slate-100"></div>
        </div>
        @endif
    </div>

    {{-- ─── KOLOM KANAN: Pelapor & Update Status ─────────────────────── --}}
    <div class="space-y-6">

        {{-- Info Pelapor --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-gray-900 font-bold mb-4 flex items-center gap-2">
                <i class="fas fa-user text-blue-500"></i>
                Data Pelapor
            </h3>
            @if($laporan->pelapor)
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    @if($laporan->pelapor->foto_profil)
                        <img src="{{ asset('storage/' . $laporan->pelapor->foto_profil) }}" alt="Foto pelapor" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                    @else
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-lg">
                            {{ strtoupper(substr($laporan->pelapor->nama_lengkap, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="text-gray-900 font-semibold text-sm">{{ $laporan->pelapor->nama_lengkap }}</p>
                        <p class="text-gray-400 text-xs">Masyarakat</p>
                    </div>
                </div>
                <div class="space-y-2 pt-2 border-t border-gray-50">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-envelope w-4 text-gray-400"></i>
                        {{ $laporan->pelapor->email ?? '-' }}
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-phone w-4 text-gray-400"></i>
                        {{ $laporan->pelapor->nomor_hp ?? '-' }}
                    </div>
                </div>
            </div>
            @else
            <p class="text-gray-400 text-sm">Data pelapor tidak tersedia.</p>
            @endif
        </div>

        {{-- Update Status --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-gray-900 font-bold mb-4 flex items-center gap-2">
                <i class="fas fa-edit text-blue-500"></i>
                Update Status Laporan
            </h3>

            <form action="{{ route('admin_jalan.laporan.update_status', $laporan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Pilih Status --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Status Baru <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="statusSelect"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="toggleCatatanField(this.value)">
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}"
                                    {{ old('status', $laporan->status) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="foto_penanganan"
                           class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Foto Survey / Penanganan
                    </label>
                    <input type="file" name="foto_penanganan[]" id="foto_penanganan"
                           accept="image/jpeg,image/png,image/webp" multiple
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('foto_penanganan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('foto_penanganan.*')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-400 text-xs mt-1">
                        Opsional, maksimal 10 foto sekali unggah, 5 MB per foto. Foto baru ditambahkan ke dokumentasi yang sudah ada.
                    </p>
                </div>

                {{-- Catatan / Alasan --}}
                <div class="mb-4">
                    <label for="catatan"
                           class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        <span id="catatanLabel">Catatan Admin</span>
                        <span id="catatanRequired" class="text-red-500 hidden"> *</span>
                    </label>
                    <textarea name="catatan" id="catatan" rows="4"
                              placeholder="Tambahkan catatan atau alasan..."
                              class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('catatan', $laporan->catatan_admin ?? $laporan->alasan_penolakan) }}</textarea>
                    @error('catatan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-400 text-xs mt-1">
                        <span id="catatanHint">Opsional. Catatan ini dapat dilihat oleh pelapor.</span>
                    </p>
                </div>

                <button type="submit"
                        class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-sm active:scale-[0.98]">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan Status
                </button>
            </form>
        </div>

        {{-- Tombol Kembali --}}
        <a href="{{ route('admin_jalan.laporan.indeks') }}"
           class="flex items-center justify-center gap-2 w-full py-3 bg-gray-100 text-gray-700 font-semibold text-sm rounded-xl hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Daftar Laporan
        </a>
    </div>
</div>

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    function toggleCatatanField(status) {
        const label     = document.getElementById('catatanLabel');
        const required  = document.getElementById('catatanRequired');
        const hint      = document.getElementById('catatanHint');
        const catatan   = document.getElementById('catatan');

        if (status === 'ditolak') {
            label.textContent = 'Alasan Penolakan';
            required.classList.remove('hidden');
            hint.textContent  = 'Wajib diisi. Jelaskan alasan penolakan kepada pelapor.';
            catatan.placeholder = 'Jelaskan alasan laporan ini ditolak...';
        } else if (status === 'dalam_perbaikan') {
            label.textContent = 'Update Progres';
            required.classList.add('hidden');
            hint.textContent  = 'Opsional. Informasikan perkembangan perbaikan kepada pelapor.';
            catatan.placeholder = 'Contoh: Tim teknisi sedang dalam perjalanan ke lokasi...';
        } else {
            label.textContent = 'Catatan Admin';
            required.classList.add('hidden');
            hint.textContent  = 'Opsional. Catatan ini dapat dilihat oleh pelapor.';
            catatan.placeholder = 'Tambahkan catatan atau alasan...';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sel = document.getElementById('statusSelect');
        if (sel) toggleCatatanField(sel.value);

        const peta = document.getElementById('peta-detail');
        if (peta && window.L) {
            const lat = @json($laporan->koordinat['lat']);
            const lng = @json($laporan->koordinat['lng']);
            if (lat !== null && lng !== null) {
                const mapDetail = L.map('peta-detail').setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(mapDetail);
                L.marker([lat, lng]).addTo(mapDetail);
                setTimeout(() => mapDetail.invalidateSize(), 250);
            }
        }
    });
</script>
@endpush

@endsection
