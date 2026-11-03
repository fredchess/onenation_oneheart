@extends('front.layouts.app')

@section('title', 'À propos de One Nation One Heart')
@section('meta_description', 'One Nation One Heart promeut l\'unité, la solidarité et l\'action sociale au bénéfice des orphelins et nécessiteux du Cameroun.')

@section('content')
    @include('front.partials.page-hero', ['title' => 'À propos de ONOH', 'crumbs' => ['À propos' => null], 'site' => 'hero_about'])

    <section class="wrap grid items-center gap-10 py-12 lg:grid-cols-2 lg:gap-16 lg:py-16">
        <div class="photo aspect-[4/5] max-h-[560px] lg:aspect-auto lg:h-full" data-reveal>
            <x-onoh.site-photo for="about_photo" alt="Bénévoles de ONOH" sizes="(min-width: 1024px) 45vw, 100vw" />
        </div>
        <div data-reveal style="--delay: 100ms">
            <h2 class="display border-b border-rule pb-5 text-[2.4rem] text-indigo md:text-[3.25rem]">Qui sommes-nous ?</h2>
            <div class="mt-5 space-y-4 text-slate">
                <p>One Nation One Heart est une association qui souhaite promouvoir sur le plan national l'unité, la solidarité et l'action sociale au bénéfice des orphelins et nécessiteux sous toutes leurs formes, dans la limite de nos capacités et en ligne avec notre objet principal.</p>
                <p>Promouvoir le bien-être, l'éducation, le développement économique et social des enfants dans les orphelinats et centres sociaux camerounais.</p>
                <p>Mettre en place des œuvres d'entraide et d'assistance fondées sur les principes d'équité et d'égalité à l'endroit des orphelins et nécessiteux.</p>
            </div>
            <a href="{{ route('public.joinus') }}" class="btn-indigo mt-7">Rejoindre l'aventure <x-onoh.icon name="arrow-right" class="arrow" /></a>
        </div>
    </section>

    @if ($team->count() > 0)
        @php($initial = 8)
        <section class="border-t border-rule bg-paper py-12 lg:py-16"
                 @if ($teamDisplay === 'carousel') x-data="carousel(4000)" @pointerenter="paused = true" @pointerleave="paused = false" @focusin="paused = true" @endif>
            <div class="wrap">
                @include('front.partials.section-title', [
                    'title' => 'Notre équipe',
                    'text' => 'Les étoiles derrière ONOH.',
                    'stacked' => true,
                    'aside' => $teamDisplay === 'carousel'
                        ? '<div class="flex gap-1.5" x-show="scrollable" x-cloak><button type="button" @click="go(-1)" class="grid size-11 place-items-center rounded-ui border border-rule bg-white text-indigo hover:border-indigo" aria-label="Membres précédents"><svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 6-6 6 6 6"/></svg></button><button type="button" @click="go(1)" class="grid size-11 place-items-center rounded-ui border border-rule bg-white text-indigo hover:border-indigo" aria-label="Membres suivants"><svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg></button></div>'
                        : null,
                ])

                @if ($teamDisplay === 'carousel')
                    <div x-ref="track" class="grid snap-x snap-mandatory auto-cols-[calc((100%-1rem)/2)] grid-flow-col gap-4 overflow-x-auto pb-1 [scrollbar-width:none] md:auto-cols-[calc((100%-2.5rem)/3)] md:gap-5 lg:auto-cols-[calc((100%-3.75rem)/4)] [&::-webkit-scrollbar]:hidden"
                         tabindex="0" aria-label="Membres de l'équipe" data-reveal>
                        @foreach ($team as $member)
                            <div class="snap-start">@include('front.partials.team-card', ['member' => $member])</div>
                        @endforeach
                    </div>
                @else
                    <div x-data="{ all: false }">
                        <ul class="grid grid-cols-2 gap-4 md:grid-cols-3 md:gap-5 lg:grid-cols-4">
                            @foreach ($team as $member)
                                <li @if ($loop->index >= $initial) x-show="all" x-cloak x-transition.opacity.duration.300ms @else data-reveal style="--delay: {{ ($loop->index % 4) * 60 }}ms" @endif>
                                    @include('front.partials.team-card', ['member' => $member])
                                </li>
                            @endforeach
                        </ul>
                        @if ($team->count() > $initial)
                            <p class="mt-8 text-center" x-show="!all">
                                <button type="button" class="btn-indigo" @click="all = true">Voir toute l'équipe ({{ $team->count() }}) <x-onoh.icon name="chevron-down" class="size-4" /></button>
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    @endif
@endsection
