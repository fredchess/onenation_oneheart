@extends('front.layouts.app')

@section('title', 'Orphelinats')
@section('meta_description', 'Trouvez un orphelinat ou un centre social légal au Cameroun par nom, ville, quartier, âge des enfants et besoins.')

@section('content')
    @php
        $selectedCities = array_filter((array) request('villes', []));
        $selectedAges = (array) request('ages', []);
        $selectedNeeds = (array) request('besoins', []);
        $ages = ['1' => '0 – 6 ans', '2' => '7 – 13 ans', '3' => '14 – 21 ans'];
        $sorts = ['3' => 'Total dons reçus croissant', '4' => 'Total dons reçus décroissant', '1' => 'Nombre d\'enfants croissant', '2' => 'Nombre d\'enfants décroissant'];
        $filterCount = count($selectedCities) + count($selectedAges);
    @endphp

    @include('front.partials.page-hero', ['title' => 'Orphelinats', 'crumbs' => ['Orphelinats' => null], 'site' => 'hero_orphanages'])

    <form action="{{ route('public.orphanages') }}" method="GET" role="search" id="recherche"
          x-data="{ sheet: false, desktop: window.matchMedia('(min-width: 1024px)').matches }"
          @resize.window.debounce.200ms="desktop = window.matchMedia('(min-width: 1024px)').matches"
          @keydown.escape.window="sheet = false">
        <section class="border-b border-rule bg-paper">
            <div class="wrap">
                {{-- Barre de recherche principale --}}
                <div class="relative z-10 -mt-7 grid rounded-box bg-white shadow-[0_18px_40px_-26px_rgba(11,15,48,.6)] lg:grid-cols-[1.2fr_1fr_1fr_1fr_auto]">
                    <label class="block border-b border-rule px-4 py-3 lg:border-b-0 lg:border-r">
                        <span class="label mb-0.5">Nom de l'orphelinat</span>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Rechercher…"
                               class="w-full bg-transparent font-semibold placeholder:font-normal placeholder:text-slate/60 focus:outline-none">
                    </label>

                    {{-- Ville, quartier, âge : visibles sur desktop, dans le panneau « Filtres » sur mobile --}}
                    <div x-show="sheet && !desktop" x-cloak x-transition.opacity class="fixed inset-0 z-[55] bg-night/50 lg:hidden" @click="sheet = false" aria-hidden="true"></div>
                    <div id="filtres" :role="desktop ? null : 'dialog'" :aria-modal="(!desktop && sheet).toString()" aria-label="Filtres"
                         class="fixed inset-x-0 bottom-0 z-[60] max-h-[85dvh] overflow-y-auto rounded-t-xl bg-white px-5 pt-3 shadow-2xl transition-[transform,visibility] duration-300 lg:static lg:contents lg:max-h-none lg:translate-y-0 lg:overflow-visible lg:rounded-none lg:p-0 lg:shadow-none"
                         :class="sheet || desktop ? 'translate-y-0' : 'invisible translate-y-full'"
                         style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom, 0px))">
                        <div class="mx-auto mb-3 h-1.5 w-12 rounded-full bg-rule lg:hidden" aria-hidden="true"></div>
                        <div class="mb-4 flex items-center justify-between lg:hidden">
                            <h2 class="display text-3xl text-indigo">Filtres</h2>
                            <button type="button" class="grid size-11 place-items-center rounded-ui border border-rule text-indigo" @click="sheet = false" aria-label="Fermer les filtres"><x-onoh.icon name="close" class="size-5" /></button>
                        </div>

                        @include('front.partials.checkbox-dropdown', [
                            'name' => 'villes[]', 'label' => 'Ville', 'placeholder' => 'Toutes les villes', 'responsive' => true,
                            'options' => $villes->sortBy('name')->pluck('name', 'name')->all(), 'selected' => $selectedCities,
                            'class' => 'mb-6 lg:mb-0 lg:border-r lg:border-rule lg:px-4 lg:py-3',
                        ])

                        <label class="mb-6 block lg:mb-0 lg:border-r lg:border-rule lg:px-4 lg:py-3">
                            <span class="label mb-2 lg:mb-0.5">Quartier</span>
                            <input type="text" name="street" value="{{ request('street') }}" placeholder="Ex. Biyem-Assi"
                                   class="field lg:min-h-0 lg:border-0 lg:p-0 lg:font-semibold lg:placeholder:font-normal lg:focus:ring-0">
                        </label>

                        @include('front.partials.checkbox-dropdown', [
                            'name' => 'ages[]', 'label' => 'Âge des enfants', 'placeholder' => 'Toutes tranches', 'responsive' => true,
                            'options' => $ages, 'selected' => $selectedAges,
                            'class' => 'mb-6 lg:mb-0 lg:border-r lg:border-rule lg:px-4 lg:py-3',
                        ])

                        <div class="mb-6 lg:hidden">
                            <label for="sort-m" class="label mb-2">Trier par</label>
                            <select id="sort-m" class="field" @change="$refs.sort.value = $el.value">
                                @foreach ($sorts as $value => $label)
                                    <option value="{{ $value }}" @selected((string) request('sort', '3') === (string) $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sticky bottom-0 -mx-5 grid grid-cols-[1fr_1.4fr] gap-2 border-t border-rule bg-white px-5 pt-3 lg:hidden">
                            <a href="{{ route('public.orphanages') }}" class="btn shadow-[inset_0_0_0_2px_currentColor] text-indigo">Effacer</a>
                            <button type="submit" class="btn-indigo">Voir les résultats</button>
                        </div>
                    </div>

                    <button type="submit" class="btn-coral rounded-none rounded-b-box px-7 max-lg:min-h-12 lg:rounded-bl-none lg:rounded-r-box">Rechercher <x-onoh.icon name="arrow-right" class="arrow" /></button>
                </div>

                {{-- Besoins en un clic + accès aux filtres sur mobile --}}
                <div class="flex flex-wrap items-center gap-2 py-5">
                    <button type="button" class="chip border-indigo bg-indigo text-white lg:hidden" @click="sheet = true" aria-controls="filtres" :aria-expanded="sheet.toString()">
                        <x-onoh.icon name="filter" class="mr-1.5 size-4" /> Filtres @if ($filterCount)({{ $filterCount }})@endif
                    </button>
                    <span class="mr-1 hidden text-sm font-semibold text-slate lg:inline">Besoins :</span>
                    @foreach (\App\Models\Orphanage::NEEDS as $key => $label)
                        <label class="chip">
                            <input type="checkbox" name="besoins[]" value="{{ $key }}" @checked(in_array($key, $selectedNeeds)) @change="$el.form.requestSubmit()">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="wrap pb-16">
            <div class="flex flex-wrap items-baseline justify-between gap-3 py-8">
                <h2 class="display text-[2.1rem] text-indigo"><span class="tabular-nums">{{ $orphelinats->total() }}</span> {{ Str::plural('orphelinat', $orphelinats->total()) }}</h2>
                <label class="hidden items-center gap-2 text-sm text-slate lg:flex">
                    Trier par :
                    <select name="sort" x-ref="sort" class="bg-transparent font-semibold text-ink focus:outline-none" @change="$el.form.requestSubmit()">
                        @foreach ($sorts as $value => $label)
                            <option value="{{ $value }}" @selected((string) request('sort', '3') === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            @if ($orphelinats->count() > 0)
                <div class="grid gap-x-7 gap-y-11 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($orphelinats as $orphelinat)
                        @include('front.partials.orphanage-card', ['orphanage' => $orphelinat, 'delay' => ($loop->index % 3) * 70])
                    @endforeach
                </div>
                {{ $orphelinats->links('front.partials.pagination') }}
            @else
                <div class="rounded-box border border-rule bg-paper px-6 py-14 text-center">
                    <img src="{{ asset('lovecare/images/empty.svg') }}" alt="" class="mx-auto w-44" loading="lazy">
                    <p class="display mt-6 text-3xl text-indigo">Pas d'orphelinats trouvés</p>
                    <p class="mt-2 text-slate">Essayez d'élargir votre recherche ou de retirer un filtre.</p>
                    <a href="{{ route('public.orphanages') }}" class="link mt-5">Voir tous les orphelinats <x-onoh.icon name="arrow-right" class="arrow" /></a>
                </div>
            @endif
        </section>
    </form>
@endsection
