<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileService
{
    private string $secretKey;
    private string $siteKey;
    private string $verifyEndpoint = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function __construct()
    {
        $this->secretKey = config('services.turnstile.secret_key');
        $this->siteKey = config('services.turnstile.site_key');
    }

    public function getSiteKey(): string
    {
        return $this->siteKey;
    }

    public function isEnabled(): bool
    {
        return !empty($this->secretKey) && !empty($this->siteKey);
    }

    public function verify(string $token): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }

        try {
            $response = Http::timeout(10)->post($this->verifyEndpoint, [
                'secret' => $this->secretKey,
                'response' => $token,
            ]);

            return $response->successful() && $response->json('success') === true;
        } catch (\Exception $e) {
            \Log::error('Turnstile verification failed', [
                'error' => $e->getMessage(),
                'token' => substr($token, 0, 10) . '...',
            ]);

            return false;
        }
    }
}
