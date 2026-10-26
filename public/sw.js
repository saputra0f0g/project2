self.addEventListener('push', (event) => {
    let payload = {};

    if (event.data) {
        try {
            payload = event.data.json();
        } catch {
            payload = { body: event.data.text() };
        }
    }

    const options = {
        body: payload.body || 'Ada laporan kerusakan jalan baru.',
        icon: payload.icon || '/favicon.ico',
        badge: payload.badge || '/favicon.ico',
        tag: payload.tag || 'sigap-laporan',
        data: payload.data || {},
    };

    event.waitUntil(
        self.registration.showNotification(payload.title || 'Laporan Baru Masuk', options)
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = new URL(event.notification.data?.url || '/', self.location.origin);
    if (targetUrl.origin !== self.location.origin) {
        return;
    }

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clients) => {
            for (const client of clients) {
                if ('focus' in client) {
                    client.navigate(targetUrl.href);
                    return client.focus();
                }
            }

            return self.clients.openWindow(targetUrl.href);
        })
    );
});
