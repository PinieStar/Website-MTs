@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">

        <div class="relative overflow-hidden rounded-2xl bg-teal-950 p-6 text-white shadow-float sm:p-8">
            <div class="pointer-events-none absolute inset-0 opacity-[0.06]"
                 style="background-image: linear-gradient(to right, #ffffff 1px, transparent 1px), linear-gradient(to bottom, #ffffff 1px, transparent 1px); background-size: 40px 40px;"></div>
            <div class="pointer-events-none absolute -right-20 -top-24 h-64 w-64 rounded-full bg-teal-700/40 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 left-1/3 h-56 w-56 rounded-full bg-gold/15 blur-3xl"></div>

            <div class="relative">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gold">Ringkasan</p>
                <h2 class="mt-1 font-display text-2xl font-semibold sm:text-3xl">
                    Selamat datang, {{ auth()->user()->name }}
                </h2>
                <p class="mt-2 text-sm text-white/80">
                    Kelola konten website MTs NU Al Syairiyah dari panel ini.
                </p>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $stats = [
                    ['label' => 'Berita', 'value' => $jumlahBerita, 'route' => 'admin.posts.berita', 'icon' => 'news', 'module' => 'berita'],
                    ['label' => 'Artikel', 'value' => $jumlahArtikel, 'route' => 'admin.posts.artikel', 'icon' => 'article', 'module' => 'artikel'],
                    ['label' => 'Album Galeri', 'value' => $jumlahGaleri, 'route' => 'admin.galleries.index', 'icon' => 'photo', 'module' => 'galeri'],
                    ['label' => 'Prestasi', 'value' => $jumlahPrestasi, 'route' => 'admin.achievements.index', 'icon' => 'trophy', 'module' => 'prestasi'],
                ];

                // Hanya kartu untuk modul yang boleh diakses akun ini.
                $stats = array_filter($stats, fn (array $stat): bool => auth()->user()->canAccessModule($stat['module']));
            @endphp

            @foreach ($stats as $stat)
                <a href="{{ route($stat['route']) }}"
                   class="group flex flex-col gap-3 rounded-xl border border-line bg-bone p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-teal-bright hover:shadow-float">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand text-on-brand transition group-hover:bg-brand-hover">
                        <x-admin.icon :name="$stat['icon']" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="text-sm text-ink-soft">{{ $stat['label'] }}</p>
                        <p class="mt-1 font-display text-3xl font-semibold">{{ $stat['value'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        @if ($tipeBisaDiakses !== [])
        <div class="overflow-hidden rounded-xl border border-line bg-bone shadow-sm">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <h3 class="font-display text-base font-semibold">Draft Terbaru</h3>
                <a href="{{ route('admin.posts.'.$tipeBisaDiakses[0]) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-teal-bright transition hover:underline">
                    Lihat semua
                    <x-admin.icon name="chevron-down" class="h-3.5 w-3.5 -rotate-90" />
                </a>
            </div>

            @if ($draftTerbaru->isEmpty())
                <div class="px-5 py-12 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-paper text-ink-soft">
                        <x-admin.icon name="article" class="h-5 w-5" />
                    </span>
                    <p class="mt-3 text-sm text-ink-soft">
                        Belum ada draft. Draft yang Anda simpan akan muncul di sini.
                    </p>
                </div>
            @else
                <div class="hidden overflow-x-auto md:block">
                    <table class="min-w-full text-sm">
                        <thead class="bg-paper text-left text-xs uppercase tracking-wide text-ink-soft">
                            <tr>
                                <th class="px-5 py-3 font-medium">Judul</th>
                                <th class="px-5 py-3 font-medium">Jenis</th>
                                <th class="px-5 py-3 font-medium">Terakhir diubah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($draftTerbaru as $post)
                                <tr class="transition hover:bg-paper/60">
                                    <td class="px-5 py-3 font-medium">{{ $post->judul }}</td>
                                    <td class="px-5 py-3 capitalize text-ink-soft">
                                        <span class="inline-flex items-center rounded-full bg-paper px-2 py-0.5 text-xs font-medium text-ink-soft">
                                            {{ $post->type }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-ink-soft">{{ $post->updated_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Daftar kartu untuk layar HP --}}
                <div class="divide-y divide-line md:hidden">
                    @foreach ($draftTerbaru as $post)
                        <div class="flex items-start justify-between gap-3 px-4 py-3">
                            <div class="min-w-0">
                                <p class="font-medium leading-snug text-ink">{{ $post->judul }}</p>
                                <p class="mt-1 text-xs text-ink-soft">{{ $post->updated_at->diffForHumans() }}</p>
                            </div>
                            <span class="inline-flex shrink-0 items-center rounded-full bg-paper px-2 py-0.5 text-[11px] font-medium capitalize text-ink-soft">
                                {{ $post->type }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        @endif
    </div>
@endsection
