@extends('front.layouts.app')

@php
    $identity = $orphelinat->data_identity ?? [];
    $stats = $orphelinat->data_stats ?? [];
    $education = $orphelinat->data_education ?? [];
    $needsData = $orphelinat->data_needs ?? [];
    $address = $orphelinat->data_address ?? [];
    $promoter = $orphelinat->data_identity_promoter ?? [];
    $cover = $orphelinat->getFirstMediaUrl('profile_images');
    $phone = $promoter['promoter_phone'] ?? ($orphelinat->responsable->datas['tel'] ?? null);
    $website = $identity['website'] ?? null;
    $map = $address['google_address'] ?? '';
    $needTitles = [
        'food_needs' => 'Besoins alimentaires',
        'health_needs' => 'Besoins sanitaires & hygiéniques',
        'school_needs' => 'Besoins scolaires',
        'clothes_needs' => 'Besoins vestimentaires',
        'ludic_needs' => 'Besoins ludiques',
        'other_needs' => 'Autres besoins',
    ];
    $filledNeeds = $orphelinat->filledNeeds();
    $ageBands = [
        '0 – 6 ans' => (int) ($stats['children_number_0_6'] ?? 0),
        '7 – 13 ans' => (int) ($stats['children_number_7_13'] ?? 0),
        '14 – 21 ans' => (int) ($stats['children_number_14_21'] ?? 0),
    ];
    $ageMax = max(1, max($ageBands));
@endphp

@section('title', $orphelinat->name)
@section('meta_title', $orphelinat->name)
@section('meta_description', Str::limit(strip_tags($identity['history'] ?? ($identity['mini_description'] ?? '')), 160))
@section('meta_url', route('public.orphanages.details', ['orphanage_slug' => $orphelinat->slug]))
@section('meta_image', $cover)
@section('donate_anchor', '#donate')

@section('content')
    @include('front.partials.page-hero', [
        'title' => $identity['name'] ?? $orphelinat->name,
        'crumbs' => ['Orphelinats' => route('public.orphanages'), $orphelinat->name => null],
        'image' => $cover ?: \App\Support\SiteImages::fallbackUrl($orphelinat->id, 1080),
        'fallback' => \App\Support\SiteImages::fallbackUrl($orphelinat->id, 1080),
    ])

    {{-- Infos utiles --}}
    <section class="ndop text-white">
        <dl class="wrap grid grid-cols-2 lg:grid-cols-4">
            <div class="border-white/12 py-5 pr-4 lg:border-r lg:py-6">
                <dt class="text-xs font-bold uppercase tracking-[.12em] text-sky">Enfants</dt>
                <dd class="mt-1 font-display text-4xl font-extrabold tabular-nums">{{ $stats['children_number'] ?? 0 }}</dd>
            </div>
            @php($location = collect([$orphelinat->city->name ?? null, trim(strip_tags($address['localisation'] ?? ''))])->filter()->join(' · '))
            <div class="min-w-0 border-white/12 py-5 pl-4 lg:border-r lg:px-6 lg:py-6">
                <dt class="text-xs font-bold uppercase tracking-[.12em] text-sky">Localisation</dt>
                {{-- Certaines adresses sont de longues descriptions : 3 lignes maximum, texte complet au survol --}}
                <dd class="mt-1.5 line-clamp-3 break-words font-semibold" title="{{ $location }}">{{ $location }}</dd>
            </div>
            <div class="min-w-0 border-t border-white/12 py-5 pr-4 lg:border-r lg:border-t-0 lg:px-6 lg:py-6">
                <dt class="text-xs font-bold uppercase tracking-[.12em] text-sky">Téléphone</dt>
                <dd class="mt-1.5 font-semibold">
                    @if ($phone)<a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="hover:underline">{{ $phone }}</a>@else<span class="text-sky">Non communiqué</span>@endif
                    @if (!empty($promoter['second_phone']))<br><a href="tel:{{ preg_replace('/\s+/', '', $promoter['second_phone']) }}" class="hover:underline">{{ $promoter['second_phone'] }}</a>@endif
                </dd>
            </div>
            <div class="border-t border-white/12 py-5 pl-4 lg:border-t-0 lg:px-6 lg:py-6">
                <dt class="text-xs font-bold uppercase tracking-[.12em] text-sky">Site web</dt>
                <dd class="mt-1.5 font-semibold">
                    @if ($website)
                        <a href="{{ str_starts_with($website, 'http') ? $website : 'http://' . $website }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 hover:underline">Visiter <x-onoh.icon name="arrow-up-right" class="size-4" /></a>
                    @else
                        <span class="text-sky">Aucun</span>
                    @endif
                </dd>
            </div>
        </dl>
    </section>

    <div class="wrap grid gap-12 py-12 lg:grid-cols-[minmax(0,1fr)_25rem] lg:gap-14 lg:py-16">
        <div class="min-w-0 space-y-14">
            <a href="#donate" class="btn-coral w-full lg:hidden">Faire un don à cet orphelinat</a>

            @if (!empty($identity['description']))
                <section class="rich" data-reveal>{!! $identity['description'] !!}</section>
            @endif

            @if (!empty($identity['history']))
                <section data-reveal>
                    @include('front.partials.section-title', ['title' => "Histoire de l'orphelinat"])
                    <div class="rich">{!! $identity['history'] !!}</div>
                </section>
            @endif

            @if ($filledNeeds)
                <section>
                    @include('front.partials.section-title', ['title' => "Besoins de l'orphelinat", 'text' => 'Ce dont la structure a besoin aujourd\'hui. Votre don financier sert à couvrir ces besoins.'])
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ($filledNeeds as $key => $label)
                            <article class="rounded-box border border-rule p-5" data-reveal style="--delay: {{ $loop->index * 60 }}ms">
                                <h3 class="display text-2xl text-indigo">{{ $needTitles[$key] }}</h3>
                                <div class="rich mt-2 text-[15px]">{!! $needsData[$key] !!}</div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (!empty($identity['withonoh']))
                <section class="ndop rounded-box p-7 text-white sm:p-9" data-reveal>
                    <h2 class="display text-[2.4rem]">L'orphelinat et ONOH</h2>
                    <div class="rich mt-4 text-white/90 [&_a]:text-white">{!! $identity['withonoh'] !!}</div>
                </section>
            @endif

            {{-- Informations complémentaires (anciens accordéons) --}}
            <section>
                @include('front.partials.section-title', ['title' => 'Informations complémentaires'])
                <div class="divide-y divide-rule border-y border-rule" x-data="{ open: 'stats' }">
                    @php($promoterName = $orphelinat->responsable->name ?? ($promoter['promoter_name'] ?? null))
                    @foreach ([
                        'promoter' => 'Promoteur',
                        'stats' => 'Statistiques',
                        'education' => 'Éducation',
                    ] as $panel => $label)
                        <div>
                            <button type="button" class="flex w-full items-center justify-between py-4 text-left font-display text-2xl font-extrabold uppercase text-indigo"
                                    @click="open = open === '{{ $panel }}' ? null : '{{ $panel }}'" :aria-expanded="(open === '{{ $panel }}').toString()" aria-controls="panel-{{ $panel }}">
                                {{ $label }}
                                <x-onoh.icon name="chevron-down" class="size-5 transition-transform duration-300" ::class="open === '{{ $panel }}' && 'rotate-180'" />
                            </button>
                            <div id="panel-{{ $panel }}" x-show="open === '{{ $panel }}'" x-collapse x-cloak>
                                <div class="pb-6">
                                    @if ($panel === 'promoter')
                                        <p><span class="text-slate">Nom du promoteur :</span> <strong class="text-indigo">{{ $promoterName ?: 'Non communiqué' }}</strong></p>
                                    @elseif ($panel === 'stats')
                                        <div class="grid gap-6 sm:grid-cols-2" x-data="{ on: false }" x-intersect.once="on = true">
                                            <ul class="space-y-3.5">
                                                @foreach ($ageBands as $band => $value)
                                                    <li>
                                                        <p class="flex justify-between text-sm"><span class="text-slate">{{ $band }}</span><b class="tabular-nums text-indigo">{{ $value }}</b></p>
                                                        <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-paper"><i class="block h-full rounded-full bg-indigo transition-[width] duration-1000 ease-out-soft" :style="on ? 'width: {{ round($value / $ageMax * 100) }}%' : 'width: 0'"></i></div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <dl class="grid grid-cols-2 gap-4 self-start">
                                                <div class="rounded-box bg-paper p-4"><dt class="text-sm text-slate">Garçons</dt><dd class="font-display text-3xl font-extrabold tabular-nums text-indigo">{{ $stats['boys_number'] ?? 0 }}</dd></div>
                                                <div class="rounded-box bg-paper p-4"><dt class="text-sm text-slate">Filles</dt><dd class="font-display text-3xl font-extrabold tabular-nums text-indigo">{{ $stats['girls_number'] ?? 0 }}</dd></div>
                                            </dl>
                                        </div>
                                    @else
                                        <dl class="grid gap-x-8 text-[15px] sm:grid-cols-2">
                                            @foreach ([
                                                'school_children_number' => 'Enfants scolarisés',
                                                'schoolexam_children_number' => 'En classe d\'examen',
                                                'maternelle_children_number' => 'En maternelle',
                                                'primary_children_number' => 'Au primaire',
                                                'college_children_number' => 'Au collège',
                                                'university_children_number' => 'À l\'université',
                                                'professional_children_number' => 'En recherche de formation professionnelle',
                                            ] as $key => $label2)
                                                <div class="flex justify-between gap-4 border-b border-rule py-2.5"><dt class="text-slate">{{ $label2 }}</dt><dd class="font-bold tabular-nums text-indigo">{{ $education[$key] ?? 0 }}</dd></div>
                                            @endforeach
                                        </dl>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            @if (str_starts_with($map, '<iframe'))
                <section data-reveal>
                    @include('front.partials.section-title', ['title' => 'Comment trouver ' . ($identity['name'] ?? $orphelinat->name) . ' ?'])
                    <div class="overflow-hidden rounded-box border border-rule [&_iframe]:block [&_iframe]:aspect-[4/3] [&_iframe]:h-auto [&_iframe]:w-full sm:[&_iframe]:aspect-video">{!! $map !!}</div>
                </section>
            @endif
        </div>

        <aside id="donate" class="scroll-mt-24 lg:sticky lg:top-24 lg:self-start" aria-label="Faire un don">
            @include('front.partials.donation-form')
        </aside>
    </div>
@endsection
