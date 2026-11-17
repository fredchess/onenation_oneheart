@php($url = route('public.blog.details', ['blog_slug' => $blog->slug]))
<article class="group relative flex flex-col" data-reveal style="--delay: {{ $delay ?? 0 }}ms">
    <div class="photo relative aspect-[4/3]">
        @php($fallback = \App\Support\SiteImages::fallbackUrl($blog->id))
        <img src="{{ $blog->getFirstMediaUrl('images') ?: $fallback }}" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $fallback }}'">
        <time datetime="{{ $blog->created_at->toDateString() }}" class="absolute bottom-0 left-0 rounded-tr-ui bg-indigo px-2.5 py-1.5 text-xs font-semibold uppercase tracking-[.08em] text-white">{{ $blog->created_at->translatedFormat('d M Y') }}</time>
    </div>
    <h3 class="display mt-4 text-[1.65rem] text-indigo">
        <a href="{{ $url }}" class="after:absolute after:inset-0 after:content-['']">{{ $blog->name }}</a>
    </h3>
    <p class="mt-1.5 line-clamp-2 text-[15px] text-slate">{{ $blog->datas['description'] ?? '' }}</p>
    <span class="link mt-3">Lire l'article <x-onoh.icon name="arrow-right" class="arrow" /></span>
</article>
