@extends('front.layouts.app')
@section('title', 'Rejoignez-nous')
@section('meta_description', 'Devenez bénévole chez One Nation One Heart et aidez les orphelinats du Cameroun.')

@section('content')
    @include('front.partials.page-hero', ['title' => 'Rejoignez-nous', 'crumbs' => ['Rejoignez-nous' => null], 'site' => 'hero_joinus'])

    <section class="wrap grid gap-12 py-12 lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-16 lg:py-16">
        <div class="min-w-0">
            @include('front.partials.section-title', [
                'title' => "Formulaire d'inscription",
                'text' => "N'hésitez pas à nous rejoindre et à mettre votre pierre à l'édifice. À temps plein ou partiel, nous sommes déjà ravis d'avoir vos compétences dans nos équipes.",
            ])

            <form id="become-volunteer" action="{{ route('public.joinus.register') }}" method="POST" class="grid gap-8" data-reveal>
                @csrf

                @if ($errors->any())
                    <div class="rounded-box bg-coral/10 px-5 py-4 text-coral-deep" role="alert">
                        <p class="font-bold">Merci de corriger les points suivants :</p>
                        <ul class="mt-1.5 list-disc pl-5 text-sm">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="label">Nom complet</label>
                        <input type="text" class="field" name="name" id="name" value="{{ old('name') }}" autocomplete="name" required>
                    </div>
                    <div>
                        <label for="email" class="label">Email</label>
                        <input type="email" class="field" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required>
                    </div>
                    <div>
                        <label for="tel" class="label">N° de téléphone</label>
                        <input type="tel" class="field" name="tel" id="tel" value="{{ old('tel') }}" autocomplete="tel" inputmode="tel" placeholder="6 XX XX XX XX">
                    </div>
                    <div>
                        <label for="provenance" class="label">Comment as-tu connu ONOH ?</label>
                        <select name="provenance" id="provenance" class="field">
                            @foreach (['bouche-oreille' => 'Bouche à oreille', 'reseaux-sociaux' => 'Réseaux sociaux', 'autres' => 'Autres'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('provenance') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="city" class="label">Dans quelle ville résides-tu ?</label>
                        <input type="text" class="field" name="city" id="city" value="{{ old('city') }}" autocomplete="address-level2">
                    </div>
                </div>

                <fieldset>
                    <legend class="label">Tes préférences d'activités au sein de ONOH</legend>
                    <div class="mt-1 flex flex-wrap gap-2">
                        @foreach ([
                            'developpement-web' => 'Pôle Développement Web',
                            'prospection' => 'Pôle Prospection',
                            'financement-relation-donateur' => 'Pôle Financement & Relation donateurs',
                            'communication' => 'Pôle Communication',
                            'juridique' => 'Pôle Juridique',
                            'partenaire' => 'Pôle Recherche de Partenariats',
                        ] as $value => $label)
                            <label class="chip">
                                <input type="checkbox" name="preferences[]" value="{{ $value }}" @checked(in_array($value, old('preferences', [])))>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <div>
                    <label for="message" class="label">As-tu déjà été investi dans une association à but non lucratif ? Si oui, parle-nous-en en quelques mots.</label>
                    <textarea name="past_experience" id="message" rows="5" class="field">{{ old('past_experience') }}</textarea>
                </div>

                <div class="[&_.mb-4]:mb-0 empty:hidden">@include('components.turnstile-widget', ['theme' => 'light'])</div>

                <button type="submit" class="btn-coral justify-self-start">Devenir bénévole <x-onoh.icon name="arrow-right" class="arrow" /></button>
            </form>
        </div>

        <aside class="lg:sticky lg:top-24 lg:self-start" data-reveal style="--delay: 100ms">
            <div class="ndop rounded-box p-6 text-white">
                <h2 class="display text-3xl">Une question ?</h2>
                <p class="mt-2 text-sm text-[#dfe3ff]">Contactez-nous, nous vous répondrons rapidement.</p>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="tel:+237655029867" class="hover:underline">+237 6 55 02 98 67</a></li>
                    <li><a href="tel:+33614597593" class="hover:underline">+33 6 14 59 75 93</a></li>
                    <li><a href="mailto:contact@onoh.org" class="hover:underline">contact@onoh.org</a></li>
                </ul>
            </div>
        </aside>
    </section>
@endsection
