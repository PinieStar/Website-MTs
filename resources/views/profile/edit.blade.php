@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">

        {{-- Ringkasan identitas --}}
        <section class="relative overflow-hidden rounded-2xl bg-teal-950 p-6 text-white shadow-float sm:p-8">
            <div class="pointer-events-none absolute inset-0 opacity-[0.06]"
                 style="background-image: linear-gradient(to right, #ffffff 1px, transparent 1px), linear-gradient(to bottom, #ffffff 1px, transparent 1px); background-size: 40px 40px;"></div>
            <div class="pointer-events-none absolute -right-20 -top-24 h-64 w-64 rounded-full bg-teal-700/40 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 left-1/3 h-56 w-56 rounded-full bg-gold/15 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4 sm:gap-5">
                    <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-gold font-display text-xl font-bold uppercase text-teal-950 ring-4 ring-white/10 sm:h-20 sm:w-20 sm:text-2xl">
                        {{ mb_substr($user->name, 0, 2) }}
                    </span>

                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="truncate font-display text-xl font-semibold sm:text-2xl">
                                {{ $user->name }}
                            </h2>

                            @if ($user->isSuperAdmin())
                                <span class="inline-flex items-center gap-1 rounded-full bg-gold/20 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-gold">
                                    <x-admin.icon name="star" class="h-3 w-3" />
                                    Super Admin
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-white/10 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-white/85">
                                    Admin
                                </span>
                            @endif

                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide {{ $user->aktif ? 'bg-success/15 text-green-300' : 'bg-danger/15 text-red-300' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $user->aktif ? 'bg-green-300' : 'bg-red-300' }}"></span>
                                {{ $user->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        <p class="mt-1.5 truncate text-sm text-white/80">{{ $user->email }}</p>

                        <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-white/70">
                            <span class="inline-flex items-center gap-1.5">
                                <x-admin.icon name="calendar" class="h-3.5 w-3.5" />
                                Bergabung {{ $user->created_at?->format('d/m/Y') ?? '-' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <x-admin.icon name="article" class="h-3.5 w-3.5" />
                                {{ $user->posts_count }} tulisan
                            </span>
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-white/20 bg-white/5 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2 focus:ring-offset-teal-950">
                    <x-admin.icon name="arrow-left" class="h-4 w-4" />
                    Kembali ke dashboard
                </a>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem] xl:grid-cols-[minmax(0,1fr)_22rem]">
            {{-- Form pengaturan --}}
            <div class="space-y-6">
                <div class="rounded-xl border border-line bg-bone p-5 sm:p-6">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="rounded-xl border border-line bg-bone p-5 sm:p-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Ringkasan akun & zona berbahaya --}}
            <aside class="space-y-6">
                <div class="rounded-xl border border-line bg-bone p-5">
                    <h3 class="flex items-center gap-2 font-display text-base font-semibold text-teal-deep">
                        <x-admin.icon name="shield" class="h-4 w-4 text-teal-bright" />
                        Ringkasan akun
                    </h3>

                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-ink-soft">Peran</dt>
                            <dd class="text-right font-medium text-ink">
                                {{ $user->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
                            </dd>
                        </div>

                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-ink-soft">Status akun</dt>
                            <dd class="text-right font-medium {{ $user->aktif ? 'text-success-text' : 'text-danger-text' }}">
                                {{ $user->aktif ? 'Aktif' : 'Nonaktif' }}
                            </dd>
                        </div>

                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-ink-soft">Email</dt>
                            <dd class="max-w-[11rem] truncate text-right font-medium text-ink" title="{{ $user->email }}">
                                {{ $user->email }}
                            </dd>
                        </div>

                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-ink-soft">Verifikasi email</dt>
                            <dd class="inline-flex items-center gap-1 text-right font-medium {{ $user->email_verified_at ? 'text-success-text' : 'text-gold-text' }}">
                                <x-admin.icon :name="$user->email_verified_at ? 'check' : 'alert'" class="h-3.5 w-3.5" />
                                {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum' }}
                            </dd>
                        </div>

                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-ink-soft">Tulisan</dt>
                            <dd class="text-right font-medium text-ink">{{ $user->posts_count }}</dd>
                        </div>
                    </dl>

                    <p class="mt-4 rounded-lg bg-paper/70 px-3 py-2.5 text-xs leading-relaxed text-ink-soft">
                        Peran dan status akun hanya dapat diubah oleh Super Admin melalui menu
                        Manajemen Admin.
                    </p>
                </div>

                <div class="rounded-xl border border-danger/25 bg-bone p-5">
                    @include('profile.partials.delete-user-form')
                </div>
            </aside>
        </div>
    </div>
@endsection
