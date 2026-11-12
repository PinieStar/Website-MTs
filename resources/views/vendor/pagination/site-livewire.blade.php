@php
    if (! isset($scrollTo)) {
        $scrollTo = 'body';
    }

    $scrollIntoViewJsSnippet = ($scrollTo !== false)
        ? "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()"
        : '';

    $base = 'inline-flex h-10 min-w-10 items-center justify-center rounded-lg border px-3 text-sm font-semibold transition';
    $idle = 'border-line bg-bone text-ink-soft hover:border-teal-bright/40 hover:text-ink';
    $disabled = 'cursor-not-allowed border-line bg-bone text-ink-soft opacity-40';
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman"
         class="flex flex-col items-center gap-3 sm:flex-row sm:justify-between">
        <p class="text-sm text-ink-soft">
            Menampilkan
            <span class="font-semibold text-ink">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-semibold text-ink">{{ $paginator->total() }}</span>
        </p>

        <div class="flex flex-wrap items-center justify-center gap-1.5">
            {{-- Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="{{ $base }} {{ $disabled }} gap-1" aria-disabled="true">
                    <span aria-hidden="true">&larr;</span>
                    <span class="hidden sm:inline">Sebelumnya</span>
                    <span class="sr-only sm:hidden">Sebelumnya</span>
                </span>
            @else
                <button type="button"
                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled"
                        class="{{ $base }} {{ $idle }} gap-1">
                    <span aria-hidden="true">&larr;</span>
                    <span class="hidden sm:inline">Sebelumnya</span>
                    <span class="sr-only sm:hidden">Sebelumnya</span>
                </button>
            @endif

            {{-- Ringkas di layar kecil --}}
            <span class="px-2 text-sm text-ink-soft sm:hidden">
                Hal. {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            {{-- Nomor halaman (layar lebar) --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="hidden h-10 items-center px-1 text-sm text-ink-soft sm:inline-flex">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <span wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}" class="hidden sm:inline-flex">
                            @if ($page == $paginator->currentPage())
                                <span class="{{ $base }} border-brand bg-brand text-on-brand" aria-current="page">{{ $page }}</span>
                            @else
                                <button type="button"
                                        wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                        class="{{ $base }} {{ $idle }}"
                                        aria-label="Ke halaman {{ $page }}">{{ $page }}</button>
                            @endif
                        </span>
                    @endforeach
                @endif
            @endforeach

            {{-- Berikutnya --}}
            @if ($paginator->hasMorePages())
                <button type="button"
                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled"
                        class="{{ $base }} {{ $idle }} gap-1">
                    <span class="hidden sm:inline">Berikutnya</span>
                    <span class="sr-only sm:hidden">Berikutnya</span>
                    <span aria-hidden="true">&rarr;</span>
                </button>
            @else
                <span class="{{ $base }} {{ $disabled }} gap-1" aria-disabled="true">
                    <span class="hidden sm:inline">Berikutnya</span>
                    <span class="sr-only sm:hidden">Berikutnya</span>
                    <span aria-hidden="true">&rarr;</span>
                </span>
            @endif
        </div>
    </nav>
@endif
