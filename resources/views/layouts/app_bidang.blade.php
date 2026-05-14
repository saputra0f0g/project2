<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGAP PUPR - Admin Bidang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
        // Mengambil data notifikasi (Jika fitur notifikasi sudah dibuat untuk Admin Bidang)
        $semuaNotif = \App\Models\Notifikasi::where('user_id', Auth::id())->latest()->get();
        $notifBelumDibaca = $semuaNotif->where('is_read', false)->count();
        $notifDropdown = $semuaNotif->take(5);
    @endphp

    <aside class="w-64 bg-white min-h-screen border-r border-gray-100 flex flex-col fixed left-0 top-0 h-full z-20 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
        <div class="p-8 flex items-center justify-center border-b border-gray-50">
            <img src="{{ asset('gambar/puprsigap1.png') }}" alt="Logo PUPR" class="w-48 object-contain">
        </div>

        <div class="px-6 py-4">
            <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-4 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-white text-blue-600 flex items-center justify-center shadow-sm font-bold text-lg">
                    <i class="fas fa-road"></i>
                </div>
                <div>
                    <h3 class="text-xs font-extrabold text-blue-900 leading-tight">Bidang Admin</h3>
                    <p class="text-[9px] text-blue-500 font-medium">Sistem Informasi SIGAP</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 mt-2 flex flex-col space-y-1">
            <a href="{{ route('admin_bidang.beranda') }}" class="flex items-center px-8 py-3.5 transition-colors duration-200 {{ request()->routeIs('admin_bidang.beranda') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-medium' }}">
                <i class="fas fa-th-large w-6 text-center mr-3 text-lg"></i> Dashboard
            </a>

            <a href="{{ route('admin_bidang.laporan') }}" class="flex items-center px-8 py-3.5 transition-colors duration-200 {{ request()->routeIs('admin_bidang.laporan*') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-medium' }}">
                <i class="fas fa-file-alt w-6 text-center mr-3 text-lg"></i> Kelola Laporan
            </a>

            <a href="#" class="flex items-center px-8 py-3.5 transition-colors duration-200 text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-medium">
                <i class="fas fa-clipboard-check w-6 text-center mr-3 text-lg"></i> Penugasan
            </a>

            <a href="#" class="flex items-center px-8 py-3.5 transition-colors duration-200 text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-medium justify-between">
                <div class="flex items-center">
                    <i class="fas fa-desktop w-6 text-center mr-3 text-lg"></i> Monitoring
                </div>
                <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
            </a>
        </nav>

        <div class="p-6 mt-auto">
            <div class="space-y-4 px-2">
                <form id="form-keluar" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>

                <button type="button" onclick="konfirmasiKeluar()" class="w-full flex items-center px-4 py-3.5 mt-auto text-red-500 hover:bg-red-50 hover:text-red-600 rounded-xl transition-colors duration-200 font-medium">
                    <i class="fas fa-sign-out-alt w-6 text-center mr-3 text-lg"></i> Keluar
                </button>
            </div>
        </div>
    </aside>

    <main class="flex-1 ml-64 min-h-screen flex flex-col">

        <header class="bg-white border-b border-gray-100 px-8 py-4 flex justify-between items-center sticky top-0 z-[1500]">
            <div class="relative w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" placeholder="Cari laporan atau petugas..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-pupr-blue focus:ring-1 focus:ring-pupr-blue transition">
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
                            <a href="#" class="text-[10px] font-bold text-pupr-blue hover:underline">Tandai dibaca</a>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @forelse($notifDropdown as $notif)
                                <a href="#" class="block px-4 py-3 border-b border-gray-50 transition border-l-4 {{ $notif->is_read ? 'bg-white border-transparent hover:bg-gray-50' : 'bg-blue-50/50 border-blue-500 hover:bg-blue-50' }}">
                                    <p class="text-xs {{ $notif->is_read ? 'text-gray-600 font-medium' : 'text-gray-900 font-bold' }} mb-0.5">{{ $notif->judul }}</p>
                                    <p class="text-[10px] {{ $notif->is_read ? 'text-gray-400' : 'text-gray-600' }}">{{ $notif->pesan }}</p>
                                    <p class="text-[9px] text-gray-400 mt-1 font-medium"><i class="far fa-clock"></i> {{ $notif->created_at->diffForHumans() }}</p>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-gray-400 text-xs">Belum ada notifikasi baru.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="h-8 w-px bg-gray-200 mx-2"></div>

                <div class="flex items-center space-x-3 cursor-pointer">
                    <div class="hidden md:block text-right">
                        <p class="text-sm font-bold text-gray-800 leading-tight">{{ Auth::user()->nama_lengkap ?? 'Admin Bidang' }}</p>
                        <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">{{ Auth::user()->bidang->nama_bidang ?? 'Sistem SIGAP' }}</p>
                    </div>
                    @if(Auth::user()->foto_profil)
                        <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm" alt="Profil">
                    @else
                        <div class="w-10 h-10 rounded-full bg-pupr-blue text-white flex items-center justify-center font-bold shadow-sm">
                            {{ substr(Auth::user()->nama_lengkap ?? 'A', 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>
        </header>

        <div class="p-8 flex-1">
            @yield('konten')
        </div>

    </main>

    <script>
        function toggleHeaderMenu(menuId) {
            let elemenMenu = document.getElementById(menuId);
            elemenMenu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            if(!event.target.closest('#btn-notif') && !event.target.closest('#menu-notif')) {
                let menuNotif = document.getElementById('menu-notif');
                if(menuNotif) menuNotif.classList.add('hidden');
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function konfirmasiKeluar() {
            Swal.fire({
                title: 'Akhiri Sesi?',
                text: "Apakah Anda yakin ingin keluar dari Dasbor SIGAP? Anda harus login kembali untuk masuk.",
                icon: 'question',
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
