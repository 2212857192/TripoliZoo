<?php

namespace App\Console\Commands;

use App\Models\DeviceToken;
use App\Services\FcmAccessTokenProvider;
use App\Services\FcmPushService;
use Illuminate\Console\Command;

class FcmDiagnose extends Command
{
    protected $signature = 'fcm:diagnose';

    protected $description = 'Check Firebase push notification configuration';

    public function handle(FcmPushService $fcm, FcmAccessTokenProvider $tokens): int
    {
        $credentials = config('services.fcm.credentials');
        $projectId = config('services.fcm.project_id');

        $this->line('FCM project: '.($projectId ?: '—'));
        $this->line('Credentials: '.($credentials ?: '—'));
        $this->line('Credentials readable: '.(is_string($credentials) && is_readable($credentials) ? 'yes' : 'no'));
        $this->line('FCM configured: '.($fcm->isConfigured() ? 'yes' : 'no'));

        $accessToken = $tokens->getAccessToken();
        $this->line('Access token: '.($accessToken ? 'ok' : 'missing'));

        $deviceTokens = DeviceToken::query()->count();
        $this->line('Registered device tokens: '.$deviceTokens);

        if ($deviceTokens === 0) {
            $this->warn('No device tokens — open the app on a phone, log in as doctor/supervisor, and grant notification permission.');
        }

        if (! $fcm->isConfigured()) {
            $this->error('Fix FCM_PROJECT_ID and FCM_CREDENTIALS in .env');

            return self::FAILURE;
        }

        if (! $accessToken) {
            $this->error('Cannot obtain FCM access token — check the service account JSON file.');

            return self::FAILURE;
        }

        $this->info('Server-side FCM looks ready.');

        return self::SUCCESS;
    }
}
