{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- NAVBAR — resources/views/landing/_navbar.blade.php       --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<header id="navbar" class="fixed top-0 left-0 right-0 z-50 nav-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-[70px]">

            {{-- ── Logo ── --}}
            <a href="#beranda" class="flex items-center gap-3 group flex-shrink-0">
                <img src="{{ asset('gambar/puprlogo.png') }}"
                     alt="Logo PUPR"
                     class="h-10 w-10 object-contain group-hover:scale-105 transition-transform duration-300">
                <div class="leading-tight">
                    <span class="text-white font-black text-lg tracking-wide block">SIGAP</span>
                    <span class="text-blue-300 text-[11px] font-semibold tracking-widest uppercase">PUPR Kab. Subang</span>
                </div>
            </a>

            {{-- ── Desktop Nav ── --}}
            <nav class="hidden lg:flex items-center gap-1">
                @foreach([
                    ['#beranda',     'Beranda'],
                    ['#tentang',     'Tentang'],
                    ['#fitur',       'Fitur'],
                    ['#cara-instal', 'Cara Instal'],
                    ['#bantuan',     'Bantuan'],
                ] as [$href, $label])
                <a href="{{ $href }}"
                   class="nav-link px-4 py-2 text-blue-100 hover:text-white text-sm font-semibold rounded-lg hover:bg-white/10 transition-all duration-200">
                    {{ $label }}
                </a>
                @endforeach
            </nav>

            {{-- ── Login Internal Button ── --}}
            <div class="hidden lg:flex items-center">
                <a href="{{ route('login') }}"
                   class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-200 shadow-lg hover:-translate-y-0.5"
                   style="background: linear-gradient(135deg,#F59E0B,#D97706); color:#0F2D6B; box-shadow:0 6px 20px rgba(245,158,11,0.35);">
                    <i class="fas fa-sign-in-alt"></i>
                    Login Internal
                </a>
            </div>

            {{-- ── Hamburger (Mobile) ── --}}
            <button id="hamburger" class="lg:hidden text-white p-2 rounded-lg hover:bg-white/10 transition-all">
                <i class="fas fa-bars text-xl" id="hamburger-icon"></i>
            </button>
        </div>
    </div>

    {{-- ── Mobile Menu ── --}}
    <div id="mobile-menu" class="lg:hidden overflow-hidden max-h-0 opacity-0 border-t border-white/10">
        <div class="px-4 py-4 space-y-1">
            @foreach([
                ['#beranda',     'Beranda'],
                ['#tentang',     'Tentang'],
                ['#fitur',       'Fitur'],
                ['#cara-instal', 'Cara Instal'],
                ['#bantuan',     'Bantuan'],
            ] as [$href, $label])
            <a href="{{ $href }}" onclick="closeMobileMenu()"
               class="block px-4 py-3 text-blue-100 hover:text-white hover:bg-white/10 rounded-lg text-sm font-semibold transition-all">
                {{ $label }}
            </a>
            @endforeach

            <div class="pt-3 border-t border-white/10">
                <a href="{{ route('login') }}"
                   class="flex items-center justify-center gap-2 w-full py-3 rounded-xl font-bold text-sm"
                   style="background: linear-gradient(135deg,#F59E0B,#D97706); color:#0F2D6B;">
                    <i class="fas fa-sign-in-alt"></i> Login Internal
                </a>
            </div>
        </div>
    </div>
</header>
