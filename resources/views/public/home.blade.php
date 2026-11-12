@extends('layouts.public')

@php
    $site = config('site');
    $hero = $site['hero'];
    $ppdb = $site['ppdb'];
@endphp

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden pb-16 pt-32 sm:pt-36 lg:pb-24 lg:pt-40" aria-labelledby="hero-title">
        <div class="pointer-events-none absolute -right-40 -top-32 size-[34rem] rounded-full bg-[radial-gradient(circle,rgb(15_118_110/0.14),transparent_65%)]"
             aria-hidden="true"
             data-parallax="0.08"></div>
        <div class="container-page relative">
            <div class="grid items-center gap-10 lg:grid-cols-12 lg:gap-8">
                <div class="lg:col-span-6 xl:col-span-6">
                    <p class="eyebrow" data-reveal="fade">
                        <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                        {{ $hero['eyebrow'] }}
                    </p>

                    <h1 id="hero-title" class="mt-6 max-w-[12ch] font-display text-hero font-extrabold text-teal-deep" data-reveal="lines">
                        {{ $hero['title'] }}
                    </h1>

                    <p class="measure mt-6 text-lead text-ink-soft" data-reveal="fade">
                        {{ $hero['subtitle'] }}
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-3" data-reveal="fade">
                        <x-public.button :href="route('public.profil')" size="lg">Jelajahi Madrasah</x-public.button>
                        <x-public.button :href="route('public.akademik')" variant="secondary" size="lg" icon="book-open">
                            Program Akademik
                        </x-public.button>
                    </div>

                    <div class="mt-10 flex flex-nowrap items-center gap-x-4 text-[0.62rem] font-semibold uppercase tracking-[0.06em] text-ink-soft/80 sm:gap-x-2.5" data-reveal="fade">
                        <a href="{{ $site['instagram_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram {{ $site['instagram_handle'] }}" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-teal-bright transition-colors hover:text-teal-deep">
                            <x-public.icon name="instagram" :size="13" />
                            <span class="hidden sm:inline">{{ $site['instagram_handle'] }}</span>
                        </a>
                        <span class="hidden h-3 w-px shrink-0 bg-line sm:block" aria-hidden="true"></span>
                        <a href="{{ $site['youtube_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube {{ $site['youtube_handle'] }}" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-teal-bright transition-colors hover:text-teal-deep">
                            <x-public.icon name="youtube" :size="13" />
                            <span class="hidden sm:inline">{{ $site['youtube_handle'] }}</span>
                        </a>
                        <span class="hidden h-3 w-px shrink-0 bg-line sm:block" aria-hidden="true"></span>
                        <a href="{{ $site['tiktok_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="TikTok {{ $site['tiktok_handle'] }}" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-teal-bright transition-colors hover:text-teal-deep">
                            <x-public.icon name="tiktok" :size="13" />
                            <span class="hidden sm:inline">{{ $site['tiktok_handle'] }}</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-6" data-reveal="fade">
                    <div class="relative mx-auto max-w-[30rem] lg:ml-auto">
                        <div class="pointer-events-none absolute -bottom-5 -left-5 hidden h-40 w-28 rounded-bento border border-gold/40 sm:block" aria-hidden="true"></div>

                        <div class="relative h-[20rem] w-full overflow-hidden rounded-bento bg-teal-deep shadow-lift sm:h-[24rem] lg:h-[26rem]" data-arch>
                            <img src="/images/gedung.jpg"
                                 alt="Gedung MTs NU Al Syairiyah"
                                 loading="eager"
                                 decoding="async"
                                 class="absolute inset-0 h-full w-full object-cover object-center">

                            <div class="absolute inset-0 bg-gradient-to-t from-teal-deep/90 via-teal-deep/35 to-teal-deep/10" aria-hidden="true"></div>
                            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgb(15_118_110/0.35),transparent_58%)]" aria-hidden="true"></div>

                            {{-- Logo badge --}}
                            <div class="absolute right-5 top-5 z-10 sm:left-7 sm:top-7">
                                <img src="/images/logo.png"
                                     alt="Logo MTs NU Al Syairiyah"
                                     width="100"
                                     height="100"
                                     class="h-16 w-auto object-contain drop-shadow-[0_6px_18px_rgb(0_0_0/0.4)] sm:h-20">
                            </div>

                            <div class="relative flex h-full flex-col justify-end p-7 sm:p-9">
                                <div class="relative">
                                    <p class="lg:text-right text-center font-display text-4xl font-extrabold leading-[0.95] tracking-[-0.04em] text-paper drop-shadow-[0_2px_12px_rgb(0_0_0/0.35)]">
                                        Madrasah Tsanawiyah NU Al Syairiyah
                                    </p>
                                </div>

                                <div class="pointer-events-none absolute -right-12 top-1/2 size-48 -translate-y-1/2 rounded-full border border-gold/25" aria-hidden="true"></div>
                                <div class="pointer-events-none absolute -right-4 top-1/2 size-24 -translate-y-1/2 rounded-full border border-gold/20" aria-hidden="true"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Quick links --}}
    <x-public.section class="pt-2 lg:pt-4" labelledby="quick-links-title">
        <div class="flex flex-col gap-8">
            <div class="flex flex-wrap items-end justify-between gap-4" data-reveal="fade">
                <div>
                    <p class="eyebrow">
                        <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                        Jelajahi
                    </p>
                    <h2 id="quick-links-title" class="mt-4 max-w-2xl font-display text-title font-bold text-teal-deep">
                        Satu madrasah, banyak jalan untuk bertumbuh.
                    </h2>
                </div>
                <p class="max-w-xs text-sm text-ink-soft">
                    Mulai dari arah pendidikan, kegiatan, kabar terbaru, hingga potret keseharian madrasah.
                </p>
            </div>

            <div class="bento-grid lg:auto-rows-[minmax(10.5rem,auto)]" data-stagger>
                <x-public.bento-link :href="route('public.akademik')"
                                     title="Program Unggulan"
                                     eyebrow="Akademik"
                                     description="Arah pendidikan, penguatan keislaman, dan program yang mendukung proses belajar siswa."
                                     icon="book-open"
                                     tone="dark"
                                     class="col-span-2 min-h-[19rem] md:col-span-3 lg:col-span-5 lg:row-span-2" />

                <x-public.bento-link :href="route('public.akademik').'#ekstrakurikuler'"
                                     title="Kegiatan Sekolah"
                                     eyebrow="Ekstrakurikuler"
                                     description="Ruang berkarya dan berorganisasi di luar jam belajar."
                                     icon="sparkles"
                                     tone="mint"
                                     class="col-span-2 md:col-span-3 lg:col-span-4" />

                <x-public.bento-link :href="route('public.berita.index')"
                                     title="Berita & Pengumuman"
                                     eyebrow="Informasi"
                                     description="Kabar terbaru madrasah, langsung dari kanal resmi."
                                     icon="message-circle"
                                     tone="light"
                                     class="col-span-2 md:col-span-3 lg:col-span-3" />

                <x-public.bento-link :href="route('public.galeri.index')"
                                     title="Galeri Madrasah"
                                     eyebrow="Dokumentasi"
                                     description="Potret kegiatan dan keseharian madrasah."
                                     icon="camera"
                                     tone="light"
                                     class="col-span-2 md:col-span-3 lg:col-span-7" />
            </div>
        </div>
    </x-public.section>

    {{-- Statistics --}}
    <x-public.section class="pt-0" labelledby="statistik-title">
        <div class="rounded-bento bg-teal-deep p-6 text-paper sm:p-8 lg:p-10">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-xl">
                    <p class="eyebrow text-gold-soft">
                        <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                        Madrasah dalam angka
                    </p>
                    <h2 id="statistik-title" class="mt-4 font-display text-title font-bold text-paper" data-reveal="lines">
                        Yang belajar, yang mendampingi.
                    </h2>
                </div>
                <p class="max-w-sm text-sm leading-relaxed text-paper/65">
                    Angka-angka ini memberi gambaran tentang siapa yang belajar dan mendampingi di madrasah.
                </p>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-3 lg:grid-cols-4" data-stagger>
                @foreach ($statistics as $statistic)
                    <x-public.stat-card
                        :label="$statistic->label"
                        :value="$statistic->value"
                        :icon="$statistic->icon"
                        class="min-h-[10.5rem]" />
                @endforeach
            </div>
        </div>
    </x-public.section>

    {{-- PPDB CTA --}}
    <x-public.section class="pt-0" labelledby="ppdb-title">
        <div class="relative overflow-hidden rounded-bento bg-teal-deep p-7 text-paper sm:p-10 lg:p-14">
            <x-public.pattern variant="arch" class="text-paper" :opacity="0.07" />
            <div class="pointer-events-none absolute -right-16 -top-20 size-72 rounded-full border border-gold/20" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-24 right-16 hidden size-80 rounded-t-full border border-paper/10 lg:block" aria-hidden="true"></div>

            <div class="relative grid gap-10 lg:grid-cols-12 lg:items-end">
                <div class="lg:col-span-7">
                    <p class="eyebrow text-gold-soft">
                        <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                        Penerimaan Peserta Didik Baru
                    </p>
                    <h2 id="ppdb-title" class="mt-5 max-w-xl font-display text-display font-bold text-paper" data-reveal="lines">
                        Langkah berikutnya dimulai dari sini.
                    </h2>
                    <p class="measure mt-5 text-paper/65">
                        Informasi lengkap mengenai jadwal, persyaratan, dan alur pendaftaran tersedia di situs PPDB resmi madrasah.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <x-public.button :href="$site['ppdb_url']" variant="gold" size="lg" icon="arrow-up-right">
                            Daftar Sekarang
                        </x-public.button>
                        <span class="text-xs text-paper/65">Membuka situs PPDB di tab baru.</span>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="rounded-2xl border border-paper/15 bg-paper/5 p-5 backdrop-blur-sm">
                        <p class="text-[0.62rem] font-bold uppercase tracking-[0.2em] text-paper/60">Jadwal PPDB</p>
                        <p class="mt-3 font-display text-2xl font-bold leading-snug text-gold-soft">
                            {{ $ppdb['schedule'] ?? 'Belum ada jadwal' }}
                        </p>
                        <p class="mt-2 text-sm leading-relaxed text-paper/65">
                            {{ $ppdb['note'] ?? 'Jadwal resmi akan diperbarui di situs PPDB. Silakan buka tautan untuk informasi terkini.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </x-public.section>
@endsection
