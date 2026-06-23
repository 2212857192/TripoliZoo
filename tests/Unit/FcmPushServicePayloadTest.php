<?php

namespace Tests\Unit;

use App\Services\FcmAccessTokenProvider;
use App\Services\FcmPushService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FcmPushServicePayloadTest extends TestCase
{
    public function test_push_payload_includes_background_fields_for_mobile_apps(): void
    {
        $credentialsPath = storage_path('framework/testing-fcm-creds.json');
        if (! is_dir(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0777, true);
        }
        file_put_contents($credentialsPath, '{}');

        config([
            'services.fcm.project_id' => 'tripoli-zoo-test',
            'services.fcm.credentials' => $credentialsPath,
        ]);

        $this->mock(FcmAccessTokenProvider::class, function ($mock): void {
            $mock->shouldReceive('getAccessToken')->once()->andReturn('test-access-token');
        });

        Http::fake([
            'fcm.googleapis.com/*' => Http::response(['name' => 'projects/test/messages/1'], 200),
        ]);

        app(FcmPushService::class)->sendToToken(
            'device-token-123',
            'حالة حجر جديدة',
            'تم تسجيل حالة حجر صحي',
            ['route' => '/doctor/quarantine/Q-100'],
        );

        Http::assertSent(function ($request): bool {
            $payload = $request->data();
            $message = $payload['message'] ?? [];

            return ($message['notification']['title'] ?? null) === 'حالة حجر جديدة'
                && ($message['notification']['body'] ?? null) === 'تم تسجيل حالة حجر صحي'
                && ($message['data']['route'] ?? null) === '/doctor/quarantine/Q-100'
                && ($message['data']['click_action'] ?? null) === 'FLUTTER_NOTIFICATION_CLICK'
                && ($message['android']['notification']['channel_id'] ?? null) === 'quarantine_alerts'
                && ($message['android']['priority'] ?? null) === 'HIGH'
                && ($message['apns']['payload']['aps']['content-available'] ?? null) === 1;
        });
    }
}
