<?php

namespace App\Notifications;

use App\Models\LaporanKeluhan;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class LaporanBaruNotification extends Notification
{
    public function __construct(private readonly LaporanKeluhan $laporan) {}

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Laporan Baru Masuk')
            ->body($this->laporan->judul_laporan)
            ->icon('/favicon.ico')
            ->tag('laporan-'.$this->laporan->getKey())
            ->data([
                'url' => route('admin_jalan.laporan.detail', $this->laporan->getKey(), false),
            ])
            ->options(['TTL' => 300]);
    }
}
