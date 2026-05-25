@extends('layouts.app_bidang')

@push('css')
<style>
    /* Animasi Elegan untuk Halaman Profil */
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

    /* Efek hover khusus pada baris log */
    .log-item-hover:hover { transform: translateX(5px); }
</style>
@endpush

@section('konten')
<div class="max-w-7xl mx-auto pb-10 relative">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 animasi-masuk">
        <div>
            <a href="{{ route('admin_bidang.beranda') }}" class="text-sm font-bold text-pupr-blue hover:underline mb-2 inline-block transition"><i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard</a>
            <h2 class="text-2xl font-extrabold text-gray-900">Detail Profil Karyawan</h2>
            <p class="text-sm text-gray-500">Informasi identitas dan riwayat aktivitas sistem.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <div class="lg:col-span-4 space-y-6">

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden text-center animasi-masuk delay-1">
                <div class="h-32 bg-[#1E3A8A] relative overflow-hidden flex items-center justify-center">
                    @if($user->foto_profil)
                        <img src="{{ asset('storage/' . $user->foto_profil) }}" class="absolute inset-0 w-full h-full object-cover opacity-50 blur-[2px] transition-all duration-500 hover:scale-105 hover:blur-0" alt="Cover Profil">
                    @endif
                </div>

                <div class="relative -mt-16 flex flex-col items-center z-10">
                    <div class="relative inline-block">
                        <form action="{{ route('admin_bidang.profil.foto') }}" method="POST" enctype="multipart/form-data" id="form-foto">
                            @csrf
                            <label for="foto_profil" class="cursor-pointer group relative block">
                                <div class="w-28 h-28 bg-white rounded-full p-1 shadow-md border border-gray-100 relative overflow-hidden transition transform hover:scale-105 duration-300">
                                    @if($user->foto_profil)
                                        <img src="{{ asset('storage/' . $user->foto_profil) }}" class="w-full h-full object-cover rounded-full" alt="Foto Profil">
                                    @else
                                        <div class="w-full h-full rounded-full bg-gray-200 flex items-center justify-center text-4xl text-gray-500 font-bold">
                                            {{ substr($user->nama_lengkap ?? 'K', 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="absolute inset-1 bg-black/50 rounded-full flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300">
                                        <i class="fas fa-camera text-white text-xl mb-1"></i>
                                        <span class="text-[9px] text-white font-bold tracking-wider">UBAH</span>
                                    </div>
                                </div>
                                <input type="file" name="foto_profil" id="foto_profil" class="hidden" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="validasiFoto(this)">
                            </label>
                        </form>

                        @if($user->foto_profil)
                        <form action="{{ route('admin_bidang.profil.foto.hapus') }}" method="POST" id="form-hapus-foto" class="absolute bottom-0 right-0 z-10">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="konfirmasiHapusFoto()" class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg border-2 border-white transition transform hover:scale-110" title="Hapus Foto Profil">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </form>
                        @endif
                    </div>

                    <p class="text-[9px] font-bold text-gray-400 mt-3 uppercase tracking-widest">JPG, PNG, WEBP • Max: 10MB</p>
                </div>

                <div class="px-6 pb-6 mt-3">
                    <h3 class="text-xl font-extrabold text-gray-900">{{ $user->nama_lengkap ?? 'Karyawan Bidang' }}</h3>
                    <p class="text-sm font-bold text-pupr-blue mt-1">{{ str_replace('_', ' ', strtoupper($user->peran ?? 'KARYAWAN BIDANG')) }}</p>

                    <div class="flex justify-center gap-2 mt-4 mb-6">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold tracking-wider">AKTIF</span>
                        <span class="bg-blue-50 text-blue-600 border border-blue-100 px-3 py-1 rounded-full text-[10px] font-bold tracking-wider">KARYAWAN</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 relative animasi-masuk delay-2">
                <div class="flex justify-between items-center mb-5">
                    <h4 class="font-bold text-gray-800">Kontak & Lokasi</h4>
                    <button onclick="bukaModalEditProfil()" class="text-xs font-bold text-pupr-blue hover:underline flex items-center transition hover:scale-105">
                        <i class="fas fa-edit mr-1"></i> Edit Data
                    </button>
                </div>

                <div class="space-y-5">
                    <div class="flex items-start group">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-pupr-blue flex items-center justify-center mr-4 shrink-0 group-hover:bg-pupr-blue group-hover:text-white transition-colors duration-300"><i class="fas fa-envelope"></i></div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Email Sistem</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->email ?? 'Belum diatur' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start group">
                        <div class="w-10 h-10 rounded-lg bg-green-50 text-green-500 flex items-center justify-center mr-4 shrink-0 group-hover:bg-green-500 group-hover:text-white transition-colors duration-300"><i class="fab fa-whatsapp text-xl"></i></div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Nomor WhatsApp</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->nomor_hp ?? 'Belum diatur' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start group">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-pupr-blue flex items-center justify-center mr-4 shrink-0 group-hover:bg-pupr-blue group-hover:text-white transition-colors duration-300"><i class="fas fa-building"></i></div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Kantor Wilayah</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->kantor_wilayah ?? 'Bidang SIGAP Subang' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8 space-y-6">

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 lg:p-8 animasi-masuk delay-3">
                <div class="flex justify-between items-start mb-6">
                    <h4 class="text-lg font-bold text-gray-800">Informasi Otoritas</h4>
                    <i class="fas fa-shield-check text-green-500 text-xl animate-pulse" title="Terverifikasi"></i>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="group hover:bg-gray-50 p-2 rounded-lg transition">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">ID Pengguna Sistem</p>
                        <p class="text-base font-semibold text-gray-800 font-mono bg-gray-50 px-2 py-1 rounded inline-block border border-gray-100 group-hover:border-blue-200 transition">SGP-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="group hover:bg-gray-50 p-2 rounded-lg transition">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Peran Aplikasi</p>
                        <p class="text-base font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $user->peran ?? 'Karyawan Bidang') }}</p>
                    </div>
                    <div class="group hover:bg-gray-50 p-2 rounded-lg transition">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tanggal Pendaftaran Akun</p>
                        <p class="text-base font-semibold text-gray-800">{{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 lg:p-8 animasi-masuk delay-4">
                <div class="flex justify-between items-center mb-6">
                    <h4 class="text-lg font-bold text-gray-800">Aktivitas Terakhir Anda</h4>
                    <button onclick="bukaModalLog()" class="text-xs font-bold text-pupr-blue hover:underline transition hover:scale-105">Lihat Semua Riwayat</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-gray-50 text-sm">
                            @forelse($aktivitas_terbaru ?? [] as $aktivitas)
                            <tr class="hover:bg-blue-50/50 transition duration-300">
                                <td class="py-4 pr-4">
                                    <p class="font-bold text-gray-800">{{ $aktivitas->aktivitas }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Log ID #{{ $aktivitas->id }}</p>
                                </td>
                                <td class="py-4 text-gray-500 text-xs text-right">
                                    <p><i class="far fa-clock mr-1"></i>{{ $aktivitas->created_at->diffForHumans() }}</p>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center py-8 text-gray-400 text-xs">
                                    <i class="fas fa-clipboard-list text-2xl mb-2 opacity-50 block"></i>
                                    Belum ada aktivitas tercatat. Lakukan aksi di menu laporan!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-edit-profil" class="fixed inset-0 z-[2000] hidden items-center justify-center">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="tutupModalEditProfil()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden flex flex-col animasi-masuk">

        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800 text-lg">Edit Profil Karyawan</h3>
            <button onclick="tutupModalEditProfil()" class="text-gray-400 hover:text-red-500 transition text-xl focus:outline-none">&times;</button>
        </div>

        <div class="p-6">
            <form action="{{ route('admin_bidang.profil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase mb-1.5 block">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" value="{{ $user->nama_lengkap }}" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:border-pupr-blue focus:ring-1 focus:ring-pupr-blue outline-none shadow-sm transition" required>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase mb-1.5 block">Alamat Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ $user->email }}" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:border-pupr-blue focus:ring-1 focus:ring-pupr-blue outline-none shadow-sm transition" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase mb-1.5 flex items-center"><i class="fab fa-whatsapp text-green-500 mr-1.5 text-sm"></i> Nomor WhatsApp</label>
                            <input type="text" name="nomor_hp" value="{{ $user->nomor_hp }}" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none shadow-sm transition" placeholder="Contoh: 081234567890">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase mb-1.5 block">Kantor Wilayah</label>
                            <input type="text" name="kantor_wilayah" value="{{ $user->kantor_wilayah ?? 'Bidang SIGAP Subang' }}" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:border-pupr-blue focus:ring-1 focus:ring-pupr-blue outline-none shadow-sm transition">
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-5">
                    <button type="button" onclick="tutupModalEditProfil()" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-bold transition shadow-sm">Batal</button>
                    <button type="submit" class="px-8 py-2.5 bg-[#1E3A8A] hover:bg-blue-800 text-white rounded-lg text-sm font-bold transition shadow-md flex items-center hover:shadow-lg transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal-log" class="fixed inset-0 z-[2000] hidden items-center justify-center">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="tutupModalLog()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden flex flex-col max-h-[85vh] animasi-masuk">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Seluruh Riwayat Aktivitas</h3>
                <p class="text-xs text-gray-500">Mencatat seluruh jejak rekam karyawan di dalam sistem.</p>
            </div>
            <button onclick="tutupModalLog()" class="text-gray-400 hover:text-red-500 transition text-xl focus:outline-none">&times;</button>
        </div>

        <div class="p-6 overflow-y-auto flex-1">
            <ul class="space-y-4 border-l-2 border-blue-100 ml-3 py-2">
                @forelse($semua_aktivitas ?? [] as $log)
                <li class="relative pl-6 hover:bg-blue-50/50 p-3 rounded-r-lg transition-all duration-300 flex justify-between items-start group log-item-hover animasi-masuk" style="animation-delay: {{ $loop->index * 0.05 }}s;">
                    <div>
                        <span class="absolute -left-[11px] top-4 w-5 h-5 rounded-full bg-pupr-blue border-4 border-white shadow-sm transition group-hover:scale-110"></span>
                        <p class="text-sm font-bold text-gray-800">{{ $log->aktivitas }}</p>
                        <p class="text-[10px] font-semibold text-gray-500 mt-1 uppercase tracking-wider"><i class="far fa-clock mr-1"></i> {{ $log->created_at->format('d M Y - H:i') }} WIB <span class="ml-2 px-1.5 py-0.5 bg-blue-100 text-blue-600 rounded">{{ $log->kategori }}</span></p>
                    </div>

                    <form action="{{ route('admin_bidang.profil.log.hapus_satu', $log->id) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition duration-300">
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
            <p class="text-xs text-gray-500"><i class="fas fa-info-circle mr-1 text-blue-500"></i> Data yang dihapus akan hilang permanen.</p>
            <form id="form-hapus-semua-log" action="{{ route('admin_bidang.profil.log.hapus') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" onclick="konfirmasiHapusSemuaLog()" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-500 hover:text-white border border-red-100 hover:border-red-500 rounded-lg text-xs font-bold transition shadow-sm flex items-center hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus Seluruh Riwayat
                </button>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
    function validasiFoto(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB

            if (file.size > maxSize) {
                Swal.fire({
                    title: 'Ukuran Terlalu Besar!',
                    text: 'Ukuran foto maksimal adalah 10MB.',
                    icon: 'error',
                    confirmButtonColor: '#1E3A8A',
                    confirmButtonText: 'Mengerti',
                    customClass: { popup: 'rounded-2xl shadow-xl', title: 'font-bold', confirmButton: 'px-6 py-2.5 rounded-lg text-sm font-bold shadow-md' }
                });
                input.value = '';
            } else {
                document.getElementById('form-foto').submit();
            }
        }
    }

    // Modal Control yang lebih smooth
    function bukaModalLog() {
        let modal = document.getElementById('modal-log');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function tutupModalLog() {
        let modal = document.getElementById('modal-log');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function bukaModalEditProfil() {
        let modal = document.getElementById('modal-edit-profil');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function tutupModalEditProfil() {
        let modal = document.getElementById('modal-edit-profil');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function konfirmasiHapusFoto() {
        Swal.fire({
            title: 'Hapus Foto Profil?',
            text: "Foto akan dikembalikan ke inisial nama bawaan.",
            icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#9ca3af', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true,
            customClass: { popup: 'rounded-2xl shadow-xl', title: 'font-bold', confirmButton: 'rounded-lg text-sm font-bold', cancelButton: 'rounded-lg text-sm font-bold' }
        }).then((result) => { if (result.isConfirmed) document.getElementById('form-hapus-foto').submit(); });
    }

    function konfirmasiHapusSemuaLog() {
        tutupModalLog();
        Swal.fire({
            title: 'Hapus Seluruh Riwayat?',
            text: "Peringatan: Riwayat aktivitas akan dihapus secara permanen dari database.",
            icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#9ca3af', confirmButtonText: 'Ya, Hapus Permanen!', cancelButtonText: 'Batal', reverseButtons: true,
            customClass: { popup: 'rounded-2xl shadow-xl border border-red-100', title: 'font-bold', confirmButton: 'rounded-lg text-sm font-bold shadow-md', cancelButton: 'rounded-lg text-sm font-bold' }
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('form-hapus-semua-log').submit();
            else bukaModalLog();
        });
    }

    function konfirmasiHapusSatuLog(btn) {
        tutupModalLog();
        Swal.fire({
            title: 'Hapus Catatan Ini?',
            text: "Catatan aktivitas ini akan dihapus permanen.",
            icon: 'question',
            showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#9ca3af', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal', reverseButtons: true,
            customClass: { popup: 'rounded-2xl shadow-xl', title: 'font-bold', confirmButton: 'rounded-lg text-sm font-bold shadow-md', cancelButton: 'rounded-lg text-sm font-bold' }
        }).then((result) => {
            if (result.isConfirmed) btn.closest('form').submit();
            else bukaModalLog();
        });
    }

    @if(session('sukses'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('sukses') }}', showConfirmButton: false, timer: 2500, customClass: { popup: 'rounded-2xl shadow-xl border border-gray-100' }});
        });
    @endif
</script>
@endpush
@endsection
