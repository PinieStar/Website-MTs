@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}"
         class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">

        <p class="text-xs text-ink-soft">
            Menampilkan
            @if ($paginator->firstItem())
                <span class="font-semibold text-ink">{{ $paginator->firstItem() }}</span>
                <span aria-hidden="true">-</span>
                <span class="font-semibold text-ink">{{ $paginator->lastItem() }}</span>
            @else
                <span class="font-semibold text-ink">{{ $paginator->count() }}</span>
            @endif
            dari
            <span class="font-semibold text-ink">{{ $paginator->total() }}</span>
            hasil
        </p>

        <div class="flex flex-wrap items-center justify-center gap-1.5">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}"
                      class="inline-flex min-h-10 cursor-not-allowed items-center justify-center gap-1 rounded-lg border border-line px-3 text-sm font-medium text-ink-soft opacity-40">
                    <span aria-hidden="true">‹</span>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   aria-label="{{ __('pagination.previous') }}"
                   class="inline-flex min-h-10 items-center justify-center gap-1 rounded-lg border border-line px-3 text-sm font-medium text-ink-soft transition hover:border-teal-bright/30 hover:text-teal-deep">
                    <span aria-hidden="true">‹</span>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true"
                          class="inline-flex min-h-10 min-w-10 cursor-default items-center justify-center rounded-lg border border-line px-2 text-sm font-medium text-ink-soft">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border border-teal-deep bg-teal-deep px-2 text-sm font-semibold text-paper">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                               class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border border-line px-2 text-sm font-medium text-ink-soft transition hover:border-teal-bright/30 hover:text-teal-deep">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   aria-label="{{ __('pagination.next') }}"
                   class="inline-flex min-h-10 items-center justify-center gap-1 rounded-lg border border-line px-3 text-sm font-medium text-ink-soft transition hover:border-teal-bright/30 hover:text-teal-deep">
                    <span class="hidden sm:inline">Berikutnya</span>
                    <span aria-hidden="true">›</span>
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}"
                      class="inline-flex min-h-10 cursor-not-allowed items-center justify-center gap-1 rounded-lg border border-line px-3 text-sm font-medium text-ink-soft opacity-40">
                    <span class="hidden sm:inline">Berikutnya</span>
                    <span aria-hidden="true">›</span>
                </span>
            @endif
        </div>
    </nav>
@endif
