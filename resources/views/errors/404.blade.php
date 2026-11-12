@extends('layouts.public')

@section('title', 'Halaman Tidak Ditemukan')
@section('robots', 'noindex, nofollow')

@section('content')
    <section class="relative overflow-hidden pb-40 pt-32" aria-labelledby="not-found-title">
        {{-- Pattern geometris halus --}}
        <x-public.pattern variant="arch" class="text-teal-deep" :opacity="0.06" />

        {{-- Gelombang teal di dasar section --}}
        <svg class="pointer-events-none absolute inset-x-0 bottom-0 -z-10 h-56 w-full text-teal-deep"
             viewBox="0 0 1536 240"
             preserveAspectRatio="none"
             aria-hidden="true">
            <path fill="currentColor" d="M0,120 C280,200 520,20 820,80 C1120,140 1320,60 1536,120 L1536,240 L0,240 Z"></path>
        </svg>

        <div class="container-page relative z-10">
            <div class="grid items-center gap-12 lg:grid-cols-[1fr_0.95fr]">
                {{-- Kolom teks --}}
                <div class="max-w-xl">
                    <p class="eyebrow flex items-center gap-3" data-reveal="fade">
                        <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                        Kesalahan 404
                    </p>

                    <p class="mt-6 font-display text-[clamp(4.5rem,13vw,8.5rem)] font-extrabold leading-none tracking-[-0.06em] text-teal-deep" aria-hidden="true">
                        404
                    </p>

                    <h1 id="not-found-title" class="mt-4 font-display text-display font-bold text-teal-deep" data-reveal="lines">
                        Halaman ini tidak ditemukan.
                    </h1>

                    <p class="measure mt-5 text-ink-soft" data-reveal="fade">
                        Tautan mungkin sudah berubah atau halaman belum tersedia. Anda
                        dapat kembali ke beranda dan melanjutkan penjelajahan dari sana.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3" data-reveal="fade">
                        <x-public.button :href="route('public.home')" size="lg" icon="arrow-left">
                            Kembali ke Beranda
                        </x-public.button>

                        <x-public.button :href="route('public.profil')" variant="secondary" size="lg">
                            Kenali Madrasah
                        </x-public.button>
                    </div>
                </div>

                {{-- Kolom foto (tanpa card) --}}
                <div class="relative mx-auto w-full max-w-xl" data-reveal="fade">
                    <div class="pointer-events-none absolute inset-0 -z-10 rounded-full bg-[radial-gradient(circle_at_50%_40%,rgb(15_118_110/0.14),transparent_65%)]" aria-hidden="true"></div>

                    <span class="pointer-events-none absolute -left-2 -top-2 size-12 rounded-tl-2xl border-l-[3px] border-t-[3px] border-amber-400" aria-hidden="true"></span>
                    <span class="pointer-events-none absolute -bottom-2 -right-2 size-12 rounded-br-2xl border-b-[3px] border-r-[3px] border-teal-deep" aria-hidden="true"></span>

                    <img src="/images/404/404-students2.png"
                         alt="Ilustrasi halaman tidak ditemukan"
                         class="relative h-auto w-full select-none drop-shadow-[0_20px_35px_rgb(15_118_110/0.25)]"
                         loading="eager"
                         decoding="async">
                </div>
            </div>
        </div>
    </section>
@endsection
