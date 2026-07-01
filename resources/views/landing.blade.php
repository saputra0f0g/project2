<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGAP — Sistem Informasi Geografis dan Pengaduan Publik | Dinas PUPR Kab. Subang</title>
    <meta name="description" content="Laporkan kerusakan infrastruktur di Kabupaten Subang dengan mudah, cepat, dan akurat. Unduh aplikasi SIGAP sekarang.">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        navy:  { DEFAULT: '#0F2D6B', dark: '#091E4A', light: '#1a3d8a' },
                        gold:  { DEFAULT: '#F59E0B', light: '#FCD34D', dark: '#D97706' },
                        sky:   { soft: '#EFF6FF',   medium: '#DBEAFE' }
                    },
                    animation: {
                        'float':  'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%,100%': { transform: 'translateY(0px)' },
                            '50%':     { transform: 'translateY(-14px)' }
                        },
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Glassmorphism */
        .glass {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.15);
        }

        /* Sticky nav */
        .nav-blur {
            background: rgba(15, 45, 107, 0.93);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        /* Hero gradient */
        .hero-bg {
            background: linear-gradient(135deg, #091E4A 0%, #0F2D6B 45%, #1a3d8a 75%, #0e4b8c 100%);
        }

        /* CTA button */
        .btn-cta {
            background: linear-gradient(135deg, #F59E0B, #D97706);
            box-shadow: 0 8px 30px rgba(245,158,11,0.4);
            transition: all 0.3s ease;
        }
        .btn-cta:hover {
            background: linear-gradient(135deg, #FBBF24, #F59E0B);
            box-shadow: 0 14px 40px rgba(245,158,11,0.55);
            transform: translateY(-2px);
        }

        /* Feature card hover */
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(15,45,107,0.12);
        }

        /* Blob decorations */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            pointer-events: none;
        }

        /* Badge */
        .badge-secure {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.22);
        }

        /* Scroll reveal */
        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Phone drop-shadow */
        .phone-shadow { filter: drop-shadow(0 30px 60px rgba(0,0,0,0.4)); }

        /* Mobile menu */
        #mobile-menu { transition: max-height 0.4s ease, opacity 0.3s ease; }
    </style>
</head>
<body class="bg-white text-gray-800 antialiased">

    @include('landing._navbar')
    @include('landing._hero')
    @include('landing._tentang')
    @include('landing._fitur')
    @include('landing._cara-instal')
    @include('landing._bantuan')
    @include('landing._footer')

    {{-- ═══════════ GLOBAL SCRIPTS ═══════════ --}}
    <script>
        // ── Smooth Scroll (e.preventDefault — URL tetap bersih) ──────
        const NAVBAR_HEIGHT = 70;

        function smoothScrollTo(targetId) {
            const target = document.getElementById(targetId);
            if (!target) return;
            const top = target.getBoundingClientRect().top + window.scrollY - NAVBAR_HEIGHT;
            window.scrollTo({ top, behavior: 'smooth' });
        }

        // Delegasi semua <a href="#..."> agar URL tidak berubah
        document.addEventListener('click', function (e) {
            const link = e.target.closest('a[href^="#"]');
            if (!link) return;

            const href = link.getAttribute('href');
            // Hanya tangani hash anchor (bukan href="#" kosong)
            if (!href || href === '#') { e.preventDefault(); return; }

            const targetId = href.slice(1); // hapus '#'
            const target   = document.getElementById(targetId);
            if (!target) return;

            e.preventDefault();                        // ← jaga URL tetap bersih
            closeMobileMenu();
            smoothScrollTo(targetId);
        });

        // ── Mobile Menu ────────────────────────────────────────────
        const hamburger     = document.getElementById('hamburger');
        const hamburgerIcon = document.getElementById('hamburger-icon');
        const mobileMenu    = document.getElementById('mobile-menu');
        let menuOpen = false;

        hamburger.addEventListener('click', () => {
            menuOpen = !menuOpen;
            if (menuOpen) {
                mobileMenu.style.maxHeight = mobileMenu.scrollHeight + 'px';
                mobileMenu.style.opacity   = '1';
                hamburgerIcon.classList.replace('fa-bars', 'fa-times');
            } else {
                closeMobileMenu();
            }
        });

        function closeMobileMenu() {
            menuOpen = false;
            mobileMenu.style.maxHeight = '0';
            mobileMenu.style.opacity   = '0';
            hamburgerIcon.classList.replace('fa-times', 'fa-bars');
        }

        // ── FAQ Accordion ────────────────────────────────────────────
        function toggleFaq(btn) {
            const answer = btn.nextElementSibling;
            const icon   = btn.querySelector('i.fa-chevron-down');
            const isOpen = answer.style.maxHeight && answer.style.maxHeight !== '0px';

            document.querySelectorAll('.faq-answer').forEach(a => a.style.maxHeight = '0px');
            document.querySelectorAll('.faq-btn i.fa-chevron-down').forEach(i => i.style.transform = '');

            if (!isOpen) {
                answer.style.maxHeight = answer.scrollHeight + 'px';
                icon.style.transform   = 'rotate(180deg)';
            }
        }

        // ── Scroll Reveal ────────────────────────────────────────────
        const revealEls = document.querySelectorAll('.reveal');
        const observer  = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    observer.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });
        revealEls.forEach(el => observer.observe(el));

        // ── Active nav highlight on scroll ───────────────────────────
        const navLinks = document.querySelectorAll('.nav-link');
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('section[id]');
            let current = '';
            sections.forEach(s => {
                if (window.scrollY >= s.offsetTop - NAVBAR_HEIGHT - 10) current = s.id;
            });
            navLinks.forEach(l => {
                l.classList.remove('text-gold', 'bg-white/10');
                const href = l.getAttribute('href');
                if (href === '#' + current) {
                    l.classList.add('text-gold', 'bg-white/10');
                }
            });
        }, { passive: true });
    </script>

</body>
</html>
