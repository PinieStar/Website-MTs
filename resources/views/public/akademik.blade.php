@extends('layouts.public')

@section('title', 'Program Akademik & Ekstrakurikuler')
@section('meta_description', 'Program unggulan MTs NU Al Syairiyah: Tahfidz & Tahsin Al-Qur\'an, KKO, KKNO, penguatan Aswaja, dan ekstrakurikuler Pramuka hingga Marching Band.')

@php
    $site = config('site');
    $programs = $site['editorial']['programs'];
    $ekskul = $site['editorial']['ekskul'];
    usort($ekskul, fn ($a, $b) => ($a['urutan'] ?? 0) <=> ($b['urutan'] ?? 0) ?: strcmp($a['nama'], $b['nama']));
@endphp

@section('content')
    <x-public.page-hero eyebrow="Akademik"
                         title="Program Pendidikan Berkualitas"
                         lead="Kurikulum terpadu yang mengintegrasikan ilmu pengetahuan dengan nilai-nilai Islam." />

    {{-- Program unggulan --}}
    <x-public.section class="pt-4 lg:pt-6" labelledby="program-title">
        <x-public.section-header eyebrow="Program Unggulan"
                                  number="01"
                                  title="Membangun potensi, menciptakan generasi unggul."
                                  lead="Program unggulan yang dirancang untuk mengembangkan kemampuan, bakat, dan karakter Islami setiap siswa."
                                  headingId="program-title" />

        <div class="mt-10 grid gap-6">
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($programs as $index => $program)
                    <article class="bento group p-6 transition-[translate,transform,box-shadow,border-color] duration-[550ms] ease-[cubic-bezier(0.22,1,0.36,1)] hover:-translate-y-1 hover:border-teal-bright/30 hover:shadow-lift" data-reveal="fade">
                        <div class="flex items-start gap-4">
                            <span class="mt-0.5 font-display text-sm font-bold text-gold">
                                {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <div>
                                <h3 class="font-display text-card font-bold text-teal-deep">{{ $program['title'] }}</h3>
                                <p class="mt-2 text-sm leading-relaxed text-ink-soft">{{ $program['description'] ?? 'Deskripsi program belum tersedia.' }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </x-public.section>

    {{-- Ekstrakurikuler --}}
    <x-public.section id="ekstrakurikuler" labelledby="ekskul-title">
        <div class="flex flex-col gap-6">
            <x-public.section-header eyebrow="Ekstrakurikuler"
                                      number="02"
                                      title="Ruang untuk mencoba, berkarya, dan bertumbuh."
                                      lead="Kegiatan di luar jam belajar yang menumbuhkan minat, bakat, dan kebersamaan."
                                      headingId="ekskul-title" />

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
                @foreach ($ekskul as $item)
                    <article class="bento group relative flex flex-col overflow-hidden !p-0 transition-[translate,transform,box-shadow,border-color] duration-[550ms] ease-[cubic-bezier(0.22,1,0.36,1)] hover:-translate-y-1.5 hover:border-teal-bright/30 hover:shadow-xl">
                        <div class="relative aspect-[4/3] overflow-hidden bg-mint">
                            @if (! empty($item['foto']))
                                <img src="{{ $item['foto'] }}"
                                     alt="Kegiatan {{ $item['nama'] }}"
                                     width="640"
                                     height="480"
                                     loading="lazy"
                                     decoding="async"
                                     data-fade-img
                                     class="size-full object-cover opacity-0 transition-[opacity,scale,translate,transform] duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform group-hover:scale-105">
                            @else
                                <div class="relative grid size-full place-items-center text-teal-bright">
                                    <x-public.pattern variant="star" class="text-teal-deep" :opacity="0.06" />
                                    <x-public.icon :name="$item['ikon'] ?? 'sparkles'" :size="40" />
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-1 flex-col gap-2 p-5 text-center">
                            <h3 class="font-display text-card font-bold text-teal-deep">{{ $item['nama'] }}</h3>
                            <p class="text-sm leading-relaxed text-ink-soft">{{ $item['deskripsi'] ?: 'Deskripsi kegiatan belum tersedia.' }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </x-public.section>
@endsection
