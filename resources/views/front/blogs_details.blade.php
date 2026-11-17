@extends('front.layouts.app')

@php
    $cover = $blog->getFirstMediaUrl('images');
    $gallery = $blog->getMedia('images');
@endphp

@section('title', $blog->name)
@section('meta_title', $blog->name)
@section('meta_description', $blog->datas['description'] ?? '')
@section('meta_url', route('public.blog.details', ['blog_slug' => $blog->slug]))
@section('meta_image', $cover)

@section('content')
    @include('front.partials.page-hero', [
        'title' => $blog->name,
        'crumbs' => ['Blogs' => route('public.blog'), $blog->name => null],
        'image' => $cover ?: \App\Support\SiteImages::fallbackUrl($blog->id, 1080),
        'fallback' => \App\Support\SiteImages::fallbackUrl($blog->id, 1080),
    ])

    <article class="wrap max-w-[860px] py-12 lg:py-16">
        <p class="text-sm font-semibold uppercase tracking-[.1em] text-slate">
            <time datetime="{{ $blog->created_at->toDateString() }}">{{ $blog->created_at->translatedFormat('d F Y') }}</time>
            @if (!empty($blog->datas['author']))<span class="mx-2 text-rule">|</span>{{ $blog->datas['author'] }}@endif
        </p>
        @if (!empty($blog->datas['description']))
            <p class="mt-4 text-xl leading-relaxed text-indigo">{{ $blog->datas['description'] }}</p>
        @endif

        <div class="rich mt-8">{!! $blog->datas['public_content'] ?? '' !!}</div>

        @if ($gallery->count() > 1)
            <section class="mt-14" x-data="{ open: null, items: {{ \Illuminate\Support\Js::from($gallery->map->getFullUrl()->values()) }} }"
                     @keydown.escape.window="open = null" @keydown.arrow-right.window="open !== null && (open = (open + 1) % items.length)"
                     @keydown.arrow-left.window="open !== null && (open = (open - 1 + items.length) % items.length)">
                @include('front.partials.section-title', ['title' => 'Galerie'])
                <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($gallery as $media)
                        <li>
                            <button type="button" class="photo block aspect-square w-full" @click="open = {{ $loop->index }}" aria-label="Agrandir la photo {{ $loop->iteration }}">
                                <img src="{{ $media->getFullUrl() }}" alt="" loading="lazy">
                            </button>
                        </li>
                    @endforeach
                </ul>
                <div x-show="open !== null" x-cloak x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center bg-night/95 p-4"
                     role="dialog" aria-modal="true" aria-label="Photo agrandie" @click.self="open = null" x-trap.noscroll="open !== null">
                    <img :src="items[open]" alt="" class="max-h-[85dvh] max-w-full rounded-box object-contain">
                    <button type="button" class="absolute right-4 top-4 grid size-12 place-items-center rounded-ui bg-white/10 text-white hover:bg-white/20" @click="open = null" aria-label="Fermer"><x-onoh.icon name="close" /></button>
                    <button type="button" class="absolute left-3 top-1/2 grid size-12 -translate-y-1/2 place-items-center rounded-ui bg-white/10 text-white hover:bg-white/20" @click="open = (open - 1 + items.length) % items.length" aria-label="Photo précédente"><x-onoh.icon name="chevron-left" /></button>
                    <button type="button" class="absolute right-3 top-1/2 grid size-12 -translate-y-1/2 place-items-center rounded-ui bg-white/10 text-white hover:bg-white/20" @click="open = (open + 1) % items.length" aria-label="Photo suivante"><x-onoh.icon name="chevron-right" /></button>
                </div>
            </section>
        @endif

        <p class="mt-12 flex flex-wrap gap-x-7 gap-y-3 border-t border-rule pt-6">
            <a href="{{ route('public.home') }}#donate" class="link">Soutenir nos actions <x-onoh.icon name="arrow-right" class="arrow" /></a>
            <a href="{{ route('public.blog') }}" class="link text-indigo">Tous les articles <x-onoh.icon name="arrow-right" class="arrow" /></a>
        </p>
    </article>
@endsection
