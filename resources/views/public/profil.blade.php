@extends('layouts.public')

@section('title', 'Profil, Sejarah, Visi & Misi')
@section('meta_description', 'Profil MTs NU Al Syairiyah, Batang: sejarah sejak 1986, visi, misi, tujuan, serta data guru dan tenaga kependidikan.')

@php
    $site = config('site');
    $editorial = $site['editorial'];
    $linimasa = $editorial['sejarah']['linimasa'];
    $misi = $editorial['misi'];
    $tujuan = $editorial['tujuan'];
    $tahunBerdiri = $editorial['tahun_berdiri'];
    $visi = $editorial['visi'];
@endphp

@section('content')
    <x-public.page-hero eyebrow="Profil Madrasah"
                         title="Mengenal lebih dekat madrasah kami."
                         lead="Sejarah, visi-misi, dan tujuan pendidikan madrasah, dirangkum dalam satu cerita."
                         headingId="profil-title" />

    {{-- Profil madrasah: sejarah, visi-misi, tujuan, dan alasan dalam satu bagian --}}
    <x-public.section class="pt-4 lg:pt-6" labelledby="profil-title">
        <div class="space-y-6">
            {{-- Foto gedung + sejarah singkat --}}
            <div class="grid gap-6 lg:grid-cols-12" data-reveal="fade">
                <div class="bento relative overflow-hidden p-2 lg:col-span-5">
                    <div class="relative h-64 w-full overflow-hidden rounded-[calc(var(--radius-bento)-0.5rem)] bg-teal-deep sm:h-80 lg:h-full lg:min-h-[26rem]">
                        <img src="/images/gedung.jpg"
                             alt="Gedung {{ $site['name'] }} Limpung"
                             loading="eager"
                             decoding="async"
                             class="h-full w-full object-cover object-center">

                        <div class="absolute inset-0 bg-gradient-to-t from-teal-deep/85 via-teal-deep/15 to-transparent" aria-hidden="true"></div>

                        <span class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full bg-paper/90 px-3 py-1.5 text-[0.65rem] font-bold uppercase tracking-[0.16em] text-teal-deep backdrop-blur-sm">
                            <x-public.icon name="landmark" :size="12" />
                            {{ $tahunBerdiri ? 'Sejak '.$tahunBerdiri : 'Tahun berdiri belum tersedia' }}
                        </span>

                        <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6">
                            <p class="font-display text-lg font-bold leading-tight text-paper">Gedung {{ $site['name'] }}</p>
                            <p class="mt-1 flex items-center gap-1.5 text-xs font-medium text-paper/70">
                                <x-public.icon name="map-pin" :size="12" />
                                Plumbon, Limpung, Batang
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bento relative overflow-hidden p-7 sm:p-9 lg:col-span-7">
                    <x-public.pattern variant="arch" class="text-teal-deep" :opacity="0.04" />

                    <div class="relative flex h-full flex-col justify-center">
                        <p class="eyebrow">
                            <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                            Sejarah Singkat
                        </p>
                        <h2 class="mt-4 font-display text-[clamp(1.4rem,2.2vw,1.8rem)] font-bold leading-snug text-teal-deep">
                            Mewarisi Nilai, Melanjutkan Perjuangan
                        </h2>

                        <ol class="mt-8 space-y-8" data-stagger>
                            @foreach ($linimasa as $index => $item)
                                <li class="relative pl-12">
                                    @if ($index < count($linimasa) - 1)
                                        <span class="absolute left-[1.06rem] top-9 -bottom-8 border-l-2 border-dashed border-teal-bright/25" aria-hidden="true"></span>
                                    @endif
                                    <span class="absolute left-0 top-0 grid size-9 place-items-center rounded-full bg-gold text-paper ring-4 ring-bone" aria-hidden="true">
                                        <x-public.icon name="landmark" :size="15" />
                                    </span>
                                    <p class="font-display text-2xl font-bold leading-none text-teal-deep">{{ $item['tahun'] }}</p>
                                    <p class="mt-2 text-sm leading-relaxed text-ink-soft">{{ $item['teks'] }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>

            {{-- Visi & misi --}}
            <div class="grid items-stretch gap-6 lg:grid-cols-12" data-reveal="fade">
                <div class="bento bento-dark relative overflow-hidden p-7 sm:p-9 lg:col-span-5">
                    <x-public.pattern variant="star" class="text-paper" :opacity="0.07" />
                    <div class="relative flex h-full flex-col justify-center">
                        <p class="eyebrow text-gold-soft">
                            <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                            Visi
                        </p>
                        @if ($visi)
                            <h2 class="mt-6 text-center font-display text-[clamp(1.4rem,2.4vw,2rem)] font-bold leading-tight text-paper">
                                {{ $visi }}
                            </h2>
                        @else
                            <p class="mt-4 text-center text-sm leading-relaxed text-paper/65">
                                Rumusan visi resmi madrasah belum tersedia.
                            </p>
                        @endif
                    </div>
                </div>

                <div class="bento p-7 sm:p-9 lg:col-span-7">
                    <p class="eyebrow">Misi</p>
                    <h2 class="mt-4 font-display text-card font-bold text-teal-deep">Langkah untuk mewujudkannya.</h2>

                    <ol class="mt-6 space-y-3">
                        @foreach ($misi as $index => $item)
                            <li class="flex items-center gap-4 rounded-2xl border border-line-soft bg-paper/60 p-4">
                                <span class="grid size-8 shrink-0 place-items-center rounded-full bg-teal-deep text-xs font-bold text-paper">
                                    {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <p class="text-sm text-left leading-relaxed text-ink-soft">{{ $item }}</p>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>

            {{-- Tujuan madrasah --}}
            <div class="bento p-7 sm:p-9" data-reveal="fade">
                <p class="eyebrow">
                    <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                    Tujuan Madrasah
                </p>
                <h2 class="mt-4 max-w-2xl font-display text-card font-bold text-teal-deep">
                    Capaian yang hendak diwujudkan bersama.
                </h2>

                <ol class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
                    @foreach ($tujuan as $index => $item)
                        <li class="flex gap-3 rounded-2xl border border-line-soft bg-paper/60 p-4">
                            <span class="grid size-7 shrink-0 place-items-center rounded-full bg-mint text-[0.68rem] font-bold text-teal-bright">
                                {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <p class="text-sm leading-relaxed text-ink-soft">{{ $item }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </x-public.section>
@endsection
