<?php

namespace Tests\Unit\Services;

use App\Services\TurnstileService;
use PHPUnit\Framework\TestCase;

class TurnstileServiceTest extends TestCase
{
    private TurnstileService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TurnstileService();
    }

    public function test_is_disabled_when_no_keys_configured()
    {
        config(['services.turnstile.site_key' => null]);
        config(['services.turnstile.secret_key' => null]);

        $this->assertFalse($this->service->isEnabled());
    }

    public function test_is_enabled_when_keys_configured()
    {
        config(['services.turnstile.site_key' => 'test-site-key']);
        config(['services.turnstile.secret_key' => 'test-secret-key']);

        $service = new TurnstileService();
        $this->assertTrue($service->isEnabled());
    }

    public function test_returns_site_key()
    {
        $siteKey = 'test-site-key-123';
        config(['services.turnstile.site_key' => $siteKey]);

        $service = new TurnstileService();
        $this->assertEquals($siteKey, $service->getSiteKey());
    }

    public function test_verify_returns_true_when_disabled()
    {
        config(['services.turnstile.site_key' => null]);
        config(['services.turnstile.secret_key' => null]);

        $service = new TurnstileService();
        $this->assertTrue($service->verify('any-token'));
    }
}
