{{-- Formulaire de don : mêmes champs que components.donation-form, en 3 étapes --}}
@php
    $raised = isset($orphelinat)
        ? $orphelinat->dons->where('payment_status', \App\Enums\PaymentStatus::SUCCESS)->sum('amount')
        : ($total_donations ?? 0);
    $presets = [2000, 5000, 10000, 25000];
    $initialAmount = old('amount', old('amount_card', 5000));
@endphp
<div class="overflow-hidden rounded-lg border border-rule bg-white" x-intersect:enter="donateInView = true" x-intersect:leave="donateInView = false">
    <div class="band"></div>
    <div class="ndop flex flex-wrap items-end justify-between gap-x-4 gap-y-2 px-5 py-4 text-white sm:px-6 sm:py-5">
        <h2 class="display whitespace-nowrap text-[2.1rem]">Faire un don</h2>
        <p class="whitespace-nowrap text-[13px] text-sky">
            <strong class="block font-display text-[1.9rem] leading-none font-extrabold tabular-nums text-white sm:text-[2.1rem]">{{ number_format($raised, 0, ',', ' ') }} <span class="text-lg">FCFA</span></strong>
            {{ isset($orphelinat) ? 'collectés pour cet orphelinat' : 'récoltés grâce à vous' }}
        </p>
    </div>

    <form method="POST" action="{{ route('public.donation') }}" id="donation_form" class="grid gap-5 px-5 pb-6 pt-5 sm:px-6"
          x-data="{ mode: '{{ old('payment_mode', 'momo') }}', amount: '{{ $initialAmount }}' }">
        @csrf
        <input type="hidden" name="donate_option" value="{{ \App\Enums\DonationTypeEnum::FINANCIAL->value }}">
        <input type="hidden" name="payment_mode" :value="mode">
        @isset($orphelinat)
            <input type="hidden" name="orphanage_id" value="{{ $orphelinat->id }}">
        @endisset

        @if ($errors->any())
            <ul class="rounded-ui bg-coral/10 px-4 py-3 text-sm font-medium text-coral-deep" role="alert">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        @endif

        <fieldset class="grid gap-2.5">
            <legend class="mb-2.5 flex items-baseline gap-2.5 text-[13px] font-bold uppercase tracking-[.1em] text-indigo"><span class="font-display text-lg text-coral">1</span> Montant</legend>
            <div class="grid grid-cols-4 gap-1.5">
                @foreach ($presets as $preset)
                    <button type="button" @click="amount = '{{ $preset }}'"
                            class="h-12 rounded-ui border text-[15px] font-semibold tabular-nums transition-colors"
                            :class="amount == '{{ $preset }}' ? 'border-2 border-indigo bg-paper text-indigo' : 'border-rule text-ink hover:border-indigo'"
                            :aria-pressed="(amount == '{{ $preset }}').toString()">{{ number_format($preset, 0, ',', ' ') }}</button>
                @endforeach
            </div>
            <label class="relative block">
                <span class="sr-only">Autre montant en FCFA</span>
                <input type="number" min="100" step="100" inputmode="numeric" class="field pr-16 tabular-nums" placeholder="Autre montant"
                       :name="mode === 'momo' ? 'amount' : 'amount_card'" x-model="amount" required>
                <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-[13px] font-semibold text-slate">FCFA</span>
            </label>
        </fieldset>

        <div class="grid gap-2.5">
            <p class="flex items-baseline gap-2.5 text-[13px] font-bold uppercase tracking-[.1em] text-indigo" id="pay-title"><span class="font-display text-lg text-coral">2</span> Moyen de paiement</p>
            <div class="tablist" role="tablist" aria-labelledby="pay-title">
                <button type="button" role="tab" class="tab" id="tab-momo" aria-controls="panel-momo" :aria-selected="(mode === 'momo').toString()" @click="mode = 'momo'">Mobile Money</button>
                <button type="button" role="tab" class="tab" id="tab-card" aria-controls="panel-card" :aria-selected="(mode === 'card').toString()" @click="mode = 'card'">Carte bancaire</button>
            </div>
            <div id="panel-momo" role="tabpanel" aria-labelledby="tab-momo" x-show="mode === 'momo'" class="grid gap-2">
                <label class="flex items-stretch overflow-hidden rounded-ui border border-rule focus-within:border-indigo focus-within:ring-2 focus-within:ring-indigo/15">
                    <span class="grid place-items-center border-r border-rule bg-paper px-3 text-sm font-semibold text-slate">+237</span>
                    <span class="sr-only">Numéro Orange Money ou MTN MoMo</span>
                    <input id="phone" type="tel" name="tel" inputmode="tel" autocomplete="tel-national" value="{{ old('tel') }}"
                           placeholder="6 XX XX XX XX" class="min-h-12 w-full min-w-0 px-3.5 text-[15px] focus:outline-none"
                           :required="mode === 'momo'" :disabled="mode !== 'momo'">
                </label>
                <p class="flex flex-wrap items-center gap-1.5 text-[13px] text-slate">Accepté :
                    <span class="mark bg-[#ff7900] text-white">Orange Money</span><span class="mark bg-[#ffcb05] text-[#111]">MTN MoMo</span></p>
            </div>
            <div id="panel-card" role="tabpanel" aria-labelledby="tab-card" x-show="mode === 'card'" x-cloak class="grid gap-2">
                <p class="text-sm">Vous saisirez votre carte sur la page de paiement sécurisée.</p>
                <p class="flex flex-wrap items-center gap-1.5 text-[13px] text-slate">Accepté :
                    <span class="mark bg-[#1a1f71] text-white">VISA</span><span class="mark border border-rule bg-white text-[#111]">Mastercard</span></p>
            </div>
        </div>

        <fieldset class="grid gap-2">
            <legend class="mb-2.5 flex items-baseline gap-2.5 text-[13px] font-bold uppercase tracking-[.1em] text-indigo"><span class="font-display text-lg text-coral">3</span> Vos coordonnées</legend>
            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                <label><span class="sr-only">Nom complet</span><input type="text" name="name" class="field" placeholder="Nom complet" autocomplete="name" value="{{ old('name') }}" required></label>
                <label><span class="sr-only">Adresse email</span><input type="email" name="email" class="field" placeholder="Adresse email" autocomplete="email" value="{{ old('email') }}" required></label>
            </div>
        </fieldset>

        {{-- Turnstile en mode invisible : le widget ne s'affiche pas --}}
        <div class="[&_.mb-4]:mb-0 empty:hidden">@include('components.turnstile-widget', ['theme' => 'light'])</div>

        <button type="submit" class="btn-coral h-14 w-full text-[15px]">
            <span x-text="amount ? 'Donner ' + Number(amount).toLocaleString('fr-FR').replace(/ /g, ' ') + ' FCFA' : 'Faire mon don'">Faire mon don</span>
        </button>
        <p class="-mt-2 flex items-center justify-center gap-1.5 text-center text-xs text-slate">
            <svg class="size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>
            Vous serez redirigé vers la page de paiement sécurisée.
        </p>
    </form>
</div>
