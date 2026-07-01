<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutentikasiController extends Controller
{
    // Menampilkan halaman login (welcome.blade.php)
    public function tampilLogin()
    {
        return view('welcome');
    }

    // Proses Autentikasi
    public function prosesLogin(Request $request)
    {
        $kredensial = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($kredensial)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Cek status akun
            if ($user->status_akun !== 'aktif') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Akun Anda dinonaktifkan atau belum aktif. Hubungi admin.');
            }

            // Pengalihan berdasarkan Peran (Role) sesuai SRS
            if ($user->peran == 'admin_universal') {
                return redirect()->route('admin_universal.beranda')->with('sukses', 'Selamat datang, Admin Universal ' . $user->nama_lengkap . '!');
            } elseif ($user->peran == 'admin_bidang') {
                return redirect()->route('admin_bidang.beranda')->with('sukses', 'Selamat datang, Admin Bidang ' . $user->nama_lengkap . '!');
            } elseif ($user->peran == 'pekerja_bidang') {
                return redirect()->route('pekerja.beranda')->with('sukses', 'Selamat datang, Pekerja ' . $user->nama_lengkap . '!');
            } elseif ($user->peran == 'masyarakat') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Akun masyarakat hanya dapat digunakan melalui aplikasi mobile (Sigap Mobile).');
            }

            return redirect()->intended('/');
        }

        return back()->with('error', 'Nama Pengguna atau Kata Sandi salah!');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
