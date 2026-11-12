@props([
    'href',
    'title',
    'description' => null,
    'eyebrow' => null,
    'icon' => 'arrow-up-right',
    'tone' => 'light',
    'class' => '',
    'external' => false,
])

@php($isDark = $tone === 'dark')

<a href="{{ $href }}"
   @if ($external) target="_blank" rel="noopener noreferrer" @endif
   class="bento bento-link group p-5 sm:p-6 {{ $isDark ? 'bento-dark' : '' }} {{ $tone === 'mint' ? 'bento-mint' : '' }} {{ $class }}">
    @if ($isDark)
        <x-public.pattern variant="star" class="text-paper" :opacity="0.07" />
    @endif

    <div class="relative flex h-full flex-col justify-between gap-10">
        <div class="flex items-start justify-between gap-4">
            <span class="grid size-11 place-items-center rounded-full border {{ $isDark ? 'border-paper/20 bg-paper/5 text-gold' : 'border-teal-bright/15 bg-white/70 text-teal-bright' }}" aria-hidden="true">
                <x-public.icon :name="$icon" :size="20" />
            </span>

            <span class="grid size-9 place-items-center rounded-full transition-transform duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 {{ $isDark ? 'bg-paper text-teal-deep' : 'border border-line bg-bone text-teal-deep' }}" aria-hidden="true">
                <x-public.icon name="arrow-up-right" :size="16" />
            </span>
        </div>

        <div>
            @if ($eyebrow)
                <p class="text-[0.6rem] font-bold uppercase tracking-[0.2em] {{ $isDark ? 'text-paper/45' : 'text-ink-soft/80' }}">{{ $eyebrow }}</p>
            @endif
            <h3 class="mt-2 font-display text-card font-bold {{ $isDark ? 'text-paper' : 'text-teal-deep' }}">{{ $title }}</h3>
            @if ($description)
                <p class="mt-2 max-w-[32ch] text-sm leading-relaxed {{ $isDark ? 'text-paper/60' : 'text-ink-soft' }}">{{ $description }}</p>
            @endif
        </div>
    </div>
</a>
