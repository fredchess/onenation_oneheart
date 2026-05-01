<?php

namespace App\Filament\Pages\Auth;

use App\Services\TurnstileService;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected static ?string $navigationLabel = null;

    public function form(Form $form): Form
    {
        $schema = [
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
        ];

        $turnstile = new TurnstileService();
        if ($turnstile->isEnabled()) {
            $schema[] = View::make('components.turnstile-widget')
                ->viewData(['theme' => 'light']);
            $schema[] = Hidden::make('cf-turnstile-response');
        }

        return $form
            ->schema($schema)
            ->statePath('data');
    }

    public function authenticate(): ?LoginResponse
    {
        $turnstile = new TurnstileService();

        // Valider le token Turnstile si activé
        if ($turnstile->isEnabled()) {
            $token = $this->getFormData()['cf-turnstile-response'] ?? null;

            if (empty($token) || !$turnstile->verify($token)) {
                throw ValidationException::withMessages([
                    'cf-turnstile-response' => 'La vérification du captcha a échoué. Veuillez réessayer.',
                ]);
            }
        }

        return parent::authenticate();
    }

    protected function getFormData(): array
    {
        return $this->form->getState();
    }
}
