{{--
    Bannière des pages intérieures : photo + dégradé, fil d'Ariane, titre.
    $title, $crumbs [label => url|null], et soit $site (emplacement de SiteImages) soit $image (URL)
--}}
<section class="relative h-[250px] overflow-hidden text-white sm:h-[300px] lg:h-[340px]">
    @isset($image)
        <img src="{{ $image }}" alt="" class="absolute inset-0 size-full object-cover" fetchpriority="high" onerror="this.onerror=null;this.src='{{ $fallback ?? asset('redesign/img/bg_2-1080.webp') }}'">
    @else
        <x-onoh.site-photo :for="$site" class="absolute inset-0 size-full object-cover" :eager="true" />
    @endisset
    <div class="absolute inset-0 bg-[linear-gradient(0deg,rgba(11,15,48,.95)_0%,rgba(11,15,48,.55)_55%,rgba(11,15,48,.25)_100%)]" aria-hidden="true"></div>
    <div class="wrap relative flex h-full flex-col justify-end pb-8 lg:pb-10">
        <nav aria-label="Fil d'Ariane" class="fade-up mb-3 text-sm text-[#cfd5ff]">
            <a href="{{ route('public.home') }}" class="hover:text-white">Accueil</a>
            @foreach ($crumbs ?? [] as $label => $url)
                <span class="mx-1">/</span>
                @if ($url)<a href="{{ $url }}" class="hover:text-white">{{ $label }}</a>@else<span class="font-semibold text-white">{{ Str::limit($label, 28) }}</span>@endif
            @endforeach
        </nav>
        <h1 class="display fade-up max-w-4xl {{ mb_strlen($title) > 24 ? 'text-[2.5rem] sm:text-6xl lg:text-7xl' : 'text-[3.25rem] sm:text-7xl lg:text-[5.5rem]' }}" style="--delay: 80ms">{{ $title }}</h1>
    </div>
</section>
<div class="band"></div>
