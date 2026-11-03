@if ($paginator->hasPages())
    <nav class="mt-12 flex items-center justify-center gap-1.5" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="grid size-11 place-items-center rounded-ui border border-rule text-rule" aria-hidden="true"><x-onoh.icon name="chevron-left" class="size-[18px]" /></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="grid size-11 place-items-center rounded-ui border border-rule text-indigo hover:border-indigo" aria-label="Page précédente"><x-onoh.icon name="chevron-left" class="size-[18px]" /></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-1 text-slate">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="grid size-11 place-items-center rounded-ui bg-indigo font-bold tabular-nums text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="hidden size-11 place-items-center rounded-ui border border-rule font-semibold tabular-nums text-indigo hover:border-indigo sm:grid">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="grid size-11 place-items-center rounded-ui border border-rule text-indigo hover:border-indigo" aria-label="Page suivante"><x-onoh.icon name="chevron-right" class="size-[18px]" /></a>
        @else
            <span class="grid size-11 place-items-center rounded-ui border border-rule text-rule" aria-hidden="true"><x-onoh.icon name="chevron-right" class="size-[18px]" /></span>
        @endif
    </nav>
@endif
