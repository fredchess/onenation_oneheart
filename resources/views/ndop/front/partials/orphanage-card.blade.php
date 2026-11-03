@php
    $url = route('public.orphanages.details', ['orphanage_slug' => $orphanage->slug]);
    $cover = $orphanage->getFirstMediaUrl('profile_images');
    $raised = $orphanage->dons->where('payment_status', \App\Enums\PaymentStatus::SUCCESS)->sum('amount');
    $needs = $orphanage->filledNeeds();
    $place = collect([$orphanage->city->name ?? null, Str::limit(trim(strip_tags($orphanage->data_address['localisation'] ?? '')), 32)])->filter()->join(' · ');
@endphp
<article class="group relative flex flex-col" data-reveal style="--delay: {{ $delay ?? 0 }}ms">
    <div class="photo relative aspect-[4/3]">
        @php($fallback = \App\Support\SiteImages::fallbackUrl($orphanage->id))
        <img src="{{ $cover ?: $fallback }}" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $fallback }}'">
        @if ($place)
            <span class="absolute bottom-0 left-0 max-w-[92%] truncate rounded-tr-ui bg-indigo px-2.5 py-1.5 text-xs font-semibold uppercase tracking-[.08em] text-white" title="{{ $place }}">{{ $place }}</span>
        @endif
    </div>
    <h3 class="display mt-4 text-[1.65rem] text-indigo">
        <a href="{{ $url }}" class="after:absolute after:inset-0 after:content-['']">{{ $orphanage->name }}</a>
    </h3>
    @if (!empty($orphanage->data_identity['mini_description']))
        <p class="mt-1.5 line-clamp-2 text-[15px] text-slate">{{ $orphanage->data_identity['mini_description'] }}</p>
    @endif
    @if ($needs)
        <ul class="mt-2.5 flex flex-wrap gap-1.5" aria-label="Besoins">
            @foreach ($needs as $need)
                <li class="rounded-ui border border-rule bg-paper px-2 py-0.5 text-xs font-semibold text-indigo">{{ $need }}</li>
            @endforeach
        </ul>
    @endif
    <div class="mt-auto pt-3">
        <p class="flex justify-between border-t border-rule pt-3 text-sm">
            <span>{{ $orphanage->data_stats['children_number'] ?? 0 }} enfants</span>
            <span>Dons : <b class="tabular-nums text-indigo">{{ number_format($raised, 0, ',', ' ') }} FCFA</b></span>
        </p>
    </div>
</article>
