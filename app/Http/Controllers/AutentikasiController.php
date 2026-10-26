<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutentikasiController extends Controller
{
    /**
     * Tampilkan halaman login dashboard.
     */
    public function tampilLogin()
    {
        if (Auth::check() && Auth::user()->peran === 'admin_jalan') {
            return redirect()->route('admin_jalan.beranda');
        }

        return view('welcome');
    }

    /**
     * Proses login dashboard — hanya untuk Admin Jalan.
     * Masyarakat login via mobile app, bukan dashboard web.
     */
    public function prosesLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $kredensial = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($kredensial, $request->boolean('ingat'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Cek status akun aktif
            if ($user->status_akun !== 'aktif') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withInput()->with('error', 'Akun Anda dinonaktifkan. Hubungi administrator sistem.');
            }

            // Hanya Admin Jalan yang boleh masuk ke dashboard web
            if ($user->peran === 'admin_jalan') {
                return redirect()
                    ->route('admin_jalan.beranda')
                    ->with('sukses', 'Selamat datang, ' . $user->nama_lengkap . '!');
            }

            // Masyarakat → tolak akses ke dashboard
            if ($user->peran === 'masyarakat') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withInput()->with('error', 'Akun masyarakat hanya dapat digunakan melalui aplikasi mobile SIGAP.');
            }

            // Peran tidak dikenal
            Auth::logout();
            return back()->withInput()->with('error', 'Akun Anda tidak memiliki akses ke dashboard ini.');
        }

        return back()->withInput()->with('error', 'Username atau Password salah!');
    }

    /**
     * Logout dari dashboard.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('sukses', 'Anda berhasil logout.');
    }
}
