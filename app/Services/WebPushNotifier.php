<?php

namespace App\Services;

use App\Models\LaporanKeluhan;
use App\Models\User;
use App\Notifications\LaporanBaruNotification;
use Illuminate\Support\Facades\Log;
use Throwable;

class WebPushNotifier
{
    public function notifyAdminsOfNewReport(LaporanKeluhan $laporan): void
    {
        $admins = User::query()
            ->adminJalan()
            ->where('status_akun', 'aktif')
            ->whereHas('pushSubscriptions')
            ->with('pushSubscriptions')
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        if (! config('webpush.vapid.public_key') || ! config('webpush.vapid.private_key')) {
            Log::warning('Web Push dilewati karena kunci VAPID belum dikonfigurasi.', [
                'laporan_id' => $laporan->getKey(),
            ]);

            return;
        }

        foreach ($admins as $admin) {
            try {
                $admin->notify(new LaporanBaruNotification($laporan));
            } catch (Throwable $exception) {
                Log::warning('Web Push laporan baru gagal dikirim ke admin.', [
                    'admin_id' => $admin->getKey(),
                    'laporan_id' => $laporan->getKey(),
                    'exception' => $exception,
                ]);
            }
        }
    }
}
