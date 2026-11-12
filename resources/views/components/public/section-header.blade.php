@props([
    'eyebrow' => null,
    'title',
    'lead' => null,
    'number' => null,
    'align' => 'left',
    'theme' => 'light',
    'class' => '',
    'titleClass' => '',
    'headingId' => null,
    'level' => 'h2',
])

@php
    $tag = $level === 'h1' ? 'h1' : 'h2';
    $isDark = $theme === 'dark';
    $alignment = $align === 'center' ? 'items-center text-center' : 'items-start';
    $headingClass = 'font-display text-display font-bold '.($isDark ? 'text-paper' : 'text-teal-deep').' '.$titleClass;
@endphp

<div class="flex flex-col gap-6 {{ $alignment }} {{ $class }}">
    @if ($eyebrow)
        <p class="eyebrow {{ $isDark ? 'text-gold-soft' : '' }}" data-reveal="fade">
            <span class="gold-rule" data-gold-line aria-hidden="true"></span>
            {{ $eyebrow }}
        </p>
    @endif

    <div class="flex w-full flex-col gap-5 {{ $align === 'center' ? 'items-center' : 'md:flex-row md:items-end md:justify-between' }}">
        <div class="max-w-3xl">
            <{{ $tag }} @if ($headingId) id="{{ $headingId }}" @endif class="{{ $headingClass }}" data-reveal="lines">
                @if ($number)
                    <span class="mr-3 inline-block align-top font-sans text-[0.7em] font-semibold tracking-[-0.02em] text-gold" aria-hidden="true">{{ $number }}</span>
                @endif
                {{ $title }}
            </{{ $tag }}>
            @if ($lead)
                <p class="measure mt-5 text-lead {{ $isDark ? 'text-paper/70' : 'text-ink-soft' }}" data-reveal="fade">
                    {{ $lead }}
                </p>
            @endif
        </div>
        <div class="shrink-0" data-reveal="fade">
            {{ $actions ?? '' }}
        </div>
    </div>
</div>
