<?php

namespace App\Support;

use Illuminate\Http\Request;

class ApiStorageUrl
{
    public static function fromPublicPath(?string $path, ?Request $request = null): ?string
    {
        if (! $path) {
            return null;
        }

        $request ??= request();
        $origin = $request instanceof Request
            ? $request->getSchemeAndHttpHost()
            : PublicUrl::baseUrl();

        return rtrim($origin, '/').'/api/storage/'.ltrim($path, '/');
    }
}
