@extends('layouts.public')

@section('title', 'Kontak, Alamat & Lokasi')
@section('meta_description', 'Kontak MTs NU Al Syairiyah: Jl. Raya Limpung - Tersono No.Km. 1,5, Plumbon, Kec. Limpung, Kabupaten Batang, Jawa Tengah 51271. Telepon/WhatsApp +62 857-8638-8418, email mtsnualsyairiyahlimpung@gmail.com.')

@php
    $site = config('site');
    $contact = $site['contact'];

    $phoneHref = filled($contact['phone']) ? 'tel:'.preg_replace('/[^+\d]/', '', (string) $contact['phone']) : null;
    $emailHref = filled($contact['email']) ? 'mailto:'.$contact['email'] : null;

    $contactRows = [
        [
            'icon' => 'map-pin',
            'label' => 'Alamat',
            'value' => $contact['address'],
            'href' => $contact['maps_url'] ?? null,
            'hint' => 'Buka lokasi di Google Maps',
        ],
        [
            'icon' => 'phone',
            'label' => 'Telepon',
            'value' => $contact['phone'],
            'href' => $phoneHref,
            'hint' => 'Hubungi melalui telepon',
        ],
        [
            'icon' => 'mail',
            'label' => 'Email',
            'value' => $contact['email'],
            'href' => $emailHref,
            'hint' => 'Kirim surel',
        ],
        [
            'icon' => 'clock',
            'label' => 'Jam Layanan',
            'value' => $contact['hours'],
            'href' => null,
            'hint' => '',
        ],
    ];

    $socials = [
        ['key' => 'instagram', 'label' => 'Instagram', 'href' => $site['social']['instagram'] ?? null],
        ['key' => 'youtube', 'label' => 'YouTube', 'href' => $site['social']['youtube'] ?? null],
        ['key' => 'tiktok', 'label' => 'TikTok', 'href' => $site['social']['tiktok'] ?? null],
    ];
    $socials = array_values(array_filter($socials, fn ($item) => filled($item['href'])));

    $whatsappMessage = rawurlencode('Assalamualaikum, saya ingin bertanya tentang MTs NU Al Syairiyah.');
    $whatsappHref = filled($contact['whatsapp'])
        ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', (string) $contact['whatsapp']).'?text='.$whatsappMessage
        : null;
@endphp

@section('content')
    <x-public.page-hero eyebrow="Hubungi Kami"
                         title="Kami Siap Membantu"
                         lead="Jika ada pertanyaan atau informasi lebih lanjut, jangan ragu untuk menghubungi kami." />

    <x-public.section class="pt-4 lg:pt-6" labelledby="kontak-title">
        <div id="kontak" class="grid gap-5 lg:grid-cols-12">
            <div class="min-w-0 lg:col-span-7">
                <div class="bento p-7 sm:p-9" data-reveal="fade">
                    <p class="eyebrow">
                        <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                        Informasi Kontak
                    </p>
                    <h2 id="kontak-title" class="mt-4 font-display text-title font-bold text-teal-deep">
                        Terhubung dengan madrasah.
                    </h2>

                    <ul class="mt-8 divide-y divide-line-soft">
                        @foreach ($contactRows as $row)
                            <li class="py-4 first:pt-0 last:pb-0">
                                @if ($row['href'])
                                    <a href="{{ $row['href'] }}"
                                       @if (str_starts_with($row['href'], 'http')) target="_blank" rel="noopener noreferrer" @endif
                                       class="group flex items-start gap-4 rounded-xl transition-colors hover:text-teal-deep"
                                       aria-label="{{ $row['hint'] ?: $row['label'] }}">
                                        <span class="mt-0.5 grid size-11 shrink-0 place-items-center rounded-full bg-mint text-teal-bright">
                                            <x-public.icon :name="$row['icon']" :size="19" />
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block text-[0.62rem] font-bold uppercase tracking-[0.16em] text-ink-soft">{{ $row['label'] }}</span>
                                            <span class="mt-1 block wrap-anywhere text-[0.95rem] font-semibold text-teal-deep">{{ $row['value'] ?? 'Belum tersedia' }}</span>
                                        </span>
                                        <x-public.icon name="arrow-up-right" :size="16" class="ml-auto mt-3 shrink-0 text-ink-soft transition-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5" />
                                    </a>
                                @else
                                    <div class="flex items-start gap-4">
                                        <span class="mt-0.5 grid size-11 shrink-0 place-items-center rounded-full bg-mint text-teal-bright">
                                            <x-public.icon :name="$row['icon']" :size="19" />
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block text-[0.62rem] font-bold uppercase tracking-[0.16em] text-ink-soft">{{ $row['label'] }}</span>
                                            <span class="mt-1 block wrap-anywhere text-[0.95rem] font-semibold text-teal-deep">{{ $row['value'] ?? 'Belum tersedia' }}</span>
                                        </span>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="flex min-w-0 flex-col gap-5 lg:col-span-5">
                <div class="bento bento-dark relative overflow-hidden p-7 sm:p-8" data-reveal="fade">
                    <x-public.pattern variant="star" class="text-paper" :opacity="0.08" />
                    <div class="relative">
                        <p class="text-[0.6rem] font-bold uppercase tracking-[0.2em] text-paper/60">Media Sosial</p>
                        <h2 class="mt-3 font-display text-[clamp(1.7rem,2.6vw,2.2rem)] font-bold leading-tight text-paper">
                            Ikuti Kami
                        </h2>
                        <p class="mt-3 text-sm leading-relaxed text-paper/60">
                            Kabar harian, dokumentasi kegiatan, dan pengumuman juga tersedia di kanal resmi madrasah.
                        </p>

                        <div class="mt-6 flex flex-wrap gap-2">
                            @foreach ($socials as $social)
                                <a href="{{ $social['href'] }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex min-h-10 items-center gap-2 rounded-pill border border-paper/20 px-3.5 text-xs font-semibold text-paper transition-colors hover:border-gold hover:text-gold"
                                   aria-label="{{ $social['label'] }}">
                                    <x-public.icon :name="$social['key']" :size="16" />
                                    {{ $social['label'] }}
                                </a>
                            @endforeach
                        </div>

                        <a href="{{ $site['instagram_url'] }}" target="_blank" rel="noopener noreferrer" class="text-link mt-6 text-gold">
                            {{ $site['instagram_handle'] }}
                            <x-public.icon name="arrow-up-right" :size="14" />
                        </a>
                    </div>
                </div>

                <div class="bento bento-mint p-7 sm:p-8" data-reveal="fade">
                    <span class="grid size-11 place-items-center rounded-full bg-teal-deep text-paper" aria-hidden="true">
                        <x-public.icon :name="$whatsappHref ? 'whatsapp' : 'message-circle'" :size="20" />
                    </span>
                    <h2 class="mt-5 font-display text-card font-bold text-teal-deep">Chat via WhatsApp</h2>
                    <p class="mt-2 text-sm leading-relaxed text-ink-soft">
                        @if ($whatsappHref)
                            Sampaikan pertanyaan Anda melalui WhatsApp dengan pesan pembuka yang sudah disiapkan.
                        @else
                            Nomor WhatsApp resmi madrasah belum tersedia.
                        @endif
                    </p>

                    @if ($whatsappHref)
                        <x-public.button :href="$whatsappHref" variant="primary" class="mt-5 w-full sm:w-auto" icon="arrow-up-right">
                            Mulai Percakapan
                        </x-public.button>
                    @else
                        <span class="mt-5 inline-flex min-h-11 items-center gap-2 rounded-pill border border-line bg-bone/60 px-5 text-sm font-semibold text-ink-soft">
                            <x-public.icon name="clock" :size="16" />
                            Menunggu nomor resmi
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </x-public.section>

    <x-public.section class="pt-0" labelledby="lokasi-title">
        <div class="bento overflow-hidden" data-reveal="fade">
            <div class="flex flex-col gap-4 border-b border-line p-6 sm:flex-row sm:items-end sm:justify-between sm:p-8">
                <div>
                    <p class="eyebrow">
                        <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                        Lokasi
                    </p>
                    <h2 id="lokasi-title" class="mt-4 font-display text-title font-bold text-teal-deep">
                        Temukan madrasah
                    </h2>
                </div>

                @if ($contact['maps_url'])
                    <x-public.button :href="$contact['maps_url']" variant="secondary" icon="arrow-up-right">
                        Petunjuk Arah
                    </x-public.button>
                @endif
            </div>

            <div class="relative min-h-[22rem] overflow-hidden bg-teal-deep sm:min-h-[26rem]">
                <x-public.pattern variant="grid" class="text-paper" :opacity="0.08" />

                @if ($contact['maps_embed_url'])
                    <iframe src="{{ $contact['maps_embed_url'] }}"
                            title="Peta lokasi {{ $site['name'] }}"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="absolute inset-0 h-full w-full border-0"></iframe>
                @else
                    <div class="relative flex min-h-[22rem] flex-col items-center justify-center gap-4 p-8 text-center sm:min-h-[26rem]">
                        <span class="grid size-14 place-items-center rounded-full border border-paper/20 text-gold">
                            <x-public.icon name="map-pin" :size="24" />
                        </span>
                        <p class="font-display text-2xl font-bold text-paper">Peta lokasi</p>
                        <p class="text-sm font-semibold text-gold-soft">Peta belum tersedia</p>
                        <p class="max-w-sm text-sm text-paper/65">
                            Tautan Google Maps dan alamat lengkap akan ditambahkan setelah data resmi tersedia.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </x-public.section>
@endsection
