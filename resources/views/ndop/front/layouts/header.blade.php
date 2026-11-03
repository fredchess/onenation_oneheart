@php
    // Mêmes pages et même ordre que le menu d'origine
    $navItems = [
        ['label' => 'Accueil', 'route' => 'public.home', 'active' => Request::routeIs('public.home')],
        ['label' => 'Orphelinats', 'route' => 'public.orphanages', 'active' => Request::segment(1) == 'orphelinats'],
        ['label' => 'À propos de ONOH', 'route' => 'public.about', 'active' => Request::routeIs('public.about')],
        ['label' => 'Nous rejoindre', 'route' => 'public.joinus', 'active' => Request::routeIs('public.joinus')],
        ['label' => 'Blogs', 'route' => 'public.blog', 'active' => Request::segment(1) == 'blog'],
        ['label' => 'Contact', 'route' => 'public.contact', 'active' => Request::routeIs('public.contact')],
    ];
@endphp

{{-- À la fermeture du menu, le focus revient au bouton sans faire défiler la page (preventScroll) --}}
<header x-data="{ open: false }" @keydown.escape.window="open = false"
        x-init="$watch('open', (value) => value || $nextTick(() => $refs.burger.focus({ preventScroll: true })))"
        class="ndop sticky z-50 text-white" style="top: env(safe-area-inset-top, 0px)">
    <div class="wrap flex h-16 items-center gap-8 lg:h-[76px]">
        <a href="{{ route('public.home') }}" class="shrink-0 rounded-ui bg-white px-2 py-1.5" aria-label="One Nation One Heart, accueil">
            <img src="{{ asset('redesign/img/logo.webp') }}" alt="One Nation One Heart" width="360" height="129" class="h-7 w-auto lg:h-[34px]">
        </a>

        <nav class="ml-auto hidden lg:block" aria-label="Navigation principale">
            <ul class="flex items-center gap-6 text-[15px] font-semibold">
                @foreach ($navItems as $item)
                    <li>
                        <a href="{{ route($item['route']) }}" @if ($item['active']) aria-current="page" @endif
                           class="border-b-2 py-1.5 transition-colors {{ $item['active'] ? 'border-coral' : 'border-transparent hover:border-white/40' }}">{{ $item['label'] }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <a href="{{ route('public.home') }}#donate" class="btn-coral hidden lg:inline-flex">Faire un don</a>

        <button type="button" x-ref="burger" class="-mr-2 ml-auto grid size-12 place-items-center lg:hidden" @click="open = true"
                aria-label="Ouvrir le menu" :aria-expanded="open.toString()" aria-controls="menu-mobile">
            <x-onoh.icon name="menu" class="size-7" />
        </button>
    </div>

    {{-- Menu mobile plein écran --}}
    <div id="menu-mobile" x-show="open" x-cloak x-trap.noscroll.inert.noreturn="open"
         x-transition:enter="transition duration-300" x-transition:enter-start="opacity-0"
         x-transition:leave="transition duration-200" x-transition:leave-end="opacity-0"
         class="ndop fixed inset-0 z-[60] flex h-dvh flex-col overflow-y-auto lg:hidden"
         style="padding-top: env(safe-area-inset-top, 0px); padding-bottom: env(safe-area-inset-bottom, 0px)"
         role="dialog" aria-modal="true" aria-label="Menu">
        <div class="wrap flex h-16 shrink-0 items-center">
            <span class="rounded-ui bg-white px-2 py-1.5"><img src="{{ asset('redesign/img/logo.webp') }}" alt="" width="360" height="129" class="h-7 w-auto"></span>
            <button type="button" class="-mr-2 ml-auto grid size-12 place-items-center" @click="open = false" aria-label="Fermer le menu">
                <x-onoh.icon name="close" class="size-7" />
            </button>
        </div>
        <nav class="wrap mt-4 flex-1" aria-label="Navigation mobile">
            <ul class="divide-y divide-white/10 border-y border-white/10">
                @foreach ($navItems as $item)
                    <li>
                        <a href="{{ route($item['route']) }}" @click="open = false"
                           class="display flex items-center justify-between py-4 text-[2.1rem] {{ $item['active'] ? 'text-coral' : 'text-white' }}">
                            {{ $item['label'] }} <x-onoh.icon name="arrow-right" class="arrow text-sky" />
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
        <div class="wrap shrink-0 space-y-3 py-6">
            <a href="{{ route('public.home') }}#donate" @click="open = false" class="btn-coral w-full">Faire un don</a>
            <p class="text-center text-sm text-sky">contact@onoh.org · +237 6 55 02 98 67</p>
        </div>
    </div>
</header>
