@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-center">
        <ul class="flex flex-wrap items-center justify-center gap-1.5 text-sm">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="inline-flex h-9 items-center justify-center rounded-lg border border-ink-200 bg-ink-100 px-3 text-xs font-semibold text-ink-400">Prev</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-9 items-center justify-center rounded-lg border border-ink-200 bg-white px-3 text-xs font-semibold text-ink-700 transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700">Prev</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="inline-flex h-9 items-center justify-center rounded-lg border border-ink-200 bg-white px-3 text-xs font-semibold text-ink-500">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span aria-current="page" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-brand-600 bg-brand-600 px-3 text-xs font-semibold text-white shadow-sm">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-ink-200 bg-white px-3 text-xs font-semibold text-ink-700 transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-9 items-center justify-center rounded-lg border border-ink-200 bg-white px-3 text-xs font-semibold text-ink-700 transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700">Next</a>
                </li>
            @else
                <li>
                    <span class="inline-flex h-9 items-center justify-center rounded-lg border border-ink-200 bg-ink-100 px-3 text-xs font-semibold text-ink-400">Next</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
