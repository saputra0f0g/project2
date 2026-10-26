<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SIGAP PUPR Subang</title>
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
                        }
                    }
                }
            }
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
        }

        .input-focus:focus-within {
            border-color: #1E3A8A;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }

        /* Lapisan gambar latar kantor PUPR */
        .bg-infrastruktur {
            background-size: cover;
            background-position: center;
            transition: opacity 1s ease-in-out;
        }

        /* Animasi CSS Murni agar pasti berjalan */
        .animasi-masuk {
            animation: fadeInMasuk 0.8s ease-out forwards;
            opacity: 0; /* Mulai dari transparan */
        }

        @keyframes fadeInMasuk {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="flex flex-col md:flex-row w-full max-w-5xl bg-white shadow-2xl rounded-2xl overflow-hidden min-h-[580px] animasi-masuk">

        <div class="hidden md:flex md:w-7/12 relative flex-col justify-between p-12 overflow-hidden">

            <div id="latarSlideA" class="bg-infrastruktur absolute inset-0" aria-hidden="true"></div>
            <div id="latarSlideB" class="bg-infrastruktur absolute inset-0 opacity-0" aria-hidden="true"></div>

            <div class="absolute inset-0 bg-[#1E3A8A]/50 transition-colors duration-700 pointer-events-none"></div>

            <div class="relative z-10 w-full mt-8">

                <div class="inline-flex items-center space-x-2 border border-white/30 rounded-full px-4 py-1.5 mb-8 w-max">
                    <i class="fas fa-shield-check text-blue-200 text-[11px]"></i>
                    <span class="text-blue-100 text-[9px] font-bold tracking-widest uppercase">Dashboard Admin </span>
                </div>

                <h2 class="text-white text-4xl lg:text-5xl font-extrabold mb-6 leading-tight tracking-tight">
                    Kelola Laporan<br>Infrastruktur 
                </h2>

                <p class="text-blue-100 text-sm font-medium leading-relaxed opacity-90 max-w-md">
                    Portal khusus Admin Dinas PUPR Kabupaten Subang untuk memverifikasi, memantau, dan menindaklanjuti laporan kerusakan jalan dari masyarakat.
                </p>
            </div>

            <div class="relative z-10 flex space-x-10 mt-auto pt-8 border-t border-white/20">
                <div>
                    <p class="text-white font-extrabold text-2xl">24/7</p>
                    <p class="text-[9px] text-blue-200 font-bold uppercase tracking-widest mt-1">Monitoring</p>
                </div>
                <div>
                    <p class="text-white font-extrabold text-2xl">Aman</p>
                    <p class="text-[9px] text-blue-200 font-bold uppercase tracking-widest mt-1">Terenkripsi</p>
                </div>
            </div>
        </div>

        <div class="w-full md:w-5/12 flex flex-col justify-center p-8 lg:p-12 bg-white">

            <div class="mb-6">
                <a href="{{ route('landing') }}" class="inline-flex items-center text-[11px] font-bold text-pupr-blue hover:text-blue-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>

            <div class="mb-8">
                <h1 class="text-2xl font-extrabold text-gray-800 mb-1.5">Masuk Admin </h1>
                <p class="text-gray-500 text-[11px] font-medium">Khusus petugas pengelola laporan.</p>
            </div>

            @if(session('sukses'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-3 mb-6 rounded-r-lg text-xs font-bold flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('sukses') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 mb-6 rounded-r-lg text-xs font-bold flex items-center shadow-sm animate-pulse">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('login.proses') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-2">Nama Pengguna</label>
                    <div class="relative flex items-center bg-blue-50/50 border border-gray-200 rounded-lg overflow-hidden input-focus transition-all">
                        <span class="pl-4 text-gray-400">
                            <i class="far fa-id-badge text-sm"></i>
                        </span>
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan nama pengguna"
                            class="w-full pl-3 pr-4 py-3 bg-transparent text-xs font-semibold text-gray-800 outline-none"
                            required autofocus>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider">Kata Sandi</label>
                        </div>
                    <div class="relative flex items-center bg-blue-50/50 border border-gray-200 rounded-lg overflow-hidden input-focus transition-all">
                        <span class="pl-4 text-gray-400">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input type="password" id="inputKataSandi" name="password" placeholder="••••••••"
                            class="w-full pl-3 pr-12 py-3 bg-transparent text-xs font-semibold text-gray-800 outline-none"
                            required>
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-pupr-blue transition-colors focus:outline-none">
                            <i class="far fa-eye" id="ikonMata"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center pt-1">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="ingat" value="1" class="rounded border-gray-300 text-pupr-yellow focus:ring-pupr-yellow h-3.5 w-3.5 cursor-pointer">
                        <span class="ml-2 text-[10px] font-semibold text-gray-500 group-hover:text-gray-700 transition select-none">Ingat perangkat ini</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-pupr-yellow text-gray-900 rounded-lg font-extrabold text-xs shadow-md hover:bg-yellow-500 hover:shadow-lg active:scale-[0.99] transition-all flex items-center justify-center group mt-2 border border-yellow-500">
                    <span>Masuk Ke Dashboard</span>
                    <i class="fas fa-sign-in-alt ml-2 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <footer class="mt-12 pt-6 border-t border-gray-100">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter text-center">
                    &copy; 2026 SISTEM INFORMASI SIGAP • PUPR SUBANG
                </p>
            </footer>
        </div>
    </div>

    <script>
        async function mulaiCarouselLatar() {
            // Simpan tiga foto tambahan di public/gambar dengan nama berikut.
            const fotoLatar = [
                @json(asset('gambar/kantor.jpg')),
                @json(asset('gambar/kantor-2.jpg')),
                @json(asset('gambar/kantor-3.jpg')),
                @json(asset('gambar/kantor-4.jpg')),
            ];
            const fotoTersedia = (await Promise.all(fotoLatar.map((url) => new Promise((resolve) => {
                const gambar = new Image();
                gambar.onload = () => resolve(url);
                gambar.onerror = () => resolve(null);
                gambar.src = url;
            })))).filter(Boolean);

            if (fotoTersedia.length < 2) return;

            const slideA = document.getElementById('latarSlideA');
            const slideB = document.getElementById('latarSlideB');
            let slideAktif = slideA;
            let indeksFoto = 0;

            slideA.style.backgroundImage = `url("${fotoTersedia[0]}")`;

            window.setInterval(() => {
                indeksFoto = (indeksFoto + 1) % fotoTersedia.length;
                const slideBerikutnya = slideAktif === slideA ? slideB : slideA;
                slideBerikutnya.style.backgroundImage = `url("${fotoTersedia[indeksFoto]}")`;
                slideBerikutnya.classList.remove('opacity-0');
                slideBerikutnya.classList.add('opacity-100');
                slideAktif.classList.remove('opacity-100');
                slideAktif.classList.add('opacity-0');
                slideAktif = slideBerikutnya;
            }, 5000);
        }

        mulaiCarouselLatar();

        function togglePassword() {
            const passwordInput = document.getElementById('inputKataSandi');
            const eyeIcon = document.getElementById('ikonMata');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>
