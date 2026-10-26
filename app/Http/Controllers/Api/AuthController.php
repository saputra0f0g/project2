<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register — hanya untuk Masyarakat (self-register via mobile app).
     * Admin Jalan dibuat langsung di database / dashboard oleh superadmin.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|min:3|max:50|unique:users,username',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan login.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 50 karakter.',
            'username.unique' => 'Username ini sudah dipakai. Coba username lain.',
            'phone.max' => 'Nomor HP maksimal 20 digit.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'nama_lengkap' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'nomor_hp' => $request->phone,
            'password' => Hash::make($request->password),
            'peran' => 'masyarakat', // selalu masyarakat saat register
            'status_akun' => 'aktif',
        ]);

        $token = $user->createToken('sigap_mobile')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil. Selamat datang di SIGAP!',
            'user' => $this->formatUser($user),
            'token' => $token,
        ], 201);
    }

    /**
     * Login mobile — hanya akun masyarakat yang dapat memperoleh token aplikasi.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email atau username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Coba login via email, fallback ke username
        $user = User::where('email', $request->email)
            ->orWhere('username', $request->email)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'ID atau kata sandi salah.',
            ], 401);
        }

        if ($user->status_akun === 'non-aktif') {
            return response()->json([
                'message' => 'Akun Anda dinonaktifkan. Hubungi Admin Jalan.',
            ], 403);
        }

        if ($user->peran !== 'masyarakat') {
            return response()->json([
                'message' => 'Aplikasi mobile hanya dapat digunakan oleh masyarakat.',
            ], 403);
        }

        // Hapus token lama & buat baru
        $user->tokens()->delete();
        $token = $user->createToken('sigap_mobile')->plainTextToken;

        $user->update(['terakhir_login' => now()]);

        return response()->json([
            'user' => $this->formatUser($user),
            'token' => $token,
        ]);
    }

    /**
     * Logout — cabut token aktif.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }

    /**
     * Me — kembalikan data user yang sedang login.
     */
    public function me(Request $request)
    {
        return response()->json($this->formatUser($request->user()));
    }

    // ─── HELPER ───────────────────────────────────────────────────────────────

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->nama_lengkap,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->nomor_hp,
            'role' => $user->peran, // 'masyarakat' | 'admin_jalan'
            'foto_url' => $user->foto_profil
                           ? asset('storage/'.$user->foto_profil)
                           : null,
            'status' => $user->status_akun,
        ];
    }
}
