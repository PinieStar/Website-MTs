@php
    $site = config('site');
    $primaryNav = $site['navigation']['primary'];

    $isItemActive = function (array $item): bool {
        foreach ($item['active'] ?? [] as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    };

    $isGroupActive = function (array $item) use ($isItemActive): bool {
        if ($isItemActive($item)) {
            return true;
        }

        foreach ($item['children'] ?? [] as $child) {
            if ($isItemActive($child)) {
                return true;
            }
        }

        return false;
    };
@endphp

<header class="site-header" data-site-header>
    <div class="container-page">
        <div class="nav-shell" data-nav-shell>
            <x-public.brand :size="38" class="nav-brand shrink-0" />

            <nav class="ml-2 hidden flex-1 items-center justify-center lg:flex" aria-label="Navigasi utama">
                <ul class="nav-list" data-nav-list>
                    @foreach ($primaryNav as $item)
                        @php($groupActive = $isGroupActive($item))
                        <li @if (! empty($item['children'])) class="relative" @endif data-nav-item>
                            @if (! empty($item['children']))
                                <a href="{{ route($item['route']) }}"
                                   class="nav-link"
                                   @if ($groupActive) aria-current="page" @endif
                                   aria-haspopup="true"
                                   aria-expanded="false"
                                   @if ($groupActive) data-pill="true" @endif
                                   data-dropdown-trigger
                                   data-nav-link>
                                    {{ $item['label'] }}
                                    <x-public.icon name="chevron-down" :size="14" class="transition-transform duration-200" />
                                </a>
                                <div class="nav-dropdown" data-dropdown-panel data-open="false">
                                    @foreach ($item['children'] as $child)
                                        <a href="{{ route($child['route']) }}"
                                           @if ($isItemActive($child)) aria-current="page" @endif>
                                            <span>{{ $child['label'] }}</span>
                                            <x-public.icon name="arrow-up-right" :size="14" />
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <a href="{{ route($item['route']) }}"
                                   class="nav-link"
                                   @if ($isItemActive($item)) aria-current="page" @endif
                                   @if ($isItemActive($item)) data-pill="true" @endif
                                   data-nav-link>
                                    {{ $item['label'] }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="ml-auto flex items-center gap-2 lg:ml-0">
                <x-public.theme-toggle class="hidden size-10 border border-line bg-bone text-teal-deep hover:bg-mint sm:inline-flex" />

                <x-public.button :href="$site['ppdb_url']"
                                 size="sm"
                                 variant="primary"
                                 icon="arrow-up-right"
                                 class="px-3 sm:px-4"
                                 ariaLabel="Buka pendaftaran peserta didik baru di tab baru"
                                 data-magnetic="true">
                    PPDB
                </x-public.button>

                <button type="button"
                        class="inline-flex size-10 items-center justify-center rounded-full border border-line bg-bone text-teal-deep transition-colors hover:bg-mint lg:hidden"
                        aria-label="Buka menu navigasi"
                        aria-expanded="false"
                        aria-controls="mobile-menu"
                        data-menu-open>
                    <x-public.icon name="menu" :size="20" />
                </button>
            </div>
        </div>
    </div>
</header>

<div id="mobile-menu"
     class="mobile-panel lg:hidden"
     data-mobile-panel
     data-open="false"
     aria-hidden="true"
     inert>
    <x-public.pattern variant="star" class="text-paper" :opacity="0.05" />

    <div class="relative flex items-center justify-between">
        <x-public.brand theme="dark" :size="42" loading="eager" />
        <div class="flex items-center gap-2">
            <x-public.theme-toggle class="inline-flex size-11 border border-paper/20 text-paper hover:bg-paper/10" />
            <button type="button"
                    class="inline-flex size-11 items-center justify-center rounded-full border border-paper/20 text-paper transition-colors hover:bg-paper/10"
                    aria-label="Tutup menu navigasi"
                    data-menu-close>
                <x-public.icon name="x" :size="22" />
            </button>
        </div>
    </div>

    <div class="relative mt-8 flex-1 overflow-y-auto overscroll-contain">
        <p class="eyebrow text-paper/65">Navigasi</p>
        <ul class="mt-4" data-mobile-links>
            @foreach ($primaryNav as $index => $item)
                @php($groupActive = $isGroupActive($item))
                <li>
                    @if (! empty($item['children']))
                        <button type="button"
                                class="mobile-panel__link mobile-panel__toggle"
                                aria-expanded="{{ $groupActive ? 'true' : 'false' }}"
                                aria-controls="mobile-submenu-{{ $index }}"
                                data-mobile-toggle
                                @if ($groupActive) data-active="true" @endif>
                            <span>{{ $item['label'] }}</span>
                            <span class="mobile-panel__meta">
                                <small>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</small>
                                <x-public.icon name="chevron-down" :size="18" class="mobile-panel__chevron" />
                            </span>
                        </button>
                        <div id="mobile-submenu-{{ $index }}"
                             class="mobile-panel__subpanel"
                             data-mobile-subpanel
                             data-open="{{ $groupActive ? 'true' : 'false' }}">
                            <div>
                                <div class="mobile-panel__sublist">
                                    @foreach ($item['children'] as $child)
                                        <a href="{{ route($child['route']) }}"
                                           class="mobile-panel__sub"
                                           @if ($isItemActive($child)) aria-current="page" @endif
                                           data-mobile-link>
                                            {{ $child['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route($item['route']) }}"
                           class="mobile-panel__link"
                           @if ($isItemActive($item)) aria-current="page" @endif
                           data-mobile-link>
                            <span>{{ $item['label'] }}</span>
                            <small>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</small>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    <div class="relative mt-8 border-t border-paper/15 pt-6">
        <p class="text-sm text-paper/60">{{ $site['tagline'] }}</p>
        <div class="mt-4 flex items-center justify-between gap-4">
            <a href="{{ $site['instagram_url'] }}"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center gap-2 text-sm font-semibold text-paper">
                <x-public.icon name="instagram" :size="18" />
                {{ $site['instagram_handle'] }}
            </a>
            <span class="text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-paper/65">Limpung</span>
        </div>
    </div>
</div>
