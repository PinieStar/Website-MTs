@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'icon' => 'arrow-right',
    'iconPosition' => 'end',
    'class' => '',
    'type' => 'button',
    'target' => null,
    'ariaLabel' => null,
    'id' => null,
])

@php
    $isExternal = $href && preg_match('/^https?:\/\//', $href);
    $resolvedTarget = $target ?? ($isExternal ? '_blank' : null);
    $rel = $resolvedTarget === '_blank' ? 'noopener noreferrer' : null;

    $base = 'group inline-flex items-center justify-center gap-2 rounded-pill font-semibold leading-none transition-[background-color,border-color,color,transform,box-shadow] duration-300 ease-out focus-visible:outline-2 focus-visible:outline-offset-3 disabled:pointer-events-none disabled:opacity-50';

    $sizes = [
        'sm' => 'min-h-10 px-4 text-[0.78rem]',
        'md' => 'min-h-11 px-5 text-[0.84rem]',
        'lg' => 'min-h-[3.25rem] px-6 text-[0.9rem]',
    ];

    $variants = [
        'primary' => 'btn-primary bg-teal-deep text-paper hover:bg-teal-mid shadow-[0_1px_0_rgb(255_255_255/0.08)_inset]',
        'secondary' => 'bg-bone text-teal-deep border border-line hover:border-teal-bright hover:bg-mint',
        'gold' => 'btn-gold bg-gold text-teal-deep hover:bg-[#d8b45f] hover:shadow-float',
        'ghost' => 'bg-transparent text-teal-deep hover:bg-teal-deep/5',
        'outline-light' => 'bg-transparent text-paper border border-paper/25 hover:border-paper/70 hover:bg-paper/5',
    ];

    $classes = trim($base.' '.($sizes[$size] ?? $sizes['md']).' '.($variants[$variant] ?? $variants['primary']).' '.$class);
    $tagAttributes = $attributes->merge(['class' => $classes]);
@endphp

@if ($href)
    <a href="{{ $href }}"
       @if ($resolvedTarget) target="{{ $resolvedTarget }}" @endif
       @if ($rel) rel="{{ $rel }}" @endif
       @if ($ariaLabel) aria-label="{{ $ariaLabel }}" @endif
       @if ($id) id="{{ $id }}" @endif
       {{ $tagAttributes }}>
        @if ($iconPosition === 'start' && $icon)
            <x-public.icon :name="$icon" :size="17" class="transition-transform duration-300 group-hover:-translate-x-0.5" />
        @endif
        <span>{{ $slot }}</span>
        @if ($iconPosition === 'end' && $icon)
            <x-public.icon :name="$icon" :size="17" class="transition-transform duration-300 group-hover:translate-x-0.5" />
        @endif
    </a>
@else
    <button type="{{ $type }}"
            @if ($ariaLabel) aria-label="{{ $ariaLabel }}" @endif
            @if ($id) id="{{ $id }}" @endif
            {{ $tagAttributes }}>
        @if ($iconPosition === 'start' && $icon)
            <x-public.icon :name="$icon" :size="17" class="transition-transform duration-300 group-hover:-translate-x-0.5" />
        @endif
        <span>{{ $slot }}</span>
        @if ($iconPosition === 'end' && $icon)
            <x-public.icon :name="$icon" :size="17" class="transition-transform duration-300 group-hover:translate-x-0.5" />
        @endif
    </button>
@endif
