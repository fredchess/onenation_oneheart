<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Accueil') - One Nation One Heart</title>
    <meta name="description" content="@yield('meta_description', 'One Nation One Heart recense les orphelinats et centres sociaux légaux du Cameroun. Faites un don ou devenez bénévole.')">
    <meta name="theme-color" content="#141a4a">
    <link rel="icon" href="{{ asset('redesign/img/logo.png') }}" type="image/png">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:title" content="@yield('meta_title', 'Smile Together - One Nation One Heart')">
    <meta property="og:description" content="@yield('meta_description', 'Faites un don à des orphelins avec One Nation One Heart')">
    <meta property="og:url" content="@yield('meta_url', url()->current())">
    <meta property="og:image" content="@yield('meta_image', asset('redesign/img/bg_4-1080.webp'))">
    <meta property="og:site_name" content="One Nation One Heart">
    <meta name="twitter:card" content="summary_large_image">

    @stack('preload')
    @vite(['resources/ndop/css/app.css', 'resources/ndop/js/app.js'])

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-SZLSCY3SQV"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-SZLSCY3SQV');
    </script>
</head>

<body class="min-h-dvh overflow-x-clip" x-data="{ scrolled: false, donateInView: false }" @scroll.window.throttle.100ms="scrolled = window.scrollY > 560">
    <a href="#contenu" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-ui focus:bg-white focus:px-4 focus:py-3 focus:text-indigo">Aller au contenu</a>

    @include('front.layouts.header')
    @include('front.partials.flash')

    <main id="contenu">
        @yield('content')
    </main>

    @include('front.layouts.footer')

    {{-- Barre d'action mobile, après le premier écran ; masquée quand le formulaire de don est visible --}}
    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-rule bg-white transition-transform duration-300 lg:hidden"
         :class="scrolled && !donateInView ? 'translate-y-0' : 'translate-y-full'" x-cloak
         style="padding-bottom: env(safe-area-inset-bottom, 0px)">
        <div class="wrap grid grid-cols-[1fr_1.2fr] gap-2 py-2.5">
            <a href="{{ route('public.orphanages') }}" class="btn shadow-[inset_0_0_0_2px_currentColor] min-h-11 px-3 text-[13px] text-indigo">Orphelinats</a>
            <a href="@yield('donate_anchor', route('public.home') . '#donate')" class="btn-coral min-h-11 px-3 text-[13px]">Faire un don</a>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
