<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#042f2e">

    {{-- Terapkan tema tersimpan sebelum CSS dimuat supaya tidak berkedip --}}
    <script>
        (function () {
            var theme = 'light';
            try {
                var saved = localStorage.getItem('mts-admin-theme');
                if (saved === 'dark' || saved === 'light') {
                    theme = saved;
                } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    theme = 'dark';
                }
            } catch (e) {}
            var root = document.documentElement;
            root.dataset.theme = theme;
            root.style.colorScheme = theme;
            var meta = document.querySelector('meta[name="theme-color"]');
            if (meta) meta.content = theme === 'dark' ? '#061918' : '#042f2e';
        })();
    </script>

    <title>@yield('title', 'Dashboard') · Panel Admin MTs NU Al Syairiyah</title>

    <link rel="icon" type="image/png" sizes="64x64" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/icon-512.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    {{ Vite::fonts() }}
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
    @livewireStyles
</head>
<body class="admin-app min-h-full bg-paper font-sans text-ink antialiased">
    <div
        x-data="{
            sidebarOpen: false,
            sidebarCollapsed: (localStorage.getItem('mts-admin-sidebar') === 'collapsed'),
            toggleCollapsed() {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('mts-admin-sidebar', this.sidebarCollapsed ? 'collapsed' : 'expanded');
            },
            theme: document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light',
            toggleTheme() {
                this.theme = this.theme === 'dark' ? 'light' : 'dark';
                const root = document.documentElement;
                root.dataset.theme = this.theme;
                root.style.colorScheme = this.theme;
                try { localStorage.setItem('mts-admin-theme', this.theme); } catch (e) {}
                const meta = document.querySelector('meta[name=\'theme-color\']');
                if (meta) meta.content = this.theme === 'dark' ? '#061918' : '#042f2e';
            },
        }"
        class="min-h-screen"
    >

        {{-- Overlay sidebar (layar sempit) --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             x-transition.opacity
             class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm lg:hidden"></div>

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-white/10 bg-teal-950 text-white transition-all duration-200 ease-in-out lg:translate-x-0"
            :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'lg:w-[4.75rem]' : 'lg:w-64']"
        >
            <div class="flex items-center gap-3 border-b border-white/10 px-4 py-5" :class="sidebarCollapsed && 'lg:justify-center lg:px-0'">
                <a href="{{ route('admin.dashboard') }}" class="flex shrink-0 items-center justify-center" title="Dashboard">
                    <img src="/images/logo.png" alt="Logo MTs NU Al Syairiyah" width="44" height="41"
                         class="h-10 w-auto drop-shadow-sm" decoding="async">
                </a>
                <div class="min-w-0 leading-tight" :class="sidebarCollapsed && 'lg:hidden'">
                    <p class="truncate font-display text-sm font-semibold">MTs NU Al Syairiyah</p>
                    <p class="text-xs text-teal-300">Panel Admin</p>
                </div>
            </div>

            @php
                $menu = [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'home', 'module' => null],
                    ['route' => 'admin.posts.berita', 'label' => 'Berita', 'icon' => 'news', 'module' => 'berita'],
                    ['route' => 'admin.posts.artikel', 'label' => 'Artikel', 'icon' => 'article', 'module' => 'artikel'],
                    ['route' => 'admin.galleries.index', 'label' => 'Galeri', 'icon' => 'photo', 'module' => 'galeri'],
                    ['route' => 'admin.achievements.index', 'label' => 'Prestasi', 'icon' => 'trophy', 'module' => 'prestasi'],
                    ['route' => 'admin.statistics.index', 'label' => 'Statistik Madrasah', 'icon' => 'chart', 'module' => 'statistik'],
                ];

                // Tampilkan hanya menu yang boleh diakses akun ini.
                $menu = array_filter(
                    $menu,
                    fn (array $item): bool => $item['module'] === null || auth()->user()->canAccessModule($item['module']),
                );
            @endphp

            <nav class="flex-1 space-y-1 overflow-y-auto overflow-x-hidden px-3 py-4">
                @foreach ($menu as $item)
                    @php($active = request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])))
                    <a href="{{ route($item['route']) }}"
                       title="{{ $item['label'] }}"
                       class="group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ $active ? 'bg-white/10 font-semibold text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                       :class="sidebarCollapsed && 'lg:justify-center lg:px-0'">
                        @if ($active)
                            <span class="absolute inset-y-1.5 left-0 w-0.5 rounded-full bg-gold"></span>
                        @endif
                        <x-admin.icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                        <span class="truncate" :class="sidebarCollapsed && 'lg:hidden'">{{ $item['label'] }}</span>
                        @if ($active)
                            <span class="ml-auto h-1.5 w-1.5 shrink-0 rounded-full bg-gold" :class="sidebarCollapsed && 'lg:hidden'"></span>
                        @endif

                        {{-- Tooltip saat sidebar diperkecil --}}
                        <span x-show="sidebarCollapsed" x-cloak
                              class="pointer-events-none absolute left-full top-1/2 z-50 ml-3 hidden -translate-y-1/2 whitespace-nowrap rounded-md bg-ink px-2.5 py-1.5 text-xs font-medium text-paper opacity-0 shadow-float transition-opacity duration-150 group-hover:opacity-100 lg:block">
                            {{ $item['label'] }}
                        </span>
                    </a>
                @endforeach

                @can('manage-admins')
                    <div class="my-3 border-t border-white/10"></div>
                    <a href="{{ route('admin.manajemen-admin.index') }}"
                       title="Manajemen Admin"
                       class="group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('admin.manajemen-admin.*') ? 'bg-white/10 font-semibold text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                       :class="sidebarCollapsed && 'lg:justify-center lg:px-0'">
                        <x-admin.icon name="shield" class="h-5 w-5 shrink-0" />
                        <span class="truncate" :class="sidebarCollapsed && 'lg:hidden'">Manajemen Admin</span>
                        <span class="ml-auto inline-flex shrink-0 items-center gap-1 rounded-full bg-gold/20 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gold" :class="sidebarCollapsed && 'lg:hidden'">
                            <x-admin.icon name="star" class="h-3 w-3" />
                            Super
                        </span>

                        <span x-show="sidebarCollapsed" x-cloak
                              class="pointer-events-none absolute left-full top-1/2 z-50 ml-3 hidden -translate-y-1/2 whitespace-nowrap rounded-md bg-ink px-2.5 py-1.5 text-xs font-medium text-paper opacity-0 shadow-float transition-opacity duration-150 group-hover:opacity-100 lg:block">
                            Manajemen Admin
                        </span>
                    </a>
                @endcan
            </nav>


            <div class="border-t border-white/10 p-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout"
                            class="group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-white/80 transition hover:bg-white/10 hover:text-white"
                            :class="sidebarCollapsed && 'lg:justify-center lg:px-0'">
                        <x-admin.icon name="logout" class="h-5 w-5 shrink-0" />
                        <span :class="sidebarCollapsed && 'lg:hidden'">Logout</span>

                        <span x-show="sidebarCollapsed" x-cloak
                              class="pointer-events-none absolute left-full top-1/2 z-50 ml-3 hidden -translate-y-1/2 whitespace-nowrap rounded-md bg-ink px-2.5 py-1.5 text-xs font-medium text-paper opacity-0 shadow-float transition-opacity duration-150 group-hover:opacity-100 lg:block">
                            Logout
                        </span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Area konten --}}
        <div class="flex min-h-screen flex-col transition-[padding] duration-200 ease-in-out lg:pl-64" :class="sidebarCollapsed && 'lg:pl-[4.75rem]'">
            <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-line bg-bone/90 px-4 shadow-sm backdrop-blur sm:px-6">
                <button type="button" @click="sidebarOpen = true"
                        class="rounded-lg border border-line p-2 text-ink transition hover:bg-paper lg:hidden">
                    <x-admin.icon name="menu" class="h-6 w-6" />
                    <span class="sr-only">Buka menu</span>
                </button>

                <button type="button" @click="toggleCollapsed()"
                        class="hidden rounded-lg border border-line p-2 text-ink transition hover:bg-paper lg:block"
                        :title="sidebarCollapsed ? 'Perlebar sidebar' : 'Perkecil sidebar'"
                        :aria-label="sidebarCollapsed ? 'Perlebar sidebar' : 'Perkecil sidebar'"
                        :aria-expanded="(!sidebarCollapsed).toString()">
                    <x-admin.icon name="panel-left" class="h-5 w-5" />
                </button>

                <h1 class="truncate font-display text-lg font-semibold">@yield('title', 'Dashboard')</h1>

                <div class="ml-auto flex items-center gap-1 sm:gap-2">
                {{-- Ganti tema terang/gelap --}}
                <button type="button" @click="toggleTheme()"
                        class="rounded-lg border border-line p-2 text-ink transition hover:bg-paper"
                        :title="theme === 'dark' ? 'Ganti ke tema terang' : 'Ganti ke tema gelap'"
                        :aria-label="theme === 'dark' ? 'Ganti ke tema terang' : 'Ganti ke tema gelap'"
                        :aria-pressed="(theme === 'dark').toString()">
                    <span x-show="theme !== 'dark'"><x-admin.icon name="moon" class="h-5 w-5" /></span>
                    <span x-show="theme === 'dark'" x-cloak><x-admin.icon name="sun" class="h-5 w-5" /></span>
                </button>

                <div x-data="{ open: false }" class="relative">
                    <button type="button" @click="open = !open"
                            class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm transition hover:bg-paper">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand text-xs font-semibold text-on-brand ring-2 ring-teal-bright/40">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="hidden text-left sm:block">
                            <span class="block text-sm font-medium leading-tight">{{ auth()->user()->name }}</span>
                            <span class="block text-xs leading-tight text-ink-soft">
                                {{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
                            </span>
                        </span>
                        <span class="transition" :class="open && 'rotate-180'">
                            <x-admin.icon name="chevron-down" class="h-4 w-4 text-ink-soft" />
                        </span>
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute right-0 mt-2 w-48 overflow-hidden rounded-xl border border-line bg-bone shadow-float">
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-2 px-4 py-2.5 text-sm transition hover:bg-paper">
                            <x-admin.icon name="user" class="h-4 w-4 text-ink-soft" />
                            Profil Saya
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm text-danger transition hover:bg-paper">
                                <x-admin.icon name="logout" class="h-4 w-4" />
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </main>

            <footer class="border-t border-line px-4 py-4 text-xs text-ink-soft sm:px-6 lg:px-8">
                &copy; {{ date('Y') }} MTs NU Al Syairiyah · Panel Admin
            </footer>
        </div>
    </div>

    <x-admin.toast />

    @livewireScripts
</body>
</html>
