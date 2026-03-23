<?php

namespace App\Services;

use App\Models\CommunityNotification;
use App\Models\PushSubscription;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class PushNotificationService
{
    public function sendForNotification(CommunityNotification $notification): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        $subscriptions = PushSubscription::query()
            ->where('user_id', $notification->user_id)
            ->get();

        foreach ($subscriptions as $subscription) {
            $this->sendToSubscription($subscription, $notification);
        }
    }

    public function sendToSubscription(PushSubscription $subscription, CommunityNotification $notification): void
    {
        try {
            $accessToken = $this->resolveAccessToken();
            $projectId = $this->projectId();

            if (! $accessToken || ! $projectId) {
                return;
            }

            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => [
                        'token' => $subscription->token,
                        'notification' => [
                            'title' => $notification->title,
                            'body' => $notification->body,
                        ],
                        'data' => [
                            'notification_id' => (string) $notification->id,
                            'type' => (string) $notification->type,
                            'action_url' => (string) ($notification->action_url ?? '/notifications'),
                        ],
                        'android' => [
                            'priority' => 'high',
                        ],
                        'apns' => [
                            'headers' => [
                                'apns-priority' => '10',
                            ],
                            'payload' => [
                                'aps' => [
                                    'sound' => 'default',
                                ],
                            ],
                        ],
                        'webpush' => [
                            'notification' => [
                                'icon' => config('services.firebase.icon'),
                                'badge' => config('services.firebase.badge'),
                                'tag' => 'kdc-'.$notification->type,
                                'data' => [
                                    'action_url' => $notification->action_url ?? '/notifications',
                                ],
                            ],
                            'fcm_options' => [
                                'link' => $this->normalizeActionUrl($notification->action_url),
                            ],
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $subscription->forceFill([
                    'last_sent_at' => now(),
                    'last_error_at' => null,
                    'last_error_message' => null,
                ])->save();

                return;
            }

            $subscription->forceFill([
                'last_error_at' => now(),
                'last_error_message' => $response->body(),
            ])->save();

            if (in_array($response->status(), [404, 410], true) || str_contains($response->body(), 'UNREGISTERED')) {
                $subscription->delete();
            }
        } catch (Throwable $exception) {
            $subscription->forceFill([
                'last_error_at' => now(),
                'last_error_message' => $exception->getMessage(),
            ])->save();
        }
    }

    public function isConfigured(): bool
    {
        return (bool) ($this->serviceAccount()['client_email'] ?? null) && (bool) $this->projectId();
    }

    private function resolveAccessToken(): ?string
    {
        return Cache::remember('firebase.push.access_token', now()->addMinutes(50), function () {
            $account = $this->serviceAccount();

            if (! ($account['client_email'] ?? null) || ! ($account['private_key'] ?? null)) {
                return null;
            }

            $now = time();
            $jwt = JWT::encode([
                'iss' => $account['client_email'],
                'sub' => $account['client_email'],
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            ], $account['private_key'], 'RS256');

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            return $response->successful() ? $response->json('access_token') : null;
        });
    }

    private function serviceAccount(): array
    {
        $rawJson = config('services.firebase.service_account_json');
        $path = config('services.firebase.service_account_path');

        if ($rawJson) {
            return json_decode($rawJson, true) ?: [];
        }

        if ($path && is_file($path)) {
            return json_decode((string) file_get_contents($path), true) ?: [];
        }

        return [];
    }

    private function projectId(): ?string
    {
        return config('services.firebase.project_id') ?: ($this->serviceAccount()['project_id'] ?? null);
    }

    private function normalizeActionUrl(?string $actionUrl): string
    {
        $frontendUrl = rtrim(config('services.frontend.url'), '/');

        if (! $actionUrl) {
            return $frontendUrl.'/#/notifications';
        }

        if (str_starts_with($actionUrl, 'http://') || str_starts_with($actionUrl, 'https://')) {
            return $actionUrl;
        }

        return $frontendUrl.'/#'.(str_starts_with($actionUrl, '/') ? $actionUrl : '/'.$actionUrl);
    }
}
