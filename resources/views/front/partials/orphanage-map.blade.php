{{--
    Carte des orphelinats. $points : App\Support\OrphanageMap::points(), $class : hauteur,
    $fit : "points" (cadre sur les résultats) ou "country" (tout le Cameroun).
    Leaflet n'est chargé que lorsque la carte arrive à l'écran.
--}}
<div x-data="orphanageMap" data-fit="{{ $fit ?? 'points' }}" x-intersect.once.margin.200px="mount()" class="relative overflow-hidden rounded-box border border-rule {{ $class ?? 'h-[440px] md:h-[540px]' }}">
    <script type="application/json" x-ref="points">@json($points)</script>
    <div x-ref="canvas" class="om-map absolute inset-0" role="region" aria-label="Carte des orphelinats au Cameroun"></div>

    <div x-show="loading" class="absolute inset-0 grid place-items-center bg-paper text-sm text-slate">Chargement de la carte…</div>
    <div x-show="failed" x-cloak class="absolute inset-0 grid place-items-center bg-paper px-6 text-center text-sm text-slate">La carte n'a pas pu être chargée.</div>

    @if (count($points) === 0)
        <p class="pointer-events-none absolute inset-x-0 top-4 z-[500] mx-auto w-fit rounded-ui bg-white px-4 py-2 text-sm text-slate shadow">Aucun orphelinat localisé pour cette recherche.</p>
    @endif
</div>
<p class="mt-3 text-sm text-slate">
    <span class="hidden [@media(hover:hover)]:inline">Cliquez</span><span class="[@media(hover:hover)]:hidden">Touchez</span>
    un point rouge pour voir les chiffres de l'orphelinat. Les numéros regroupent plusieurs orphelinats proches.
</p>
