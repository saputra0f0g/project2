<?php

namespace App\Http\Controllers\AdminJalan;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => [
                'required',
                'string',
                'url',
                'max:1024',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (parse_url($value, PHP_URL_SCHEME) !== 'https') {
                        $fail('Endpoint push harus menggunakan HTTPS.');
                    }
                },
            ],
            'keys.p256dh' => 'required|string|max:255',
            'keys.auth' => 'required|string|max:255',
            'content_encoding' => 'nullable|in:aes128gcm,aesgcm',
        ]);

        $request->user()->updatePushSubscription(
            $validated['endpoint'],
            $validated['keys']['p256dh'],
            $validated['keys']['auth'],
            $validated['content_encoding'] ?? null
        );

        return response()->json(['message' => 'Notifikasi push berhasil diaktifkan.']);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => [
                'required',
                'string',
                'url',
                'max:1024',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (parse_url($value, PHP_URL_SCHEME) !== 'https') {
                        $fail('Endpoint push harus menggunakan HTTPS.');
                    }
                },
            ],
        ]);

        $request->user()->deletePushSubscription($validated['endpoint']);

        return response()->json(['message' => 'Notifikasi push berhasil dinonaktifkan.']);
    }
}
