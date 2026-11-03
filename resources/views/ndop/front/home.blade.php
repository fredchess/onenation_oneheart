@extends('front.layouts.app')

@section('donate_anchor', '#donate')

@php($slides = \App\Support\SiteImages::slides('home_hero'))

@push('preload')
    @if (isset($slides[0]['url']))
        <link rel="preload" as="image" href="{{ $slides[0]['url'] }}">
    @else
        <link rel="preload" as="image" href="{{ asset("redesign/img/{$slides[0]['photo']}-1080.webp") }}"
              imagesrcset="{{ asset("redesign/img/{$slides[0]['photo']}-640.webp") }} 640w, {{ asset("redesign/img/{$slides[0]['photo']}-1080.webp") }} 1080w" imagesizes="100vw">
    @endif
@endpush

@section('content')
    @php($cities = \App\Models\City::orderBy('name')->get(['name']))

    {{-- Hero : images qui défilent en fondu, pilules de progression --}}
    <section class="relative flex min-h-[600px] flex-col text-white lg:min-h-[580px]"
             x-data="heroSlider({{ count($slides) }}, 6000)" @pointerenter="paused = true" @pointerleave="paused = false">
        <div class="absolute inset-0 overflow-hidden" aria-roledescription="carrousel" aria-label="Photos de ONOH">
            @foreach ($slides as $slide)
                <div class="hero-slide absolute inset-0 transition-opacity duration-[1200ms] ease-in-out {{ $loop->first ? 'opacity-100 is-active' : 'opacity-0' }}"
                     :class="current === {{ $loop->index }} ? 'opacity-100 is-active' : 'opacity-0'"
                     @if (!$loop->first) aria-hidden="true" @endif :aria-hidden="(current !== {{ $loop->index }}).toString()">
                    @isset($slide['url'])
                        <img src="{{ $slide['url'] }}" alt="" class="size-full object-cover" decoding="async" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                    @else
                        <x-onoh.photo :name="$slide['photo']" alt="" class="size-full object-cover" :eager="$loop->first" />
                    @endisset
                </div>
            @endforeach
        </div>
        <div class="absolute inset-0 bg-[linear-gradient(0deg,rgba(11,15,48,.97)_8%,rgba(11,15,48,.7)_55%,rgba(11,15,48,.25)_100%)] lg:bg-[linear-gradient(90deg,rgba(11,15,48,.94)_0%,rgba(11,15,48,.78)_42%,rgba(20,26,74,.15)_100%)]" aria-hidden="true"></div>
        <div class="wrap relative flex flex-1 flex-col">
            <div class="mt-auto max-w-[620px] pb-8 pt-16 lg:pb-14">
                <p class="fade-up mb-3 font-semibold text-sky">One Nation One Heart</p>
                <h1 class="display fade-up text-[4rem] sm:text-[5.5rem] lg:text-[6.5rem]" style="--delay: 80ms">Smile together</h1>
                <p class="fade-up mb-7 mt-4 max-w-[46ch] text-base text-[#dfe3ff] sm:text-lg" style="--delay: 160ms">Nous recensons les orphelinats et centres sociaux légaux du Cameroun pour que chacun puisse les découvrir et les aider directement.</p>
                <div class="fade-up grid grid-cols-2 gap-2 sm:flex" style="--delay: 240ms">
                    <a href="#donate" class="btn-coral px-3 sm:px-6"><span class="sm:hidden">Faire un don</span><span class="hidden sm:inline">Faire un don maintenant</span> <x-onoh.icon name="arrow-right" class="arrow hidden sm:block" /></a>
                    <a href="{{ route('public.orphanages') }}" class="btn-outline px-3 sm:hidden">Orphelinats</a>
                </div>
            </div>

            @if (count($slides) > 1)
                <div class="fade-up flex justify-end pb-3" style="--delay: 320ms" :class="paused && 'is-paused'">
                    <div class="flex items-center gap-1.5" role="group" aria-label="Choisir la photo">
                        @foreach ($slides as $slide)
                            <button type="button" class="group/pill py-3" @click="go({{ $loop->index }})"
                                    :aria-current="(current === {{ $loop->index }}).toString()" aria-label="Photo {{ $loop->iteration }} sur {{ count($slides) }}">
                                <span class="relative block h-1.5 overflow-hidden rounded-full bg-white/30 transition-[width,background-color] duration-500 ease-out-soft group-hover/pill:bg-white/55 {{ $loop->first ? 'w-12' : 'w-4' }}"
                                      :class="current === {{ $loop->index }} ? 'w-12' : 'w-4'">
                                    <span class="absolute inset-y-0 left-0 w-0 rounded-full bg-white {{ $loop->first ? 'pill-run' : '' }}" style="--pill-duration: 6000ms"
                                          :class="current === {{ $loop->index }} && 'pill-run'" @animationend="next()"></span>
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Recherche d'orphelinats --}}
            <form action="{{ route('public.orphanages') }}" method="GET" role="search" class="fade-up -mx-[max(1.125rem,env(safe-area-inset-left,0px))] grid bg-white text-ink sm:mx-0 sm:rounded-t-box md:grid-cols-[1fr_1fr_auto]" style="--delay: 320ms">
                @include('front.partials.checkbox-dropdown', [
                    'name' => 'villes[]', 'label' => 'Ville', 'placeholder' => 'Toutes les villes',
                    'options' => $cities->pluck('name', 'name')->all(), 'selected' => [],
                    'class' => 'border-b border-rule px-4 py-3 md:border-b-0 md:border-r md:px-5',
                ])
                <label class="block border-b border-rule px-4 py-3 md:border-b-0 md:border-r md:px-5">
                    <span class="label mb-0.5">Nom ou quartier</span>
                    <input type="search" name="search" placeholder="Ex. Biyem-Assi" class="w-full bg-transparent font-semibold placeholder:font-normal placeholder:text-slate/60 focus:outline-none">
                </label>
                <button type="submit" class="btn-coral min-h-13 rounded-none px-8 md:rounded-tr-box">Rechercher <x-onoh.icon name="arrow-right" class="arrow" /></button>
            </form>
        </div>
    </section>
    <div class="band"></div>

    {{-- Présentation + don --}}
    <section id="donate" class="wrap grid scroll-mt-20 gap-12 py-14 lg:grid-cols-[1.1fr_.9fr] lg:gap-14 lg:py-[72px]">
        <div class="min-w-0" data-reveal>
            <h2 class="display border-b border-rule pb-5 text-[2.4rem] text-indigo md:text-[3.25rem]">Pour une action sociale, humanitaire et solidaire</h2>
            <p class="mt-5 text-slate">Sur cette plateforme, nous recensons les orphelinats et centres sociaux légaux sur toute l'étendue du territoire camerounais. Ils sont répartis selon plusieurs critères pour vous permettre de les découvrir facilement :</p>
            <ul class="mt-3 list-disc space-y-1.5 pl-5 text-slate marker:text-coral">
                <li>La localisation : région, département, ville et quartier</li>
                <li>La capacité d'accueil : nombre d'enfants pris en charge et tranches d'âge</li>
                <li>Les besoins : financiers, sanitaires, alimentaires, vestimentaires…</li>
                <li>L'histoire et les contacts des responsables</li>
            </ul>
            <p class="mt-4 text-slate">L'idée : casser les préjugés sur l'action sociale au Cameroun et rendre chacun acteur du bonheur d'autrui.</p>
            <p class="mt-7 flex flex-wrap gap-x-7 gap-y-3">
                <a href="{{ route('public.orphanages') }}" class="link">Voir les orphelinats <x-onoh.icon name="arrow-right" class="arrow" /></a>
                <a href="{{ route('public.contact') }}" class="link text-indigo">Signaler un orphelinat non recensé <x-onoh.icon name="arrow-right" class="arrow" /></a>
            </p>
        </div>
        <div class="min-w-0" data-reveal style="--delay: 100ms">
            @include('front.partials.donation-form')
        </div>
    </section>

    {{-- Chiffres clés --}}
    <section class="ndop py-8 text-white md:py-10" aria-label="Chiffres clés">
        {{-- Mobile : premier chiffre en pleine largeur puis grille 2 × 2 ; desktop : 5 colonnes --}}
        <dl class="wrap grid grid-cols-2 gap-2.5 md:grid-cols-5 md:gap-4">
            @foreach ([
                [$total_orphanages, 'Orphelinats enregistrés'],
                [$total_cities, 'Villes couvertes'],
                [$total_enfants, 'Enfants recensés'],
                [$partners->count(), 'Partenaires'],
                [33, 'Bénévoles'],
            ] as [$value, $label])
                <div class="flex flex-col rounded-box bg-white/[.07] px-4 py-4 backdrop-blur-[2px] md:items-center md:px-3 md:py-6 md:text-center {{ $loop->first ? 'col-span-2 flex-row items-baseline gap-3 md:col-span-1 md:flex-col md:gap-0' : '' }}"
                     x-data="countUp({{ (int) $value }})" x-intersect.once="run()" data-reveal style="--delay: {{ $loop->index * 60 }}ms">
                    <dt class="order-last text-sm text-sky md:mt-1.5 {{ $loop->first ? '' : 'mt-1' }}">{{ $label }}</dt>
                    <dd class="font-display font-extrabold leading-none tabular-nums {{ $loop->first ? 'text-[3.25rem]' : 'text-[2.4rem]' }} md:text-[3.5rem]" x-text="formatted">{{ number_format($value, 0, ',', ' ') }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    {{-- Actions menées --}}
    <section class="wrap py-14 lg:py-[72px]">
        @include('front.partials.section-title', [
            'title' => 'Actions menées',
            'text' => 'Remises de dons, journées récréatives, visites de santé : ce que vos dons ont permis sur le terrain.',
            'aside' => '<a href="' . route('public.blog') . '" class="link">Tous les articles <svg class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 12h16M14 6l6 6-6 6"/></svg></a>',
        ])
        @if ($blogs->count() > 0)
            <div class="-mx-[1.125rem] flex snap-x snap-mandatory scroll-px-[1.125rem] gap-5 overflow-x-auto px-[1.125rem] pb-2 [scrollbar-width:none] md:mx-0 md:grid md:grid-cols-3 md:gap-7 md:overflow-visible md:px-0">
                @foreach ($blogs->take(3) as $blog)
                    <div class="w-[82%] shrink-0 snap-start md:w-auto">@include('front.partials.blog-card', ['blog' => $blog, 'delay' => $loop->index * 80])</div>
                @endforeach
            </div>
        @else
            <p class="text-slate">Pas d'articles enregistrés pour l'instant.</p>
        @endif
    </section>

    {{-- Bannière --}}
    <section class="relative overflow-hidden py-20 text-center text-white lg:py-24">
        <x-onoh.site-photo for="home_quote" class="absolute inset-0 size-full object-cover" />
        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(11,15,48,.82),rgba(20,26,74,.9))]" aria-hidden="true"></div>
        <div class="wrap relative" data-reveal>
            <p class="text-sm font-semibold text-sky">Faites un don ou devenez bénévole</p>
            <h2 class="display mx-auto mt-3 max-w-4xl text-[2.4rem] md:text-[3.5rem]">Le plus petit acte de bonté vaut plus que la plus grande intention</h2>
            <p class="mt-7 flex flex-wrap justify-center gap-2.5">
                <a href="#donate" class="btn-coral">Faire un don</a>
                <a href="{{ route('public.joinus') }}" class="btn-outline">Devenir bénévole</a>
            </p>
        </div>
    </section>

    {{-- Partenaires : carrousel de logos --}}
    @if ($partners->count() > 0)
        <section class="wrap py-14 lg:py-[72px]" x-data="carousel(3500)" @pointerenter="paused = true" @pointerleave="paused = false" @focusin="paused = true">
            @include('front.partials.section-title', [
                'title' => 'Nos partenaires',
                'text' => 'Entreprises et associations qui soutiennent nos actions sur le terrain.<br><a href="mailto:contact@onoh.org?subject=Devenir partenaire" class="link mt-2.5">Devenir partenaire <svg class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 12h16M14 6l6 6-6 6"/></svg></a>',
                'aside' => '<div class="flex gap-1.5" x-show="scrollable" x-cloak><button type="button" @click="go(-1)" class="grid size-11 place-items-center rounded-ui border border-rule text-indigo hover:border-indigo" aria-label="Partenaires précédents"><svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 6-6 6 6 6"/></svg></button><button type="button" @click="go(1)" class="grid size-11 place-items-center rounded-ui border border-rule text-indigo hover:border-indigo" aria-label="Partenaires suivants"><svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg></button></div>',
            ])
            <div x-ref="track" :class="scrollable ? '' : 'justify-center'" class="grid snap-x snap-mandatory auto-cols-[calc((100%-1rem)/2)] grid-flow-col gap-4 overflow-x-auto pb-1 [scrollbar-width:none] md:auto-cols-[calc((100%-2rem)/3)] lg:auto-cols-[calc((100%-3rem)/4)] [&::-webkit-scrollbar]:hidden"
                 tabindex="0" aria-label="Logos des partenaires" data-reveal>
                @foreach ($partners as $partner)
                    <a href="{{ $partner->url }}" target="_blank" rel="noopener" class="group flex snap-start flex-col items-center gap-1 rounded-box border border-rule bg-white px-4 pb-4 pt-5 text-center transition-colors hover:border-indigo">
                        <span class="mb-2.5 flex h-20 items-center justify-center">
                            <img src="{{ $partner->getFirstMediaUrl('images') }}" alt="{{ $partner->name }}" loading="lazy" onerror="this.remove()" class="max-h-20 max-w-[160px] object-contain opacity-75 grayscale transition duration-300 group-hover:opacity-100 group-hover:grayscale-0">
                        </span>
                        <b class="text-[15px] text-indigo">{{ $partner->name }}</b>
                        <small class="text-[13px] text-slate">{{ $partner->domain }}</small>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Témoignages --}}
    <section class="ndop text-white" x-data="carousel(8000)" @pointerenter="paused = true" @pointerleave="paused = false">
        <div class="wrap grid gap-8 py-14 lg:grid-cols-[1fr_2fr] lg:gap-14 lg:py-[72px]">
            <div data-reveal>
                <h2 class="display text-[2.4rem] md:text-[3.25rem]">Ce qu'ils disent de nous</h2>
                <div class="mt-6 flex gap-1.5">
                    <button type="button" @click="go(-1)" class="grid size-11 place-items-center rounded-ui shadow-[inset_0_0_0_1px_rgba(255,255,255,.35)] hover:bg-white/10" aria-label="Témoignage précédent"><x-onoh.icon name="chevron-left" class="size-[18px]" /></button>
                    <button type="button" @click="go(1)" class="grid size-11 place-items-center rounded-ui shadow-[inset_0_0_0_1px_rgba(255,255,255,.35)] hover:bg-white/10" aria-label="Témoignage suivant"><x-onoh.icon name="chevron-right" class="size-[18px]" /></button>
                </div>
            </div>
            <div x-ref="track" class="grid min-w-0 snap-x snap-mandatory auto-cols-[100%] grid-flow-col gap-8 overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" data-reveal style="--delay: 100ms">
                @foreach ($testimonies as $testimony)
                    <figure class="snap-start">
                        <blockquote class="text-xl leading-normal sm:text-2xl">« {{ trim($testimony['message'], '«» ') }} »</blockquote>
                        <figcaption class="mt-5 text-[15px] text-sky"><b class="block text-white">{{ $testimony['name'] }}</b>{{ $testimony['title'] }}</figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Appel à bénévoles --}}
    <section class="grid md:grid-cols-2">
        <div class="flex flex-col justify-center bg-coral px-[max(1.125rem,env(safe-area-inset-left,0px))] py-14 text-white md:px-12 lg:px-16" data-reveal>
            <p class="text-sm font-semibold text-white/85">Apportez un soutien humain à l'aventure ONOH</p>
            <h2 class="display mt-3 text-[2.4rem] md:text-[3.25rem]">Appel à bénévoles</h2>
            <p class="mb-6 mt-4 max-w-[44ch]">Couvrir l'étendue du territoire afin d'être proches de la quasi-totalité des orphelinats et centres sociaux, faciliter les descentes et faire connaître l'association.</p>
            <a href="{{ route('public.joinus') }}" class="btn-white self-start">Devenir bénévole <x-onoh.icon name="arrow-right" class="arrow" /></a>
        </div>
        <div class="relative min-h-72">
            <x-onoh.site-photo for="home_volunteers" alt="Bénévoles de ONOH" sizes="(min-width: 768px) 50vw, 100vw" class="absolute inset-0 size-full object-cover object-[center_30%]" />
        </div>
    </section>
@endsection
