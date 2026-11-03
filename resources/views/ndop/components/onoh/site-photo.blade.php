@props(['for', 'alt' => '', 'sizes' => '100vw', 'eager' => false])
{{-- Image modifiable depuis Filament (Images et affichage), sinon photo par défaut --}}
@php($custom = \App\Support\SiteImages::url($for))
@if ($custom)
    <img src="{{ $custom }}" alt="{{ $alt }}" decoding="async" @if ($eager) fetchpriority="high" @else loading="lazy" @endif {{ $attributes }}>
@else
    <x-onoh.photo :name="\App\Support\SiteImages::defaultPhoto($for)" :alt="$alt" :sizes="$sizes" :eager="$eager" {{ $attributes }} />
@endif
