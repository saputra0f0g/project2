@extends('layouts.app_admin_jalan')

@section('title', 'Kelola Laporan')
@section('page_title', 'Kelola Laporan Jalan')
@section('page_subtitle', 'Verifikasi dan kelola laporan kerusakan infrastruktur jalan')

@section('content')

{{-- ─── FILTER BAR ──────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin_jalan.laporan.indeks') }}"
      class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <div class="flex flex-wrap gap-3 items-end">

        {{-- Search --}}
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Cari</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Kode laporan, judul, alamat..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>

        {{-- Filter Status --}}
        <div class="min-w-[160px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Status</label>
            <select name="status"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filter Jenis --}}
        <div class="min-w-[180px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Jenis Kerusakan</label>
            <select name="jenis"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Jenis</option>
                @foreach($jenisOptions as $value => $label)
                    <option value="{{ $value }}" {{ request('jenis') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Tombol Filter --}}
        <div class="flex gap-2">
            <button type="submit"
                    class="px-5 py-2.5 bg-blue-600 text-white font-semibold text-sm rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                <i class="fas fa-filter mr-1.5"></i> Filter
            </button>
            @if(request()->hasAny(['search','status','jenis']))
                <a href="{{ route('admin_jalan.laporan.indeks') }}"
                   class="px-4 py-2.5 bg-gray-100 text-gray-600 font-semibold text-sm rounded-xl hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>

        {{-- Ekspor CSV --}}
        <div class="ml-auto">
            <a href="{{ route('admin_jalan.laporan.ekspor_csv', request()->query()) }}"
               class="flex items-center gap-2 px-5 py-2.5 bg-emerald-500 text-white font-semibold text-sm rounded-xl hover:bg-emerald-600 transition-colors shadow-sm">
                <i class="fas fa-download"></i>
                Ekspor CSV
            </a>
        </div>
    </div>
</form>

{{-- ─── TABEL LAPORAN ───────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
        <div>
            <h2 class="text-gray-900 font-bold">Daftar Laporan</h2>
            <p class="text-gray-400 text-xs mt-0.5">
                Menampilkan {{ $laporan->firstItem() ?? 0 }}–{{ $laporan->lastItem() ?? 0 }}
                dari {{ $laporan->total() }} laporan
            </p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-6 py-3">Kode / Tanggal</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Laporan</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Pelapor</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Jenis</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Status</th>
                    <th class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($laporan as $item)
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <p class="text-blue-700 font-bold text-sm font-mono">{{ $item->id_laporan }}</p>
                        <p class="text-gray-400 text-xs">{{ $item->created_at?->format('d M Y') }}</p>
                    </td>
                    <td class="px-4 py-4 max-w-[200px]">
                        <p class="text-gray-900 font-semibold text-sm truncate">
                            {{ $item->judul_laporan ?? 'Laporan Tanpa Judul' }}
                        </p>
                        <p class="text-gray-400 text-xs truncate">
                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $item->alamat_map ?? '-' }}
                        </p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-gray-900 text-sm font-medium">{{ $item->pelapor?->nama_lengkap ?? '-' }}</p>
                        <p class="text-gray-400 text-xs">{{ $item->pelapor?->nomor_hp ?? '-' }}</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-gray-600 text-xs font-medium">
                            {{ \App\Models\LaporanKeluhan::JENIS_KERUSAKAN[$item->jenis_kerusakan] ?? $item->jenis_kerusakan }}
                        </p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $item->kelas_badge_status }}">
                            {{ $item->label_status }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <a href="{{ route('admin_jalan.laporan.detail', $item->id) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="text-gray-300">
                            <i class="fas fa-clipboard-list text-5xl mb-3"></i>
                            <p class="text-gray-400 font-medium">Tidak ada laporan ditemukan</p>
                            <p class="text-gray-300 text-sm">Coba ubah filter pencarian Anda</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($laporan->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between text-sm text-gray-500 gap-4">
        <p class="text-gray-500 text-sm">
            Menampilkan <span class="font-bold text-gray-800">{{ $laporan->firstItem() }}-{{ $laporan->lastItem() }}</span> dari <span class="font-bold text-gray-800">{{ number_format($laporan->total(), 0, ',', '.') }}</span> laporan
        </p>
        <div>
            {{ $laporan->links('pagination::tailwind') }}
        </div>
    </div>
    @endif
</div>

@endsection
