<?php

namespace App\Filament\Pages\Auth;

use App\Services\TurnstileService;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getTurnstileComponent(),
            ])
            ->statePath('data');
    }

    protected function getTurnstileComponent(): ?Component
    {
        $turnstile = new TurnstileService();

        if (!$turnstile->isEnabled()) {
            return null;
        }

        return Component::make()
            ->view('components.turnstile-widget', ['theme' => 'light']);
    }

    public function authenticate(): ?LoginResponse
    {
        $turnstile = new TurnstileService();

        // Valider le token Turnstile si activé
        if ($turnstile->isEnabled()) {
            $token = request()->input('cf-turnstile-response') ??
                     request()->input('data.cf-turnstile-response');

            if (empty($token) || !$turnstile->verify($token)) {
                throw ValidationException::withMessages([
                    'cf-turnstile-response' => 'La vérification du captcha a échoué. Veuillez réessayer.',
                ]);
            }
        }

        return parent::authenticate();
    }
}
