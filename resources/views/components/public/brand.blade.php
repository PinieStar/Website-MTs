@props([
    'theme' => 'light',
    'size' => 42,
    'loading' => 'eager',
])

@php($site = config('site'))

<a href="{{ route('public.home') }}"
   class="group inline-flex items-center gap-2.5 {{ $attributes->get('class') }}"
   aria-label="{{ $site['name'] }}, beranda">
    <x-public.logo :size="$size" :loading="$loading" />
    <span class="flex flex-col leading-none">
        <span class="text-[0.62rem] font-bold uppercase tracking-[0.16em] {{ $theme === 'dark' ? 'text-paper/60' : 'text-ink-soft' }}">MTs NU</span>
        <span class="mt-1 font-display text-[1.02rem] font-extrabold tracking-[-0.025em] {{ $theme === 'dark' ? 'text-paper' : 'text-teal-deep' }}">Al Syairiyah</span>
    </span>
</a>
