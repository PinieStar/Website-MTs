@php
    $site = config('site');
    $footerNav = $site['navigation']['footer'];
    $year = date('Y');

    $contactItems = [
        ['icon' => 'map-pin', 'label' => 'Alamat', 'value' => $site['contact']['address']],
        ['icon' => 'phone', 'label' => 'Telepon', 'value' => $site['contact']['phone']],
        ['icon' => 'mail', 'label' => 'Email', 'value' => $site['contact']['email']],
    ];

    $socials = [
        ['key' => 'instagram', 'label' => 'Instagram', 'href' => $site['social']['instagram'] ?? null],
        ['key' => 'youtube', 'label' => 'YouTube', 'href' => $site['social']['youtube'] ?? null],
        ['key' => 'tiktok', 'label' => 'TikTok', 'href' => $site['social']['tiktok'] ?? null],
    ];
    $socials = array_values(array_filter($socials, fn ($item) => filled($item['href'])));
@endphp

<footer class="relative mt-6 overflow-hidden bg-teal-deep text-paper">
    <x-public.pattern variant="star" class="text-paper" :opacity="0.05" />

    <div class="pointer-events-none absolute -top-px left-1/2 h-24 w-[min(92%,54rem)] -translate-x-1/2 rounded-b-[100%] border-b border-x border-paper/10" aria-hidden="true"></div>

    <div class="container-page relative">
        <div class="grid gap-12 pb-14 pt-24 md:grid-cols-2 lg:grid-cols-12 lg:gap-10 lg:pt-28">
            <div class="lg:col-span-4">
                <x-public.brand theme="dark" :size="48" loading="lazy" />
                <p class="measure-tight mt-6 text-sm leading-relaxed text-paper/65">
                    {{ $site['tagline'] }}. Madrasah Tsanawiyah di {{ $site['place'] }} di bawah naungan {{ $site['affiliation'] }}.
                </p>
                <div class="mt-7 flex flex-wrap gap-2">
                    @foreach ($socials as $social)
                        <a href="{{ $social['href'] }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex size-10 items-center justify-center rounded-full border border-paper/15 text-paper/80 transition-colors hover:border-gold hover:text-gold"
                           aria-label="{{ $social['label'] }}">
                            <x-public.icon :name="$social['key']" :size="17" />
                        </a>
                    @endforeach
                </div>
            </div>

            <nav class="lg:col-span-3" aria-label="Tautan footer">
                <p class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-paper/60">Jelajahi</p>
                <ul class="mt-5 space-y-2.5">
                    @foreach ($footerNav as $item)
                        <li>
                            <a href="{{ route($item['route']) }}"
                               class="group inline-flex items-center gap-2 text-sm text-paper/75 transition-colors hover:text-paper">
                                <span class="h-px w-0 bg-gold transition-all duration-300 group-hover:w-3"></span>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="lg:col-span-3">
                <p class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-paper/60">Kontak</p>
                <ul class="mt-5 space-y-3.5">
                    @foreach ($contactItems as $item)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 grid size-8 shrink-0 place-items-center rounded-full border border-paper/10 text-gold">
                                <x-public.icon :name="$item['icon']" :size="15" />
                            </span>
                            <span class="min-w-0">
                                <span class="block text-[0.65rem] uppercase tracking-[0.12em] text-paper/60">{{ $item['label'] }}</span>
                                <span class="block break-words text-sm text-paper/80">{{ $item['value'] }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('public.kontak') }}" class="text-link mt-5 text-paper">
                    Lihat halaman kontak
                    <x-public.icon name="arrow-right" :size="15" />
                </a>
            </div>

            <div class="lg:col-span-2">
                <p class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-paper/60">Sosial</p>
                <a href="{{ $site['instagram_url'] }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="mt-5 block rounded-2xl border border-paper/15 p-4 transition-colors hover:border-gold/60 hover:bg-paper/5">
                    <x-public.icon name="instagram" :size="22" class="text-gold" />
                    <span class="mt-4 block text-sm font-semibold">{{ $site['instagram_handle'] }}</span>
                    <span class="mt-1 block text-xs text-paper/65">Kabar & kegiatan madrasah</span>
                </a>
            </div>
        </div>

        <div class="relative flex flex-col gap-3 border-t border-paper/10 py-6 text-xs text-paper/65 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ $year }} {{ $site['name'] }}. Hak cipta dilindungi.</p>
            <p class="inline-flex items-center gap-1.5">
                <span class="inline-block size-1.5 rounded-full bg-gold" aria-hidden="true"></span>
                Limpung, Indonesia
            </p>
        </div>
    </div>
</footer>
