<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (! $notifiable->fcm_token) {
            return;
        }

        $message = $notification->toFcm($notifiable);

        if (empty($message)) {
            return;
        }

        $this->sendFcmNotification($notifiable->fcm_token, $message);
    }

    /**
     * Send FCM notification via HTTP API.
     */
    protected function sendFcmNotification(string $token, array $message): void
    {
        $serverKey = config('services.fcm.server_key');

        if (empty($serverKey)) {
            Log::warning('FCM server key not configured');
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $token,
                'notification' => [
                    'title' => $message['title'] ?? '',
                    'body' => $message['body'] ?? '',
                    'icon' => $message['icon'] ?? '/icon-192x192.png',
                    'click_action' => $message['click_action'] ?? url('/'),
                ],
                'data' => $message['data'] ?? [],
                'priority' => 'high',
            ]);

            if ($response->failed()) {
                Log::error('FCM notification failed', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('FCM notification exception', [
                'message' => $e->getMessage(),
                'token' => substr($token, 0, 20) . '...',
            ]);
        }
    }
}
