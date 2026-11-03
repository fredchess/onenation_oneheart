@php
    $flash = null;
    if (request()->input('success') === 'true') {
        $flash = ['ok', 'Paiement effectué avec succès. Merci pour votre don !'];
    } elseif (request()->input('success') === 'false') {
        $flash = ['error', "Le paiement n'a pas abouti. Vous pouvez réessayer."];
    } elseif (session('error')) {
        $flash = ['error', session('error')];
    } elseif (session('success')) {
        $flash = ['ok', session('success')];
    }
@endphp

@if ($flash)
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 7000)" x-show="show" x-transition.opacity.duration.300ms
         class="fixed inset-x-0 top-20 z-50 flex justify-center px-4 lg:top-24" role="status">
        <div class="flex max-w-lg items-start gap-3 rounded-box px-5 py-4 text-white shadow-2xl {{ $flash[0] === 'ok' ? 'bg-[#2f7d32]' : 'bg-coral' }}">
            <x-onoh.icon :name="$flash[0] === 'ok' ? 'check' : 'close'" class="mt-0.5 size-5 shrink-0" />
            <p class="font-semibold">{{ $flash[1] }}</p>
            <button type="button" class="-mr-1 ml-2 opacity-80 hover:opacity-100" @click="show = false" aria-label="Fermer">
                <x-onoh.icon name="close" class="size-5" />
            </button>
        </div>
    </div>
@endif
