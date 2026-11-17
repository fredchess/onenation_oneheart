@extends('front.layouts.app')
@section('title', 'Blogs')
@section('meta_description', 'Les actions menées par One Nation One Heart dans les orphelinats du Cameroun grâce à vos dons.')

@section('content')
    @include('front.partials.page-hero', ['title' => 'Blog', 'crumbs' => ['Blog' => null], 'site' => 'hero_blog'])

    <section class="wrap py-12 lg:py-16">
        @if ($blogs->count() > 0)
            <div class="grid gap-x-7 gap-y-11 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($blogs as $blog)
                    @include('front.partials.blog-card', ['blog' => $blog, 'delay' => ($loop->index % 3) * 70])
                @endforeach
            </div>
            {{ $blogs->links('front.partials.pagination') }}
        @else
            <div class="rounded-box border border-rule bg-paper px-6 py-14 text-center">
                <img src="{{ asset('lovecare/images/empty.svg') }}" alt="" class="mx-auto w-44" loading="lazy">
                <p class="display mt-6 text-3xl text-indigo">Pas d'articles enregistrés pour l'instant</p>
            </div>
        @endif
    </section>
@endsection
