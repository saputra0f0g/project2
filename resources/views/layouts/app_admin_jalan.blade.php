<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SIGAP Admin Jalan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
        $countMenunggu = \App\Models\LaporanKeluhan::where('status', 'menunggu')->count();
        $laporanMenunggu = \App\Models\LaporanKeluhan::with('pelapor')
            ->where('status', 'menunggu')
            ->latest()
            ->take(5)
            ->get();
        $namaAdmin = Auth::user()?->nama_lengkap ?? 'Admin Jalan';
        $inialAdmin = strtoupper(substr($namaAdmin, 0, 1));
    @endphp

    {{-- Overlay mobile --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/40 z-20 hidden lg:hidden" onclick="tutupSidebar()"></div>

    <aside id="sidebar-admin" class="w-64 bg-white min-h-screen border-r border-gray-100 flex flex-col fixed left-0 top-0 h-full z-30 shadow-[4px_0_24px_rgba(0,0,0,0.02)] -translate-x-full lg:translate-x-0 transition-transform duration-200">
        <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-3">
            <img src="{{ asset('gambar/puprlogo.png') }}" alt="Logo PUPR" class="h-10 w-10 object-contain">
            <div>
                <p class="text-sm font-extrabold text-pupr-blue leading-tight">SIGAP</p>
                <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">PUPR Kabupaten Subang</p>
            </div>
        </div>

        <div class="px-5 py-4">
            <div class="bg-blue-50/70 border border-blue-100 rounded-xl p-3.5 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-white text-blue-600 flex items-center justify-center shadow-sm">
                    <i class="fas fa-road"></i>
                </div>
                <div>
                    <h3 class="text-xs font-extrabold text-blue-900 leading-tight">Admin Jalan</h3>
                    <p class="text-[10px] text-blue-500 font-medium">Laporan infrastruktur jalan</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 mt-1 flex flex-col space-y-1">
            <a href="{{ route('admin_jalan.beranda') }}"
               class="flex items-center px-7 py-3.5 transition-colors duration-200
                      {{ request()->routeIs('admin_jalan.beranda')
                         ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 font-semibold'
                         : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-medium' }}">
                <i class="fas fa-th-large w-6 text-center mr-3"></i> Dashboard
            </a>

            <a href="{{ route('admin_jalan.laporan.indeks') }}"
               class="flex items-center px-7 py-3.5 transition-colors duration-200 justify-between
                      {{ request()->routeIs('admin_jalan.laporan*')
                         ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 font-semibold'
                         : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-medium' }}">
                <div class="flex items-center">
                    <i class="fas fa-file-alt w-6 text-center mr-3"></i> Laporan
                </div>
                @if($countMenunggu > 0)
                    <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                        {{ $countMenunggu > 99 ? '99+' : $countMenunggu }}
                    </span>
                @endif
            </a>
        </nav>

        <div class="p-5 border-t border-gray-50">
            <button type="button" onclick="konfirmasiKeluar()" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-500 bg-red-50 hover:bg-red-100 rounded-xl transition">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </button>
        </div>
    </aside>

    <main class="flex-1 lg:ml-64 min-h-screen flex flex-col w-full">
        <header class="bg-white border-b border-gray-100 px-4 sm:px-8 py-4 flex justify-between items-center sticky top-0 z-[1500] gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" onclick="bukaSidebar()" class="lg:hidden w-10 h-10 rounded-xl border border-gray-200 text-gray-600 flex items-center justify-center">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="min-w-0">
                    <h1 class="text-base sm:text-lg font-bold text-gray-900 truncate">@yield('page_title', 'Dashboard Admin Jalan')</h1>
                    <p class="text-xs text-gray-400 font-medium hidden sm:block">@yield('page_subtitle', 'Kelola laporan kerusakan jalan dari masyarakat')</p>
                </div>
            </div>

            <div class="flex items-center space-x-3 sm:space-x-5 relative">
                <form method="GET" action="{{ route('admin_jalan.laporan.indeks') }}" class="relative hidden md:block w-72">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-search text-sm"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, judul, atau alamat..."
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-pupr-blue focus:ring-1 focus:ring-pupr-blue transition">
                </form>

                <div class="relative">
                    <button id="btn-notif" type="button" onclick="toggleHeaderMenu('menu-notif')" class="text-gray-400 hover:text-pupr-blue transition relative focus:outline-none p-1" title="Laporan menunggu">
                        <i class="far fa-bell text-xl"></i>
                        @if($countMenunggu > 0)
                            <span class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 px-1 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-white">{{ $countMenunggu > 99 ? '99+' : $countMenunggu }}</span>
                        @endif
                    </button>

                    <div id="menu-notif" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                            <span class="text-sm font-bold text-gray-700">Menunggu verifikasi</span>
                            <a href="{{ route('admin_jalan.laporan.indeks', ['status' => 'menunggu']) }}" class="text-[10px] font-bold text-pupr-blue hover:underline">Lihat semua</a>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            @forelse($laporanMenunggu as $notifLaporan)
                                <a href="{{ route('admin_jalan.laporan.detail', $notifLaporan->id) }}" class="block px-4 py-3 border-b border-gray-50 bg-amber-50/40 hover:bg-amber-50 border-l-4 border-amber-400">
                                    <p class="text-xs text-gray-900 font-bold mb-0.5 truncate">{{ $notifLaporan->judul_laporan ?? $notifLaporan->id_laporan }}</p>
                                    <p class="text-[10px] text-gray-500 truncate">{{ $notifLaporan->alamat_map ?? 'Lokasi belum diisi' }}</p>
                                    <p class="text-[9px] text-gray-400 mt-1 font-medium"><i class="far fa-clock"></i> {{ $notifLaporan->created_at?->diffForHumans() }}</p>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-gray-400 text-xs">Tidak ada laporan yang menunggu verifikasi.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-end">
                    <button
                        id="btn-web-push"
                        type="button"
                        data-vapid-key="{{ config('webpush.vapid.public_key') }}"
                        data-store-url="{{ route('admin_jalan.push_subscriptions.store', [], false) }}"
                        data-delete-url="{{ route('admin_jalan.push_subscriptions.destroy', [], false) }}"
                        data-worker-url="/sw.js"
                        class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100 disabled:cursor-not-allowed disabled:opacity-50"
                        aria-describedby="web-push-status"
                    >
                        <i class="fas fa-bell" aria-hidden="true"></i>
                        <span data-label>Aktifkan Notifikasi</span>
                    </button>
                    <span id="web-push-status" class="mt-1 max-w-48 text-right text-[10px] text-gray-500" aria-live="polite"></span>
                </div>

                <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>

                <div class="relative">
                    <div id="btn-profil" onclick="toggleHeaderMenu('menu-profil')" class="flex items-center space-x-3 cursor-pointer p-1.5 rounded-lg hover:bg-gray-50 transition">
                        @if(Auth::user()?->foto_profil)
                            <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="Profil">
                        @else
                            <div class="w-10 h-10 rounded-full bg-pupr-blue text-white flex items-center justify-center font-bold">
                                {{ $inialAdmin }}
                            </div>
                        @endif

                        <div class="hidden md:block text-left">
                            <p class="text-sm font-bold text-gray-800 leading-tight">{{ $namaAdmin }}</p>
                            <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">Admin Jalan</p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs ml-1 hidden sm:inline"></i>
                    </div>

                    <div id="menu-profil" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-2 origin-top-right z-50">
                        <div class="px-4 py-2 border-b border-gray-50">
                            <p class="text-xs font-bold text-gray-800">{{ $namaAdmin }}</p>
                            <p class="text-[10px] text-gray-400">{{ Auth::user()?->email }}</p>
                        </div>
                        <button type="button" onclick="konfirmasiKeluar()" class="w-full text-left block px-4 py-2.5 text-xs font-semibold text-red-500 hover:bg-red-50 hover:text-red-600 transition">
                            <i class="fas fa-sign-out-alt w-5 text-center mr-1"></i> Keluar
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-8 flex-1">
            @if(session('success') || session('sukses'))
                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl mb-6">
                    <i class="fas fa-check-circle text-emerald-500 text-lg flex-shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('success') ?? session('sukses') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-6">
                    <i class="fas fa-exclamation-circle text-red-500 text-lg flex-shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-6">
                    <i class="fas fa-triangle-exclamation text-red-500 text-lg flex-shrink-0 mt-0.5"></i>
                    <ul class="list-disc list-inside text-sm font-medium space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('konten')
            @yield('content')
        </div>
    </main>

    <form id="form-keluar" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        function bukaSidebar() {
            document.getElementById('sidebar-admin').classList.remove('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
        }

        function tutupSidebar() {
            document.getElementById('sidebar-admin').classList.add('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        }

        function toggleHeaderMenu(menuId) {
            ['menu-notif', 'menu-profil'].forEach(id => {
                const elemenMenu = document.getElementById(id);
                if (!elemenMenu) return;
                if (id === menuId) {
                    elemenMenu.classList.toggle('hidden');
                } else {
                    elemenMenu.classList.add('hidden');
                }
            });
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('#btn-notif') && !event.target.closest('#menu-notif')) {
                document.getElementById('menu-notif')?.classList.add('hidden');
            }
            if (!event.target.closest('#btn-profil') && !event.target.closest('#menu-profil')) {
                document.getElementById('menu-profil')?.classList.add('hidden');
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function konfirmasiKeluar() {
            Swal.fire({
                title: 'Akhiri sesi?',
                text: 'Anda akan keluar dari dashboard Admin Jalan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-gray-100',
                    title: 'text-gray-800 font-bold',
                    confirmButton: 'px-6 py-2.5 rounded-lg text-sm font-bold',
                    cancelButton: 'px-6 py-2.5 rounded-lg text-sm font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-keluar').submit();
                }
            });
        }
    </script>

    @stack('scripts')
    @stack('js')

    <script>
        (() => {
            const button = document.getElementById('btn-web-push');
            const status = document.getElementById('web-push-status');
            const label = button?.querySelector('[data-label]');

            if (!button || !status || !label) return;

            const setState = (subscribed, message = '') => {
                button.dataset.subscribed = subscribed ? 'true' : 'false';
                label.textContent = subscribed ? 'Nonaktifkan Notifikasi' : 'Aktifkan Notifikasi';
                status.textContent = message;
            };

            const decodeVapidKey = (key) => {
                const padded = key + '='.repeat((4 - key.length % 4) % 4);
                const base64 = padded.replace(/-/g, '+').replace(/_/g, '/');
                return Uint8Array.from(atob(base64), (character) => character.charCodeAt(0));
            };

            const subscriptionPayload = (subscription) => {
                const payload = subscription.toJSON();
                return {
                    endpoint: payload.endpoint,
                    keys: payload.keys,
                };
            };

            const saveSubscription = async (subscription) => {
                const response = await fetch(button.dataset.storeUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(subscriptionPayload(subscription)),
                });
                await responseMessage(response);
            };

            const responseMessage = async (response) => {
                const payload = await response.json().catch(() => ({}));
                if (response.ok) return;

                const validationMessage = Object.values(payload.errors || {}).flat()[0];
                throw new Error(validationMessage || payload.message || 'Permintaan tidak berhasil.');
            };

            const refreshSubscriptionState = async () => {
                if (!window.isSecureContext || !('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
                    button.disabled = true;
                    return;
                }

                if (!button.dataset.vapidKey) {
                    button.disabled = true;
                    status.textContent = 'Kunci VAPID belum dikonfigurasi di server.';
                    return;
                }

                const registration = await navigator.serviceWorker.getRegistration(button.dataset.workerUrl);
                const subscription = await registration?.pushManager.getSubscription();
                if (subscription) {
                    await saveSubscription(subscription);
                    setState(true, 'Notifikasi aktif di browser ini.');
                } else {
                    setState(false);
                }
            };

            button.addEventListener('click', async () => {
                button.disabled = true;
                status.textContent = 'Memproses pengaturan notifikasi...';

                try {
                    const registration = await navigator.serviceWorker.register(button.dataset.workerUrl);
                    let subscription = await registration.pushManager.getSubscription();

                    if (subscription && button.dataset.subscribed === 'true') {
                        const response = await fetch(button.dataset.deleteUrl, {
                            method: 'DELETE',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ endpoint: subscription.endpoint }),
                        });
                        await responseMessage(response);
                        await subscription.unsubscribe();
                        setState(false, 'Notifikasi dinonaktifkan di browser ini.');
                        return;
                    }

                    if (!subscription) {
                        const permission = Notification.permission === 'granted'
                            ? 'granted'
                            : await Notification.requestPermission();

                        if (permission !== 'granted') {
                            throw new Error('Izin notifikasi belum diberikan. Ubah izin situs di pengaturan browser.');
                        }

                        subscription = await registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: decodeVapidKey(button.dataset.vapidKey),
                        });
                    }

                    await saveSubscription(subscription);
                    setState(true, 'Notifikasi laporan baru aktif di browser ini.');
                } catch (error) {
                    status.textContent = error.message || 'Pengaturan notifikasi gagal. Silakan coba lagi.';
                } finally {
                    button.disabled = false;
                }
            });

            refreshSubscriptionState().catch((error) => {
                status.textContent = error.message || 'Status notifikasi tidak dapat diperiksa.';
            });
        })();
    </script>
</body>
</html>
