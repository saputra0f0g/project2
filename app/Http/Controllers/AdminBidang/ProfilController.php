<?php

namespace App\Http\Controllers\AdminBidang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\LogAktivitas;
use App\Models\User;

class ProfilController extends Controller
{
    // Tampilkan Halaman Profil
    public function indeks()
    {
        $user = Auth::user();

        // Ambil log aktivitas menggunakan LogAktivitas
        $aktivitas_terbaru = LogAktivitas::where('user_id', $user->id)->latest()->take(5)->get();
        $semua_aktivitas = LogAktivitas::where('user_id', $user->id)->latest()->get();

        return view('admin_bidang.profil.index', compact('user', 'aktivitas_terbaru', 'semua_aktivitas'));
    }

    // Update Data Profil (Teks)
    public function update(Request $request)
    {
        $user = User::find(Auth::id());

        $request->validate([
            'nama_lengkap'   => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $user->id,
            'nomor_hp'       => 'nullable|string|max:20',
            'kantor_wilayah' => 'nullable|string|max:255',
        ]);

        $user->update([
            'nama_lengkap'   => $request->nama_lengkap,
            'email'          => $request->email,
            'nomor_hp'       => $request->nomor_hp,
            'kantor_wilayah' => $request->kantor_wilayah,
        ]);

        return back()->with('sukses', 'Data profil berhasil diperbarui!');
    }

    // Update Foto Profil
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240'
        ]);

        $user = User::find(Auth::id());

        if ($user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $path = $request->file('foto_profil')->store('profil', 'public');
        $user->update(['foto_profil' => $path]);

        return back()->with('sukses', 'Foto profil berhasil diubah!');
    }

    // Hapus Foto Profil
    public function hapusFoto()
    {
        $user = User::find(Auth::id());

        if ($user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);
            $user->update(['foto_profil' => null]);
        }

        return back()->with('sukses', 'Foto profil berhasil dihapus!');
    }

    // Hapus SEMUA Riwayat Log Aktivitas Pribadi
    public function hapusLog()
    {
        LogAktivitas::where('user_id', Auth::id())->delete();
        return back()->with('sukses', 'Seluruh riwayat aktivitas Anda berhasil dibersihkan!');
    }

    // Hapus SALAH SATU Riwayat Log Aktivitas
    public function hapusLogSatu($id)
    {
        $log = LogAktivitas::where('user_id', Auth::id())->findOrFail($id);
        $log->delete();

        return back()->with('sukses', 'Satu riwayat aktivitas berhasil dihapus.');
    }
}
