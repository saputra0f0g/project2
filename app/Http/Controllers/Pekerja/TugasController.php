<?php

namespace App\Http\Controllers\Pekerja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PenugasanPekerja;
use App\Models\LaporanKeluhan;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    // Menampilkan daftar tugas untuk pekerja yang sedang login
    public function indeks()
    {
        $user = Auth::user();

        // Ambil HANYA tugas yang diberikan khusus untuk pekerja ini (filter ketat by id_pekerja).
        // Kecualikan tugas yang sudah dibatalkan admin agar tidak muncul di beranda.
        $daftar_tugas = PenugasanPekerja::with(['laporan.pelapor', 'admin'])
            ->where('id_pekerja', $user->id)
            ->whereNotIn('status_tugas', ['dibatalkan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pekerja.beranda', compact('daftar_tugas'));
    }

    // Memperbarui progres pekerjaan di lapangan
    public function updateProgres(Request $request, $id)
    {
        $request->validate([
            'progres_persen' => 'required|numeric|min:0|max:100',
            'status_tugas'   => 'required|in:dikerjakan,terkendala,selesai',
        ]);

        // Pastikan tugas ini benar-benar milik pekerja yang sedang login (security check)
        $penugasan = PenugasanPekerja::where('id', $id)
            ->where('id_pekerja', Auth::id())
            ->firstOrFail();

        $statusTugas = $request->status_tugas;
        $progres = $request->progres_persen;

        // Pekerja tidak boleh langsung menyelesaikan tugas sendiri (harus via validasi Admin Bidang)
        if ($progres == 100 || $statusTugas === 'selesai') {
            $statusTugas = 'menunggu_review';
            $progres = 100;
        }

        $penugasan->update([
            'progres_persen' => $progres,
            'status_tugas'   => $statusTugas,
        ]);

        $laporan = LaporanKeluhan::find($penugasan->id_laporan);
        if ($laporan) {
            if ($statusTugas === 'menunggu_review') {
                $laporan->update(['status' => 'menunggu_validasi']);
            } elseif ($statusTugas === 'terkendala') {
                $laporan->update(['status' => 'terkendala']);
            } elseif ($statusTugas === 'dikerjakan') {
                $laporan->update(['status' => 'proses']);
            }
        }

        return redirect()->route('pekerja.beranda')->with('sukses', 'Progres pekerjaan berhasil diperbarui!');
    }
}
