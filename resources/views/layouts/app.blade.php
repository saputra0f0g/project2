<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGAP PUPR - Admin Universal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pupr: {
                            blue: '#1E3A8A',
                            yellow: '#FACC15',
                            lightbg: '#F8FAFC'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
    @stack('css')
</head>
<body class="flex">

    @php
        // Mengambil data notifikasi khusus untuk user yang sedang login
        $semuaNotif = \App\Models\Notifikasi::where('user_id', Auth::id())->latest()->get();
        $notifBelumDibaca = $semuaNotif->where('is_read', false)->count();
        $notifDropdown = $semuaNotif->take(5); // Hanya tampilkan 5 terbaru di dropdown
    @endphp

    <aside class="w-64 bg-white min-h-screen border-r border-gray-100 flex flex-col fixed left-0 top-0 h-full z-20 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
        <div class="p-8 flex items-center justify-center">
            <img src="{{ asset('gambar/puprsigap1.png') }}" alt="Logo PUPR" class="w-52 object-contain">
        </div>

        <nav class="flex-1 mt-4 flex flex-col space-y-1">
            <a href="{{ route('admin_universal.beranda') }}" class="flex items-center px-8 py-3.5 transition-colors duration-200 {{ request()->routeIs('admin_universal.beranda') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-medium' }}">
                <i class="fas fa-th-large w-6 text-center mr-3 text-lg"></i> Dashboard
            </a>
            <a href="{{ route('admin_universal.statistik') }}" class="flex items-center px-8 py-3.5 transition-colors duration-200 {{ request()->routeIs('admin_universal.statistik') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-medium' }}">
                <i class="fas fa-chart-pie w-6 text-center mr-3 text-lg"></i> Statistik
            </a>
            <a href="{{ route('admin_universal.laporan') }}" class="flex items-center px-8 py-3.5 transition-colors duration-200 {{ request()->routeIs('admin_universal.laporan*') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-medium' }}">
                <i class="fas fa-file-alt w-6 text-center mr-3 text-lg"></i> Kelola Laporan
            </a>
            <a href="{{ route('admin_universal.bidang') }}" class="flex items-center px-8 py-3.5 transition-colors duration-200 {{ request()->routeIs('admin_universal.bidang') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-medium' }}">
                <i class="fas fa-network-wired w-6 text-center mr-3 text-lg"></i> Kelola Bidang
            </a>
            <a href="{{ route('admin_universal.pengguna') }}" class="flex items-center px-8 py-3.5 transition-colors duration-200 {{ request()->routeIs('admin_universal.pengguna') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-medium' }}">
                <i class="fas fa-users w-6 text-center mr-3 text-lg"></i> Kelola Pengguna
            </a>
            </nav>

        <div class="p-6 mt-auto">
            <a href="{{ route('admin_universal.peta') }}" class="w-full bg-pupr-yellow hover:bg-yellow-500 text-white font-bold py-3.5 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center mb-8 active:scale-95">
                <i class="fas fa-map-marked-alt mr-2"></i> Buka Peta Wilayah
            </a>
        </div>
    </aside>

    <main class="flex-1 ml-64 min-h-screen flex flex-col">

        <header class="bg-white border-b border-gray-100 px-8 py-4 flex justify-between items-center sticky top-0 z-[1500]">
            <div class="relative w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" placeholder="Cari laporan atau wilayah..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-pupr-blue focus:ring-1 focus:ring-pupr-blue transition">
            </div>

            <div class="flex items-center space-x-5 relative">

                <div class="relative">
                    <button id="btn-notif" onclick="toggleHeaderMenu('menu-notif')" class="text-gray-400 hover:text-pupr-blue transition relative focus:outline-none p-1">
                        <i class="far fa-bell text-xl"></i>
                        @if($notifBelumDibaca > 0)
                            <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-white animate-pulse">{{ $notifBelumDibaca }}</span>
                        @endif
                    </button>

                    <div id="menu-notif" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden transform transition-all origin-top-right">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                            <span class="text-sm font-bold text-gray-700">Notifikasi Baru</span>
                            <a href="{{ route('admin_universal.notifikasi.baca_semua') }}" class="text-[10px] font-bold text-pupr-blue hover:underline">Tandai dibaca</a>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @forelse($notifDropdown as $notif)
                                <a href="{{ route('admin_universal.notifikasi.klik', $notif->id) }}" class="block px-4 py-3 border-b border-gray-50 transition border-l-4 {{ $notif->is_read ? 'bg-white border-transparent hover:bg-gray-50' : 'bg-blue-50/50 border-blue-500 hover:bg-blue-50' }}">
                                    <p class="text-xs {{ $notif->is_read ? 'text-gray-600 font-medium' : 'text-gray-900 font-bold' }} mb-0.5">{{ $notif->judul }}</p>
                                    <p class="text-[10px] {{ $notif->is_read ? 'text-gray-400' : 'text-gray-600' }}">{{ $notif->pesan }}</p>
                                    <p class="text-[9px] text-gray-400 mt-1 font-medium"><i class="far fa-clock"></i> {{ $notif->created_at->diffForHumans() }}</p>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-gray-400 text-xs">Belum ada notifikasi baru.</div>
                            @endforelse
                        </div>
                        <div class="px-4 py-2 bg-gray-50 text-center border-t border-gray-100 hover:bg-gray-100 transition">
                            <button onclick="bukaModalNotif()" class="text-[11px] font-bold text-gray-600 block w-full">Lihat Semua Notifikasi</button>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <button id="btn-bantuan" onclick="toggleHeaderMenu('menu-bantuan')" class="text-gray-400 hover:text-pupr-blue transition focus:outline-none p-1">
                        <i class="far fa-question-circle text-xl"></i>
                    </button>
                    <div id="menu-bantuan" class="hidden absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-2 origin-top-right">
                        <a href="{{ route('admin_universal.bantuan') }}#panduan-admin" class="block px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition"><i class="fas fa-book w-5"></i> Panduan Admin</a>
                        <a href="{{ route('admin_universal.bantuan') }}#tim-it" class="block px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition"><i class="fas fa-headset w-5"></i> Hubungi Tim IT</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('admin_universal.bantuan') }}#tentang-sigap" class="block px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition"><i class="fas fa-info-circle w-5"></i> Tentang SIGAP</a>
                    </div>
                </div>

                <div class="relative">
                    <button id="btn-apps" onclick="toggleHeaderMenu('menu-apps')" class="text-gray-400 hover:text-pupr-blue transition focus:outline-none p-1">
                        <i class="fas fa-th text-xl"></i>
                    </button>
                    <div id="menu-apps" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl border border-gray-100 p-4 origin-top-right">
                        <p class="text-[10px] font-bold text-gray-400 mb-3 uppercase tracking-widest border-b border-gray-100 pb-2">Akses Cepat</p>
                        <div class="grid grid-cols-3 gap-2">
                            <a href="{{ route('admin_universal.beranda') }}" class="flex flex-col items-center justify-start p-2 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-100 transition group text-center">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mb-2 group-hover:bg-blue-100 transition"><i class="fas fa-th-large text-lg"></i></div>
                                <span class="text-[9px] font-bold text-gray-600 uppercase leading-tight">Dashboard</span>
                            </a>
                            <a href="{{ route('admin_universal.laporan') ?? '#' }}" class="flex flex-col items-center justify-start p-2 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-100 transition group text-center">
                                <div class="w-10 h-10 rounded-full bg-red-50 text-red-500 flex items-center justify-center mb-2 group-hover:bg-red-100 transition"><i class="fas fa-file-signature text-lg"></i></div>
                                <span class="text-[9px] font-bold text-gray-600 uppercase leading-tight">Laporan</span>
                            </a>
                            <a href="{{ route('admin_universal.bidang') }}" class="flex flex-col items-center justify-start p-2 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-100 transition group text-center">
                                <div class="w-10 h-10 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center mb-2 group-hover:bg-orange-100 transition"><i class="fas fa-network-wired text-lg"></i></div>
                                <span class="text-[9px] font-bold text-gray-600 uppercase leading-tight">Bidang</span>
                            </a>
                            <a href="{{ route('admin_universal.pengguna') ?? '#' }}" class="flex flex-col items-center justify-start p-2 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-100 transition group text-center">
                                <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center mb-2 group-hover:bg-green-100 transition"><i class="fas fa-users-cog text-lg"></i></div>
                                <span class="text-[9px] font-bold text-gray-600 uppercase leading-tight">Pengguna</span>
                            </a>
                            <a href="{{ route('admin_universal.profil') }}" class="flex flex-col items-center justify-start p-2 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-100 transition group text-center">
                                <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center mb-2 group-hover:bg-purple-100 transition"><i class="fas fa-user-circle text-lg"></i></div>
                                <span class="text-[9px] font-bold text-gray-600 uppercase leading-tight">Profil</span>
                            </a>
                            <a href="{{ route('admin_universal.peta') }}" class="flex flex-col items-center justify-start p-2 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-100 transition group text-center">
                                <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center mb-2 group-hover:bg-yellow-100 transition"><i class="fas fa-map-marked-alt text-lg"></i></div>
                                <span class="text-[9px] font-bold text-gray-600 uppercase leading-tight">Peta</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="h-8 w-px bg-gray-200 mx-2"></div>

                <div class="relative">
                    <div id="btn-profil" onclick="toggleHeaderMenu('menu-profil')" class="flex items-center space-x-3 cursor-pointer p-1.5 rounded-lg hover:bg-gray-50 transition">
                        @if(Auth::user()->foto_profil)
                            <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-md" alt="Profil">
                        @else
                            <div class="w-10 h-10 rounded-full bg-pupr-blue text-white flex items-center justify-center font-bold shadow-md">
                                {{ substr(Auth::user()->nama_lengkap ?? 'A', 0, 1) }}
                            </div>
                        @endif

                        <div class="hidden md:block text-left">
                            <p class="text-sm font-bold text-gray-800 leading-tight">{{ Auth::user()->nama_lengkap ?? 'Admin Utama' }}</p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">{{ str_replace('_', ' ', Auth::user()->peran ?? 'Administrator') }}</p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs ml-1"></i>
                    </div>

                    <div id="menu-profil" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-2 origin-top-right">
                        <a href="{{ route('admin_universal.profil') }}" class="block px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                            <i class="fas fa-user-circle w-5 text-center mr-1"></i> Profil Saya
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <button type="button" onclick="konfirmasiKeluar()" class="w-full text-left block px-4 py-2.5 text-xs font-semibold text-red-500 hover:bg-red-50 hover:text-red-600 transition">
                            <i class="fas fa-sign-out-alt w-5 text-center mr-1"></i> Keluar
                        </button>
                    </div>
                </div>

            </div>
        </header>

        <div class="p-8 flex-1">
            @yield('konten')
        </div>

    </main>

    <form id="form-keluar" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        function toggleHeaderMenu(menuId) {
            // Daftar semua ID menu dropdown di header
            const menus = ['menu-notif', 'menu-bantuan', 'menu-apps', 'menu-profil'];

            menus.forEach(id => {
                let elemenMenu = document.getElementById(id);
                if (id === menuId) {
                    // Buka/Tutup menu yang diklik
                    elemenMenu.classList.toggle('hidden');
                } else {
                    // Paksa tutup menu lainnya agar tidak bertumpuk
                    elemenMenu.classList.add('hidden');
                }
            });
        }

        // Script untuk menutup dropdown otomatis jika pengguna mengklik area kosong di luar menu
        document.addEventListener('click', function(event) {
            if(!event.target.closest('#btn-notif') && !event.target.closest('#menu-notif')) {
                let menu = document.getElementById('menu-notif');
                if(menu) menu.classList.add('hidden');
            }
            if(!event.target.closest('#btn-bantuan') && !event.target.closest('#menu-bantuan')) {
                let menu = document.getElementById('menu-bantuan');
                if(menu) menu.classList.add('hidden');
            }
            if(!event.target.closest('#btn-apps') && !event.target.closest('#menu-apps')) {
                let menu = document.getElementById('menu-apps');
                if(menu) menu.classList.add('hidden');
            }
            if(!event.target.closest('#btn-profil') && !event.target.closest('#menu-profil')) {
                let menu = document.getElementById('menu-profil');
                if(menu) menu.classList.add('hidden');
            }
        });
    </script>

<div id="modal-notif" class="fixed inset-0 z-[2000] hidden">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="tutupModalNotif()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">

        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800 text-lg">Pusat Notifikasi</h3>
            <button onclick="tutupModalNotif()" class="text-gray-400 hover:text-red-500 text-xl">&times;</button>
        </div>

        <div class="p-0 overflow-y-auto flex-1 divide-y divide-gray-100">
            @forelse($semuaNotif as $notif)
            <div class="flex items-start justify-between p-4 {{ $notif->is_read ? 'bg-white' : 'bg-blue-50/30' }} hover:bg-gray-50 transition">
                <a href="{{ route('admin_universal.notifikasi.klik', $notif->id) }}" class="flex-1 pr-4">
                    <p class="text-sm {{ $notif->is_read ? 'text-gray-600 font-medium' : 'text-gray-900 font-bold' }}">{{ $notif->judul }}</p>
                    <p class="text-xs {{ $notif->is_read ? 'text-gray-500' : 'text-gray-700' }} mt-1">{{ $notif->pesan }}</p>
                    <p class="text-[10px] text-gray-400 mt-2"><i class="far fa-clock"></i> {{ $notif->created_at->translatedFormat('d M Y, H:i') }}</p>
                </a>
                <form action="{{ route('admin_universal.notifikasi.hapus', $notif->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition" title="Hapus Notifikasi Ini">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </form>
            </div>
            @empty
            <div class="text-center py-10 text-gray-400 text-sm">Tidak ada notifikasi tersimpan.</div>
            @endforelse
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
            <form id="form-hapus-semua-notif" action="{{ route('admin_universal.notifikasi.hapus_semua') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" onclick="konfirmasiHapusSemuaNotif()" class="px-4 py-2 bg-white border border-gray-300 text-red-600 hover:bg-red-50 rounded-lg text-xs font-bold transition shadow-sm flex items-center">
                    <i class="fas fa-trash-alt mr-2"></i> Bersihkan Semua Notifikasi
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function bukaModalNotif() {
        document.getElementById('menu-notif').classList.add('hidden'); // Tutup dropdown
        document.getElementById('modal-notif').classList.remove('hidden'); // Buka modal
    }
    function tutupModalNotif() { document.getElementById('modal-notif').classList.add('hidden'); }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function konfirmasiHapusSemuaNotif() {
        // Sembunyikan modal notifikasi utama sejenak agar pop-up tidak bertumpuk
        tutupModalNotif();

        Swal.fire({
            title: 'Bersihkan Semua?',
            text: "Apakah Anda yakin ingin menghapus seluruh notifikasi? Data tidak dapat dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // Warna merah Tailwind untuk tombol konfirmasi
            cancelButtonColor: '#9ca3af',  // Warna abu-abu Tailwind untuk tombol batal
            confirmButtonText: 'Ya, Bersihkan!',
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
                // Jika diklik "Ya", maka form akan otomatis di-submit (dikirim)
                document.getElementById('form-hapus-semua-notif').submit();
            } else {
                // Jika diklik "Batal", buka kembali modal daftar notifikasi
                bukaModalNotif();
            }
        });
    }
</script>

<script>
    function konfirmasiKeluar() {
        Swal.fire({
            title: 'Akhiri Sesi?',
            text: "Apakah Anda yakin ingin keluar dari Dasbor SIGAP? Anda harus login kembali untuk masuk.",
            icon: 'question', // Menggunakan ikon tanda tanya agar lebih pas untuk konfirmasi
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // Warna merah Tailwind
            cancelButtonColor: '#9ca3af',  // Warna abu-abu Tailwind
            confirmButtonText: 'Ya, Keluar!',
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
                // Jika "Ya" diklik, form logout akan dikirim secara otomatis
                document.getElementById('form-keluar').submit();
            }
        });
    }
</script>

@stack('js')

</body>
</html>
