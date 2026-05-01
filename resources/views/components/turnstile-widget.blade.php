@php
    use App\Services\TurnstileService;
    $turnstile = new TurnstileService();
@endphp

@if($turnstile->isEnabled())
    <div class="mb-4">
        <div class="cf-turnstile"
             data-sitekey="{{ $turnstile->getSiteKey() }}"
             data-theme="{{ $theme ?? 'light' }}"
             data-callback="handleTurnstileCallback">
        </div>
        <input type="hidden" name="cf-turnstile-response" id="cf-turnstile-response" wire:model.defer="data.cf-turnstile-response">
    </div>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script>
        function handleTurnstileCallback(token) {
            // Mettre à jour l'input pour les formulaires classiques
            const input = document.getElementById('cf-turnstile-response');
            if (input) {
                input.value = token;
            }

            // Dispatcher un événement pour Livewire
            const event = new Event('turnstile-verified', { bubbles: true });
            document.dispatchEvent(event);
        }

        // Gestion de l'erreur Turnstile
        window.onTurnstileError = function(errorCode) {
            console.error('Turnstile error:', errorCode);
        };
    </script>
@endif
