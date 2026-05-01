<?php

namespace App\Http\Traits;

use App\Rules\ValidTurnstileToken;
use Illuminate\Validation\Validator;

trait ValidateTurnstile
{
    /**
     * Ajoute la validation Turnstile aux règles de validation existantes.
     *
     * @param array $rules Règles de validation existantes
     * @return array Règles avec validation Turnstile ajoutée
     */
    protected function withTurnstileValidation(array $rules): array
    {
        return array_merge($rules, [
            'cf-turnstile-response' => [new ValidTurnstileToken()],
        ]);
    }

    /**
     * Crée un validateur avec support Turnstile.
     *
     * @param array $rules Règles de validation
     * @param array $messages Messages d'erreur personnalisés
     * @return Validator
     */
    protected function validateWithTurnstile(array $rules, array $messages = []): Validator
    {
        return validator(request()->all(), $this->withTurnstileValidation($rules), $messages);
    }
}
