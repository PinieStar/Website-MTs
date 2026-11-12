@if ($paginator->hasPages())
    @php
        $base = 'inline-flex h-10 min-w-10 items-center justify-center rounded-lg border px-3 text-sm font-semibold transition';
        $idle = 'border-line bg-bone text-ink-soft hover:border-teal-bright/40 hover:text-ink';
        $disabled = 'cursor-not-allowed border-line bg-bone text-ink-soft opacity-40';
    @endphp

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
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $base }} {{ $idle }} gap-1">
                    <span aria-hidden="true">&larr;</span>
                    <span class="hidden sm:inline">Sebelumnya</span>
                    <span class="sr-only sm:hidden">Sebelumnya</span>
                </a>
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
                        <span class="hidden sm:inline-flex">
                            @if ($page == $paginator->currentPage())
                                <span class="{{ $base }} border-brand bg-brand text-on-brand" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="{{ $base }} {{ $idle }}"
                                   aria-label="Ke halaman {{ $page }}">{{ $page }}</a>
                            @endif
                        </span>
                    @endforeach
                @endif
            @endforeach

            {{-- Berikutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $base }} {{ $idle }} gap-1">
                    <span class="hidden sm:inline">Berikutnya</span>
                    <span class="sr-only sm:hidden">Berikutnya</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
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
