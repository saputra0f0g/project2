<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * UserController — Manajemen profil pengguna via API.
 * Berlaku untuk: Masyarakat & Admin Jalan.
 */
class UserController extends Controller
{
    /**
     * GET /api/user/profil
     * Ambil profil user yang sedang login.
     */
    public function getProfil(Request $request)
    {
        return response()->json($this->formatUser($request->user()));
    }

    /**
     * PUT /api/user/profil
     * Update nama & nomor HP.
     */
    public function updateProfil(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max'      => 'Nama lengkap maksimal 255 karakter.',
            'phone.max'     => 'Nomor HP maksimal 20 digit.',
        ]);

        $request->user()->update([
            'nama_lengkap' => $request->name,
            'nomor_hp'     => $request->phone,
        ]);

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user'    => $this->formatUser($request->user()->fresh()),
        ]);
    }

    /**
     * POST /api/user/foto-profil
     * Upload / ganti foto profil.
     */
    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'foto.required' => 'File foto wajib diunggah.',
            'foto.image'    => 'File harus berupa gambar.',
            'foto.mimes'    => 'Format foto harus JPG, JPEG, PNG, atau WebP.',
            'foto.max'      => 'Ukuran foto maksimal 2 MB.',
        ]);

        $user = $request->user();

        // Hapus foto lama dari storage
        if ($user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $path = $request->file('foto')->store('profil', 'public');
        $user->update(['foto_profil' => $path]);

        return response()->json([
            'message'  => 'Foto profil berhasil diperbarui.',
            'foto_url' => asset('storage/' . $path),
        ]);
    }

    /**
     * PUT /api/user/password
     * Ganti password — memerlukan password saat ini.
     */
    public function gantiPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required'         => 'Kata sandi baru wajib diisi.',
            'password.min'              => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password saat ini tidak benar.',
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Password berhasil diubah.']);
    }

    // ─── HELPER ───────────────────────────────────────────────────────────────

    private function formatUser(User $user): array
    {
        return [
            'id'       => $user->id,
            'name'     => $user->nama_lengkap,
            'username' => $user->username,
            'email'    => $user->email,
            'phone'    => $user->nomor_hp,
            'role'     => $user->peran, // 'masyarakat' | 'admin_jalan'
            'foto_url' => $user->foto_profil
                           ? asset('storage/' . $user->foto_profil)
                           : null,
            'status'   => $user->status_akun,
        ];
    }
}
