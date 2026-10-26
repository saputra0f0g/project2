<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use NotificationChannels\WebPush\Events\NotificationFailed;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('http');

        Event::listen(NotificationFailed::class, function (NotificationFailed $event): void {
            Log::warning('Web Push ditolak oleh layanan push.', [
                'subscription_id' => $event->subscription->getKey(),
                'reason' => $event->report->getReason(),
                'status' => $event->report->getResponse()?->getStatusCode(),
            ]);
        });
    }
}
