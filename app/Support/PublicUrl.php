<?php

namespace App\Support;

use Illuminate\Http\Request;

class PublicUrl
{
    public static function absolute(string $path, ?string $origin = null): string
    {
        $path = '/'.ltrim($path, '/');

        if ($configured = config('app.visitor_public_url')) {
            return rtrim((string) $configured, '/').$path;
        }

        if ($origin !== null && self::isAllowedOrigin($origin) && ! self::isLocalHost($origin)) {
            return rtrim($origin, '/').$path;
        }

        return rtrim(self::baseUrl(), '/').$path;
    }

    public static function qrBaseUrl(?string $browserOrigin = null): string
    {
        if ($configured = config('app.visitor_public_url')) {
            return rtrim((string) $configured, '/');
        }

        if ($browserOrigin !== null && self::isAllowedOrigin($browserOrigin) && ! self::isLocalHost($browserOrigin)) {
            return rtrim($browserOrigin, '/');
        }

        return rtrim(self::baseUrl(), '/');
    }

    public static function isAllowedOrigin(?string $origin): bool
    {
        if (! $origin) {
            return false;
        }

        $parts = parse_url($origin);

        if (! is_array($parts) || ! isset($parts['scheme'], $parts['host'])) {
            return false;
        }

        if (! in_array($parts['scheme'], ['http', 'https'], true)) {
            return false;
        }

        return filter_var($origin, FILTER_VALIDATE_URL) !== false;
    }

    public static function baseUrl(): string
    {
        if ($configured = config('app.visitor_public_url')) {
            return rtrim((string) $configured, '/');
        }

        $request = request();
        if ($request instanceof Request && ! self::isLocalHost($request->getSchemeAndHttpHost())) {
            return rtrim($request->getSchemeAndHttpHost(), '/');
        }

        return rtrim((string) config('app.url'), '/');
    }

    public static function isLocalOnly(?string $url = null): bool
    {
        return self::isLocalHost($url ?? self::baseUrl());
    }

    public static function isLocalHost(?string $url): bool
    {
        if (! $url) {
            return true;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return in_array($host, ['localhost', '127.0.0.1', '::1'], true);
    }
}
