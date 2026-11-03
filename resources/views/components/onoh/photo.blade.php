@props(['name', 'alt' => '', 'sizes' => '100vw', 'eager' => false])
{{-- Photo du site (public/redesign/img/{name}-{640|1080}.webp) avec srcset --}}
<img src="{{ asset("redesign/img/{$name}-1080.webp") }}"
     srcset="{{ asset("redesign/img/{$name}-640.webp") }} 640w, {{ asset("redesign/img/{$name}-1080.webp") }} 1080w"
     sizes="{{ $sizes }}" alt="{{ $alt }}" decoding="async"
     @if ($eager) fetchpriority="high" @else loading="lazy" @endif
     {{ $attributes }}>
