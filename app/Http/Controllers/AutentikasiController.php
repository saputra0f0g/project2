<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutentikasiController extends Controller
{
    // Menampilkan halaman login (welcome.blade.php)
    public function tampilLogin()
    {
        return 'login page';
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

            // Pengalihan berdasarkan Peran (Role) sesuai SRS
            if ($user->peran == 'admin_universal') {
                return redirect()->route('admin_universal.beranda');
            } elseif ($user->peran == 'admin_bidang') {
                return redirect()->route('admin_bidang.beranda');
            } elseif ($user->peran == 'pekerja_bidang') {
                return redirect()->route('pekerja.beranda');
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
        return redirect('/');
    }
}
