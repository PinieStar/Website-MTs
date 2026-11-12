@extends('layouts.public')

@section('title', 'Prestasi Siswa & Madrasah')
@section('meta_description', 'Daftar prestasi siswa MTs NU Al Syairiyah, Batang, dari tingkat kecamatan hingga provinsi pada bidang akademik, keagamaan, olahraga, dan seni.')

@php
    $site = config('site');
    $pageSize = 8;

    $tingkatLabel = [
        'madrasah' => 'Madrasah',
        'kecamatan' => 'Kecamatan',
        'kabupaten' => 'Kabupaten',
        'provinsi' => 'Provinsi',
        'nasional' => 'Nasional',
    ];
@endphp

@section('content')
    <x-public.page-hero eyebrow="Prestasi"
                         title="Jejak Prestasi Siswa Kami"
                         lead="Capaian siswa dan madrasah di berbagai ajang perlombaan, dari tingkat madrasah hingga nasional." />

    <x-public.section class="pt-4 lg:pt-6" labelledby="prestasi-title">
        <div class="flex flex-col gap-8">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="eyebrow">
                        <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                        Penghargaan
                    </p>
                    <h2 id="prestasi-title" class="mt-4 font-display text-display font-bold text-teal-deep" data-reveal="lines">
                        Mengukir Prestasi, Menginspirasi Negeri
                    </h2>
                </div>
            </div>

            @if ($total > 0)
                <div class="grid grid-cols-2 gap-3" data-stagger>
                    <div class="bento bento-dark relative flex flex-col justify-between gap-8 overflow-hidden p-5 sm:p-7">
                        <div class="pointer-events-none absolute -right-14 -top-16 size-52 rounded-full bg-teal-bright/25 blur-2xl" aria-hidden="true"></div>
                        <div class="relative flex items-center gap-2">
                            <span class="h-px w-8 bg-gold" aria-hidden="true"></span>
                            <span class="text-[0.6rem] font-bold uppercase tracking-[0.18em] text-gold-soft">Rekap</span>
                        </div>
                        <div class="relative">
                            <p class="whitespace-nowrap font-display text-[clamp(2rem,5vw,3.25rem)] font-extrabold leading-none tracking-[-0.04em] text-paper tabular-nums"
                               data-count="{{ $total }}"
                               aria-label="{{ $total }} prestasi tercatat">
                                {{ $total }}
                            </p>
                            <p class="mt-2 text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-paper/60 sm:text-xs">
                                Prestasi tercatat
                            </p>
                        </div>
                    </div>

                    <div class="bento relative flex flex-col justify-between gap-8 overflow-hidden p-5 sm:p-7">
                        <div class="pointer-events-none absolute -right-10 -top-10 size-28 rounded-full border border-gold/20" aria-hidden="true"></div>
                        <div class="flex items-center gap-2">
                            <span class="h-px w-8 bg-gold" aria-hidden="true"></span>
                            <span class="text-[0.6rem] font-bold uppercase tracking-[0.18em] text-ink-soft">Periode</span>
                        </div>
                        <div>
                            <p class="whitespace-nowrap font-display text-[clamp(1.5rem,4.2vw,3.25rem)] font-extrabold leading-none tracking-[-0.04em] text-teal-deep">
                                {{ $years->count() > 1 ? $years->last().' - '.$years->first() : $years->first() }}
                            </p>
                            <p class="mt-2 text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-ink-soft sm:text-xs">
                                Rentang tahun
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid scroll-mt-28 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                     data-stagger
                     data-pager-grid="prestasi"
                     data-page-size="{{ $pageSize }}">
                    @foreach ($items as $index => $item)
                        <article class="bento group relative flex flex-col gap-4 overflow-hidden p-5 transition-[translate,transform,box-shadow,border-color] duration-[550ms] ease-[cubic-bezier(0.22,1,0.36,1)] hover:-translate-y-1.5 hover:border-teal-bright/30 hover:shadow-lift"
                                 data-filter-target="prestasi"
                                 data-category="{{ $item->bidang }}"
                                 @if ($index >= $pageSize) data-page-hidden="true" @endif>
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="rounded-badge bg-gold px-2.5 py-1 text-[0.6rem] font-bold uppercase tracking-[0.14em] text-teal-950">
                                    {{ $item->peringkat }}
                                </span>
                                <span class="rounded-badge border border-line px-2.5 py-1 text-[0.6rem] font-bold uppercase tracking-[0.14em] text-teal-deep">
                                    {{ $tingkatLabel[$item->tingkat] ?? $item->labelTingkat() }}
                                </span>
                            </div>

                            <div class="flex-1">
                                <h3 class="text-pretty font-display text-base font-bold leading-snug text-teal-deep sm:text-lg">
                                    {{ $item->judul }}
                                </h3>
                            </div>

                            <p class="flex flex-wrap items-center gap-x-2 gap-y-1 border-t border-line pt-3 text-xs text-ink-soft">
                                <span class="tabular-nums">{{ $item->tahun }}</span>
                                @if ($item->penyelenggara)
                                    <span aria-hidden="true">·</span>
                                    <span>{{ $item->penyelenggara }}</span>
                                @endif
                            </p>
                        </article>
                    @endforeach
                </div>

                <nav class="flex flex-wrap items-center justify-center gap-2" aria-label="Halaman prestasi" data-pager="prestasi"></nav>

            @else
                <div class="bento relative flex flex-col justify-center overflow-hidden p-6 sm:p-8">
                    <div class="relative flex max-w-2xl flex-col gap-4 sm:flex-row sm:items-start">
                        <span class="grid size-11 shrink-0 place-items-center rounded-full border border-teal-bright/15 bg-mint text-teal-bright" aria-hidden="true">
                            <x-public.icon name="trophy" :size="19" />
                        </span>
                        <div>
                            <h3 class="font-display text-card font-bold text-teal-deep">Data prestasi belum tersedia</h3>
                            <p class="mt-2 text-sm leading-relaxed text-ink-soft">
                                Daftar prestasi siswa dan madrasah akan tampil setelah data resmi ditambahkan.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-public.section>

    <x-public.section class="pt-0">
        <div class="bento relative overflow-hidden p-7 sm:p-9 lg:p-11" data-reveal="fade">
            <div class="pointer-events-none absolute -right-24 -top-28 size-72 rounded-full bg-[radial-gradient(circle,rgb(15_118_110/0.10),transparent_65%)]" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-20 -left-12 size-52 rounded-full border border-gold/15" aria-hidden="true"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="eyebrow">
                        <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                        Kabar terbaru
                    </p>
                    <h2 class="mt-4 max-w-xl font-display text-title font-bold text-teal-deep">
                        Ikuti perjalanan prestasi kami di Instagram.
                    </h2>
                </div>
                <a href="{{ $site['instagram_url'] }}" target="_blank" rel="noopener noreferrer" class="text-link shrink-0">
                    Buka {{ $site['instagram_handle'] }}
                    <span aria-hidden="true" class="text-base leading-none">↗</span>
                </a>
            </div>
        </div>
    </x-public.section>
@endsection
