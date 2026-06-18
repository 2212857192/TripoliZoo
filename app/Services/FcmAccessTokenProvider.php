<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FcmAccessTokenProvider
{
  private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

  private const TOKEN_URL = 'https://oauth2.googleapis.com/token';

  public function getAccessToken(): ?string
  {
    $credentialsPath = config('services.fcm.credentials');

    if (! filled($credentialsPath) || ! is_readable($credentialsPath)) {
      return null;
    }

    return Cache::remember('fcm_access_token', 50 * 60, function () use ($credentialsPath) {
      return $this->requestAccessToken($credentialsPath);
    });
  }

  private function requestAccessToken(string $credentialsPath): ?string
  {
    try {
      $credentials = json_decode((string) file_get_contents($credentialsPath), true, 512, JSON_THROW_ON_ERROR);

      $clientEmail = $credentials['client_email'] ?? null;
      $privateKey = $credentials['private_key'] ?? null;

      if (! is_string($clientEmail) || ! is_string($privateKey)) {
        throw new RuntimeException('Invalid Firebase service account JSON.');
      }

      $now = time();
      $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'], JSON_THROW_ON_ERROR));
      $claimSet = $this->base64UrlEncode(json_encode([
        'iss' => $clientEmail,
        'scope' => self::SCOPE,
        'aud' => self::TOKEN_URL,
        'iat' => $now,
        'exp' => $now + 3600,
      ], JSON_THROW_ON_ERROR));

      $unsignedJwt = $header.'.'.$claimSet;
      $signature = '';
      $signed = openssl_sign($unsignedJwt, $signature, $privateKey, OPENSSL_ALGO_SHA256);

      if (! $signed) {
        throw new RuntimeException('Unable to sign Firebase JWT.');
      }

      $jwt = $unsignedJwt.'.'.$this->base64UrlEncode($signature);

      $response = Http::asForm()->post(self::TOKEN_URL, [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt,
      ]);

      if ($response->failed()) {
        Log::warning('FCM token request failed', [
          'status' => $response->status(),
          'body' => $response->body(),
        ]);

        return null;
      }

      return $response->json('access_token');
    } catch (\Throwable $e) {
      Log::error('FCM token exception', ['message' => $e->getMessage()]);

      return null;
    }
  }

  private function base64UrlEncode(string $value): string
  {
    return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
  }
}
