<article class="mx-auto max-w-4xl px-4 pb-10 pt-32 sm:px-6 sm:pb-14 sm:pt-36 lg:pt-40">
    <a href="{{ route('public.'.$type.'.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-semibold text-teal-bright hover:underline">
        <span aria-hidden="true">←</span>
        Kembali ke {{ $type === 'berita' ? 'Berita & Pengumuman' : 'Artikel' }}
    </a>

    <header class="mt-6">
        <div class="flex flex-wrap items-center gap-2">
            <span class="rounded-full bg-teal-deep px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-paper">
                {{ $post->labelType() }}
            </span>
            @if ($post->type === 'berita')
                <span class="rounded-full bg-gold/15 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-gold">
                    {{ $post->labelKategori() }}
                </span>
            @endif
            @if ($post->sematkan)
                <span class="rounded-full border border-gold/30 bg-bone px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-gold">
                    Unggulan
                </span>
            @endif
        </div>

        <h1 class="mt-4 font-display text-3xl font-bold leading-tight text-teal-deep sm:text-4xl">
            {{ $post->judul }}
        </h1>

        <p class="mt-3 text-sm text-ink-soft">
            {{ $post->tanggalPublishLabel() }}
            <span class="mx-1.5 text-line">·</span>
            {{ $post->namaPenulis() }}
        </p>

        @if ($post->ringkasan)
            <p class="mt-5 max-w-3xl text-base leading-relaxed text-ink-soft sm:text-lg">
                {{ $post->ringkasan }}
            </p>
        @endif
    </header>

    @if ($post->hasMedia('sampul'))
        <figure class="mt-8 overflow-hidden rounded-2xl border border-line bg-bone">
            <img src="{{ $post->getFirstMediaUrl('sampul') }}"
                 alt="Sampul {{ $post->judul }}"
                 class="aspect-[16/9] w-full object-cover">
        </figure>
    @endif

    <div class="article-prose mt-8">
        {!! $post->sanitizedIsi(themeAware: true) !!}
    </div>
</article>
