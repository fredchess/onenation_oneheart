{{-- Carte membre : photo portrait, dégradé en bas (comme les bannières), nom, rôle et réseaux.
     Au survol, seule la photo zoome légèrement : la carte ne bouge pas. --}}
@php
    $photo = $member->getFirstMediaUrl('photo');
    $socials = $member->socialLinks();
@endphp
<article class="group relative isolate aspect-[4/5] overflow-hidden rounded-box bg-indigo text-white">
    {{-- Initiales en fond : visibles sans photo ou si la photo ne charge pas --}}
    <span class="ndop absolute inset-0 -z-20 grid place-items-center pb-16 font-display text-6xl font-extrabold" aria-hidden="true">{{ Str::of($member->name)->explode(' ')->filter()->map(fn ($p) => Str::upper(Str::substr($p, 0, 1)))->take(2)->join('') }}</span>
    @if ($photo)
        <img src="{{ $photo }}" alt="{{ $member->name }}" loading="lazy" decoding="async" onerror="this.remove()"
             class="absolute inset-0 -z-10 size-full object-cover transition-transform duration-700 ease-out-soft group-hover:scale-[1.04] motion-reduce:transition-none motion-reduce:group-hover:scale-100">
    @endif
    <div class="absolute inset-x-0 bottom-0 -z-10 h-3/5 bg-[linear-gradient(0deg,rgba(11,15,48,.95)_0%,rgba(11,15,48,.6)_50%,rgba(11,15,48,0)_100%)]" aria-hidden="true"></div>
    <div class="flex h-full flex-col justify-end p-4 sm:p-5">
        <h3 class="display text-[1.6rem] sm:text-3xl">{{ $member->name }}</h3>
        @if ($member->role)<p class="mt-0.5 text-sm text-sky">{{ $member->role }}</p>@endif
        @if ($socials)
            <ul class="-ml-1.5 mt-2 flex flex-wrap" aria-label="Réseaux de {{ $member->name }}">
                @foreach ($socials as $network => $url)
                    <li>
                        <a href="{{ $url }}" target="_blank" rel="noopener" class="grid size-9 place-items-center text-white/85 transition-colors hover:text-white"
                           aria-label="{{ \App\Models\TeamMember::SOCIALS[$network] }} de {{ $member->name }}">
                            <x-onoh.brand :name="$network" class="size-[18px]" />
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</article>
