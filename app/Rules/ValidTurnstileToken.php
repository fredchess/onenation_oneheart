<?php

namespace App\Rules;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTurnstileToken implements ValidationRule
{
    public function __construct(
        private TurnstileService $turnstileService = new TurnstileService()
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->turnstileService->isEnabled()) {
            return;
        }

        if (empty($value)) {
            $fail('Le captcha est requis.');
            return;
        }

        if (!$this->turnstileService->verify($value)) {
            $fail('La vérification du captcha a échoué. Veuillez réessayer.');
        }
    }
}
