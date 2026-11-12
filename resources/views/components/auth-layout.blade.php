@props(['title' => 'Masuk'])

<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">

        <title>{{ $title }} · Panel Admin MTs NU Al Syairiyah</title>

        {{ Vite::fonts() }}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full bg-paper font-sans text-ink antialiased">
        <main class="flex min-h-screen flex-col lg:grid lg:grid-cols-[1.05fr_1fr]">

            {{-- Panel brand (desktop) --}}
            <aside class="relative hidden overflow-hidden bg-teal-950 px-10 py-10 text-paper lg:flex lg:flex-col xl:px-14 xl:py-12">
                {{-- Dekorasi latar --}}
                <div class="pointer-events-none absolute inset-0 opacity-[0.06]"
                     style="background-image: linear-gradient(to right, #ffffff 1px, transparent 1px), linear-gradient(to bottom, #ffffff 1px, transparent 1px); background-size: 48px 48px;"></div>
                <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-teal-700/40 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-gold/15 blur-3xl"></div>
                <img src="{{ asset('images/logo.png') }}" alt="" aria-hidden="true"
                     class="pointer-events-none absolute -bottom-16 -right-16 w-72 opacity-[0.07]">

                <div class="relative z-10 flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-bone shadow-float">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo MTs NU Al Syairiyah"
                             class="h-9 w-9 object-contain">
                    </span>
                    <div class="leading-tight">
                        <p class="font-display text-base font-semibold">MTs NU Al Syairiyah</p>
                        <p class="mt-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-teal-300">
                            Panel Admin
                        </p>
                    </div>
                </div>

                <div class="relative z-10 my-auto max-w-xl py-16">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-gold">
                        <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
                        Ruang kerja admin
                    </span>

                    <h1 class="mt-6 font-display text-4xl font-bold leading-[1.1] tracking-tight xl:text-5xl">
                        Kelola informasi madrasah
                        <span class="text-gold">dalam satu panel.</span>
                    </h1>

                    <p class="mt-5 max-w-md text-sm leading-relaxed text-paper/70 xl:text-base">
                        Tulis berita, kelola galeri, dan perbarui prestasi siswa tanpa perlu
                        mengurus hal teknis.
                    </p>

                    <ul class="mt-8 space-y-3.5 text-sm text-paper/85">
                        @foreach ([
                            'Tulis dan terbitkan berita serta artikel madrasah.',
                            'Kelola album galeri dan dokumentasi kegiatan.',
                            'Perbarui prestasi siswa dengan cepat.',
                        ] as $item)
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-teal-700/60 text-teal-300">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="m5 12 5 5L20 7" />
                                    </svg>
                                </span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="relative z-10 flex items-center justify-between gap-4 border-t border-white/10 pt-6 text-xs text-paper/50">
                    <p>&copy; {{ date('Y') }} MTs NU Al Syairiyah</p>
                    <p class="inline-flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                        </svg>
                        Area terbatas
                    </p>
                </div>
            </aside>

            {{-- Panel formulir --}}
            <section class="relative flex min-h-screen flex-col bg-paper">
                <div class="flex items-center justify-between gap-4 px-5 py-5 sm:px-8">
                    <div class="flex items-center gap-2.5 lg:hidden">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-bone shadow-float ring-1 ring-line">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo MTs NU Al Syairiyah"
                                 class="h-7 w-7 object-contain">
                        </span>
                        <div class="leading-tight">
                            <p class="font-display text-sm font-semibold text-teal-950">MTs NU Al Syairiyah</p>
                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-ink-soft">Panel Admin</p>
                        </div>
                    </div>

                    <a href="{{ route('public.home') }}"
                       class="ml-auto inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold text-ink-soft transition hover:bg-bone hover:text-teal-900 focus:outline-none focus:ring-2 focus:ring-teal-700 focus:ring-offset-2 focus:ring-offset-paper">
                        Kembali ke situs
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M7 17 17 7" />
                            <path d="M7 7h10v10" />
                        </svg>
                    </a>
                </div>

                <div class="flex flex-1 items-center justify-center px-5 pb-10 pt-2 sm:px-8">
                    <div class="w-full max-w-[26rem]">
                        {{ $slot }}
                    </div>
                </div>

                <p class="px-5 pb-6 text-center text-[11px] text-ink-soft/80 sm:px-8 lg:text-right">
                    Akses khusus pengelola madrasah.
                </p>
            </section>
        </main>
    </body>
</html>
