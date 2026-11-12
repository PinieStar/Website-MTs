@extends('layouts.public')

@section('title', 'Galeri Foto Kegiatan')
@section('meta_description', 'Galeri foto kegiatan, pembelajaran, dan keseharian siswa MTs NU Al Syairiyah, Kabupaten Batang.')

@php
    $spans = [
        'md:col-span-3 lg:col-span-5 lg:row-span-2',
        'md:col-span-3 lg:col-span-7',
        'md:col-span-2 lg:col-span-4',
        'md:col-span-2 lg:col-span-3',
        'md:col-span-2 lg:col-span-7',
        'md:col-span-6 lg:col-span-5',
    ];
@endphp

@section('content')
    <x-public.page-hero eyebrow="Galeri"
                         title="Potret Madrasah"
                         lead="Kumpulan dokumentasi kegiatan, pembelajaran, dan keseharian madrasah." />

    <x-public.section class="pt-4 lg:pt-6" labelledby="galeri-title">
        <div class="flex flex-col gap-8">
            <div>
                <p class="eyebrow">
                    <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                    Dokumentasi
                </p>
                <h2 id="galeri-title" class="mt-4 font-display text-display font-bold text-teal-deep" data-reveal="lines">
                    Momen yang kami dokumentasikan
                </h2>
            </div>

            @if ($galleries->count())
                <div class="bento-grid" data-stagger>
                    @foreach ($galleries as $index => $gallery)
                        @php
                            $images = $gallery->images
                                ->filter(fn ($image) => $image->hasMedia('foto'))
                                ->values();
                            $cover = $images->first();
                            $caption = $gallery->keterangan ?: $gallery->album;
                            $slides = $images->map(fn ($image) => [
                                'src' => $image->getFirstMediaUrl('foto'),
                                'alt' => $caption,
                                'caption' => $caption,
                            ])->all();
                        @endphp

                        <article class="bento group relative overflow-hidden {{ $spans[$index % count($spans)] }}">
                            <button type="button"
                                    class="block h-full w-full text-left"
                                    data-lightbox-items="{{ json_encode($slides, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}">
                                <div class="relative aspect-[4/3] h-full w-full min-h-[13rem] min-w-0 overflow-hidden bg-teal-deep">
                                    <x-public.pattern variant="star" class="text-paper" :opacity="0.07" />

                                    @if ($cover)
                                        <img src="{{ $cover->getFirstMediaUrl('foto') }}"
                                             alt="{{ $caption ?: 'Dokumentasi madrasah' }}"
                                             loading="lazy"
                                             decoding="async"
                                             class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.04]">
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="flex h-16 w-16 items-center justify-center rounded-full border border-paper/20 font-display text-sm font-bold text-gold">
                                                MTs
                                            </span>
                                        </div>
                                    @endif

                                    @if ($images->count() > 1)
                                        <span class="absolute right-3 top-3 z-10 inline-flex items-center gap-1.5 rounded-pill bg-teal-deep/80 px-2.5 py-1 text-[0.65rem] font-semibold text-paper backdrop-blur-sm">
                                            <x-public.icon name="image" :size="13" />
                                            {{ $images->count() }} Foto
                                        </span>
                                    @endif

                                    <div class="absolute inset-x-0 bottom-0 overlay-bottom p-4 pt-16">
                                        <p class="text-pretty break-words text-xs font-semibold leading-snug text-paper">
                                            {{ $gallery->album }}
                                        </p>
                                        @if ($gallery->keterangan)
                                            <p class="mt-0.5 line-clamp-1 text-pretty break-words text-[0.65rem] leading-snug text-paper/75">
                                                {{ $gallery->keterangan }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </button>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="bento flex flex-col items-center gap-3 border-dashed p-8 text-center sm:p-10">
                    <span class="h-px w-12 bg-gold" aria-hidden="true"></span>
                    <p class="font-display text-xl font-bold text-teal-deep">Belum ada foto galeri</p>
                    <p class="max-w-md text-sm leading-relaxed text-ink-soft">
                        Dokumentasi kegiatan madrasah akan tampil di sini setelah album diunggah.
                    </p>
                </div>
            @endif
        </div>
    </x-public.section>

    @if ($galleries->count())
        <dialog class="lightbox" data-lightbox aria-label="Penampil foto galeri">
            <div class="relative flex w-full max-w-5xl items-center justify-center">
                <button type="button"
                        class="absolute right-0 top-0 z-10 grid size-11 place-items-center rounded-full bg-paper/10 text-paper transition-colors hover:bg-paper/20"
                        aria-label="Tutup galeri"
                        data-lightbox-close>
                    <x-public.icon name="x" :size="20" />
                </button>

                <button type="button"
                        class="absolute left-2 top-1/2 z-10 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-paper/10 text-paper transition-colors hover:bg-paper/20"
                        aria-label="Foto sebelumnya"
                        data-lightbox-prev>
                    <x-public.icon name="chevron-left" :size="22" />
                </button>

                <figure class="lightbox__figure" data-lightbox-stage>
                    <div class="lightbox__frame" data-lightbox-frame>
                        <img class="lightbox__image" alt="" data-lightbox-image draggable="false">
                    </div>
                    <figcaption class="mt-4 flex flex-col gap-2 text-paper sm:flex-row sm:items-center sm:justify-between">
                        <span class="text-sm text-paper/80" data-lightbox-caption></span>
                        <span class="text-xs tabular-nums text-paper/50" data-lightbox-counter></span>
                    </figcaption>
                </figure>

                <button type="button"
                        class="absolute right-2 top-1/2 z-10 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-paper/10 text-paper transition-colors hover:bg-paper/20"
                        aria-label="Foto berikutnya"
                        data-lightbox-next>
                    <x-public.icon name="chevron-right" :size="22" />
                </button>
            </div>
        </dialog>
    @endif
@endsection
