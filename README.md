# SIGAP Web

A Laravel 12 backend dan dashboard Admin Jalan untuk aplikasi SIGAP pelaporan kerusakan infrastruktur jalan.

## Fitur Utama

- Autentikasi Admin Jalan untuk dashboard web dan masyarakat melalui API mobile
- Dashboard Admin Jalan untuk memantau laporan masuk
- Detail laporan, pembaruan status, dan ekspor laporan ke CSV
- API untuk autentikasi, profil masyarakat, statistik, dan pelaporan/riwayat laporan
- Web Push Notification untuk memberi tahu Admin Jalan saat laporan baru masuk

## Struktur Peran

- `admin_jalan`: mengakses dashboard web, memantau laporan, memperbarui status, dan mengatur notifikasi push
- `masyarakat`: menggunakan aplikasi mobile untuk membuat dan memantau laporan serta mengelola profil

## Persyaratan

- PHP 8.2+
- Composer
- Node.js 18+ dan npm
- XAMPP, Laragon, atau server lokal serupa
- Database MySQL atau MariaDB

## Instalasi

1. Clone atau salin project ke folder server lokal, misalnya `c:\xampp\htdocs\sigap_web`
2. Jalankan:
   - `composer install`
   - `npm install`
3. Salin file environment:
   - `copy .env.example .env`
4. Sesuaikan konfigurasi database dan storage di `.env`
5. Generate app key:
   - `php artisan key:generate`
6. Jalankan migrasi dan seeder:
   - `php artisan migrate`
   - `php artisan db:seed`
7. Build asset produksi:
   - `npm run build`

> Alternatif setup otomatis:
> - `composer setup` (jika ingin menjalankan rangkaian install, env, key:generate, migrate, dan build dalam sekali jalan)

## Menjalankan Aplikasi

- Jalankan server Laravel:
  - `php artisan serve`
- Jalankan mode development Vite:
  - `npm run dev`

## Web Push Notification Admin Jalan

Web Push dikirim ke browser Admin Jalan yang telah mengaktifkan notifikasi. Push subscription disimpan per akun admin dan notifikasi dipicu setelah API laporan berhasil menyimpan laporan.

1. Salin konfigurasi Web Push dan migrasi package:
   - `php artisan vendor:publish --provider="NotificationChannels\WebPush\WebPushServiceProvider" --tag="migrations"`
   - `php artisan vendor:publish --provider="NotificationChannels\WebPush\WebPushServiceProvider" --tag="config"`
2. Buat VAPID keys dan jalankan migrasi:
   - `php artisan webpush:vapid`
   - `php artisan migrate`
3. Pastikan `APP_URL` menggunakan HTTPS pada server publik dan atur `VAPID_SUBJECT` ke URL situs atau alamat `mailto:` yang valid. Simpan `VAPID_PRIVATE_KEY` dengan aman dan jangan ganti kunci setelah admin berlangganan.
4. Login sebagai Admin Jalan melalui browser yang mendukung Web Push, buka dashboard, lalu tekan **Aktifkan Notifikasi** dan izinkan notifikasi. Push browser memerlukan HTTPS; `localhost` dapat digunakan untuk pengembangan.
5. Saat laporan baru masuk melalui `POST /api/laporan`, browser admin yang terdaftar menerima notifikasi **Laporan Baru Masuk**. Tombol yang sama dapat digunakan untuk menonaktifkan notifikasi browser tersebut.

Service worker tersedia di `public/sw.js`. Pastikan server web melayani berkas tersebut di `/sw.js` dan akses ke dashboard serta URL API berasal dari origin yang sama.

## Akun Seeder untuk Pengujian

Seeder `database/seeders/UserSeeder.php` menyediakan akun berikut:

- Admin Jalan
  - email: `admin.jalan@sigap.id`
  - username: `admin_jalan`
  - password: `AdminJalan123!`
- Masyarakat
  - email: `budi.test@sigap.id`
  - username: `user_test`
  - password: `UserTest123!`

## Skrip Composer & NPM

- `composer setup` : install composer, setup env, generate app key, migrate, dan build asset
- `npm run dev` : jalankan Vite development server
- `npm run build` : build assets produksi
- `composer test` : jalankan test suite Laravel

## Rute Utama

- `/` : halaman landing
- `/login` : login dashboard Admin Jalan
- `/admin-jalan/beranda` : dashboard Admin Jalan
- `/admin-jalan/laporan` : daftar laporan
- `/api/auth/*` : autentikasi aplikasi mobile
- `/api/laporan` : membuat dan melihat riwayat laporan
- `/api/user/*` : pengelolaan profil masyarakat

## Dependensi Utama

- `laravel/framework` ^12.0
- `laravel-notification-channels/webpush` ^13.0
- `barryvdh/laravel-dompdf` ^3.1
- `laravel/sanctum` ^4.3
- `laravel/tinker` ^2.10.1
- `vite` ^7.0.7
- `tailwindcss` ^4.0.0
- `laravel-vite-plugin` ^2.0.0
- `axios` ^1.11.0

## Catatan

Pastikan file `.env` sudah dikonfigurasi dengan benar sebelum menjalankan migrasi. Jika menggunakan storage atau upload gambar, jalankan `php artisan storage:link` bila diperlukan.
