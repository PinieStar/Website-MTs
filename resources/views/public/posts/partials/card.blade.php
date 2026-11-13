<a href="{{ route('public.'.$type.'.show', $post->slug) }}"
   class="bento group flex flex-col transition-[translate,transform,box-shadow,border-color] duration-300 hover:-translate-y-1 hover:border-teal-bright/30 hover:shadow-float">
    <div class="relative aspect-[16/10] overflow-hidden bg-teal-deep">
        @if ($post->hasMedia('sampul'))
            <img src="{{ $post->getFirstMediaUrl('sampul') }}"
                 alt="Sampul {{ $post->judul }}"
                 loading="lazy"
                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="flex h-full items-center justify-center">
                <span class="flex h-14 w-14 items-center justify-center rounded-full border border-paper/20 font-display text-xs font-bold text-gold">
                    MTs
                </span>
            </div>
        @endif

        <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
            @if ($post->sematkan)
                <span class="rounded-full bg-gold px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-teal-950">
                    Unggulan
                </span>
            @endif
            @if ($post->type === 'berita')
                <span class="rounded-full bg-bone/90 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-teal-deep backdrop-blur">
                    {{ $post->labelKategori() }}
                </span>
            @endif
        </div>
    </div>

    <div class="flex flex-1 flex-col p-5">
        <p class="text-xs font-medium text-ink-soft">{{ $post->tanggalPublishLabel() }}</p>
        <h2 class="mt-2 line-clamp-2 break-words font-display text-lg font-bold leading-snug text-teal-deep">
            {{ $post->judul }}
        </h2>
        @if ($post->ringkasan)
            <p class="mt-2 line-clamp-3 break-words text-sm leading-relaxed text-ink-soft">
                {{ $post->ringkasan }}
            </p>
        @endif
        <span class="mt-auto inline-flex items-center gap-1.5 pt-4 text-xs font-semibold text-teal-bright">
            Baca selengkapnya
            <span aria-hidden="true">→</span>
        </span>
    </div>
</a>
