<?php

namespace Tests\Unit;

use App\Support\PublicUrl;
use Illuminate\Http\Request;
use Tests\TestCase;

class PublicUrlTest extends TestCase
{
    public function test_uses_visitor_public_url_when_configured(): void
    {
        config([
            'app.url' => 'http://127.0.0.1:8000',
            'app.visitor_public_url' => 'http://192.168.1.20:8000',
        ]);

        $this->assertSame(
            'http://192.168.1.20:8000/app/animals/5',
            PublicUrl::absolute('/app/animals/5'),
        );
    }

    public function test_uses_request_host_when_app_url_is_localhost(): void
    {
        config([
            'app.url' => 'http://127.0.0.1:8000',
            'app.visitor_public_url' => null,
        ]);

        $request = Request::create('http://192.168.1.44:8000/admin/animals', 'GET');
        $this->app->instance('request', $request);
        \Illuminate\Support\Facades\Request::swap($request);

        $this->assertSame(
            'http://192.168.1.44:8000/app/animals/5',
            PublicUrl::absolute('/app/animals/5'),
        );
    }

    public function test_detects_local_only_urls(): void
    {
        $this->assertTrue(PublicUrl::isLocalOnly('http://127.0.0.1:8000/app/animals/1'));
        $this->assertFalse(PublicUrl::isLocalOnly('http://192.168.1.44:8000/app/animals/1'));
    }

    public function test_absolute_url_can_use_explicit_origin(): void
    {
        $this->assertSame(
            'http://192.168.7.3:8000/app/animals/5',
            PublicUrl::absolute('/app/animals/5', 'http://192.168.7.3:8000'),
        );
    }

    public function test_visitor_public_url_overrides_browser_origin(): void
    {
        config([
            'app.url' => 'http://127.0.0.1:8000',
            'app.visitor_public_url' => 'http://192.168.7.3:8000',
        ]);

        $this->assertSame(
            'http://192.168.7.3:8000/app/animals/5',
            PublicUrl::absolute('/app/animals/5', 'http://127.0.0.1:8000'),
        );
    }
}
