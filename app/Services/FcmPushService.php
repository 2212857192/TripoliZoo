<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmPushService
{
  public function __construct(private FcmAccessTokenProvider $tokens) {}

  public function isConfigured(): bool
  {
    return filled(config('services.fcm.project_id'))
      && filled(config('services.fcm.credentials'))
      && is_readable((string) config('services.fcm.credentials'));
  }

  /**
   * @param  Collection<int, User>|array<int, User>  $users
   * @param  array<string, string>  $data
   */
  public function sendToUsers(Collection|array $users, string $title, string $body, array $data = []): void
  {
    if (! $this->isConfigured()) {
      Log::warning('FCM push skipped: configure FCM_PROJECT_ID and FCM_CREDENTIALS in .env');

      return;
    }

    $userIds = collect($users)->pluck('id')->filter()->unique()->values();

    if ($userIds->isEmpty()) {
      return;
    }

    $tokens = DeviceToken::query()
      ->whereIn('user_id', $userIds)
      ->pluck('token')
      ->unique()
      ->values();

    if ($tokens->isEmpty()) {
      Log::info('FCM push skipped: no device tokens for users', [
        'user_ids' => $userIds->all(),
      ]);

      return;
    }

    foreach ($tokens as $token) {
      $this->sendToToken($token, $title, $body, $data);
    }
  }

  /** @param  array<string, string>  $data */
  public function sendToToken(string $token, string $title, string $body, array $data = []): void
  {
    if (! $this->isConfigured()) {
      return;
    }

    $accessToken = $this->tokens->getAccessToken();

    if (! filled($accessToken)) {
      Log::warning('FCM push skipped: no access token');

      return;
    }

    $stringData = [];
    foreach (array_merge(['click_action' => 'FLUTTER_NOTIFICATION_CLICK'], $data) as $key => $value) {
      $stringData[(string) $key] = is_scalar($value) ? (string) $value : json_encode($value);
    }

    $payload = [
      'message' => [
        'token' => $token,
        'notification' => [
          'title' => $title,
          'body' => $body,
        ],
        'data' => $stringData,
        'android' => [
          'priority' => 'HIGH',
          'ttl' => '86400s',
          'notification' => [
            'channel_id' => 'quarantine_alerts',
            'sound' => 'default',
            'notification_priority' => 'PRIORITY_MAX',
            'visibility' => 'PUBLIC',
            'default_vibrate_timings' => true,
            'default_sound' => true,
          ],
        ],
        'apns' => [
          'headers' => [
            'apns-priority' => '10',
          ],
          'payload' => [
            'aps' => [
              'alert' => [
                'title' => $title,
                'body' => $body,
              ],
              'sound' => 'default',
              'content-available' => 1,
            ],
          ],
        ],
      ],
    ];

    $projectId = config('services.fcm.project_id');
    $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

    try {
      $response = Http::withToken($accessToken)
        ->acceptJson()
        ->post($url, $payload);

      if ($response->failed()) {
        Log::warning('FCM push failed', [
          'status' => $response->status(),
          'body' => $response->body(),
        ]);
      }
    } catch (\Throwable $e) {
      Log::error('FCM push exception', ['message' => $e->getMessage()]);
    }
  }
}
