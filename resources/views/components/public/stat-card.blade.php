@props([
    'label',
    'value' => null,
    'icon' => 'users',
    'suffix' => '',
    'class' => '',
])

@php($numeric = is_int($value) || is_float($value))

<div class="bento bento-mint relative overflow-hidden p-5 sm:p-6 {{ $class }}" data-stagger-item>
    <div class="flex items-start justify-between gap-4">
        <span class="grid size-9 place-items-center rounded-full bg-teal-deep text-paper/90" aria-hidden="true">
            <x-public.icon :name="$icon" :size="17" />
        </span>
        <span class="h-px w-8 bg-gold/70" aria-hidden="true"></span>
    </div>

    <div class="mt-8">
        @if ($numeric)
            <p class="font-display text-[clamp(2.5rem,4.5vw,3.6rem)] font-extrabold leading-none tracking-[-0.05em] text-teal-deep"
               data-count="{{ $value }}"
               data-count-suffix="{{ $suffix }}"
               aria-label="{{ $value }}{{ $suffix }} {{ $label }}">
                {{ $value }}{{ $suffix }}
            </p>
        @else
            <p class="pending font-display text-[clamp(2rem,4vw,2.75rem)] font-extrabold leading-none tracking-[-0.04em]">
                {{ $value ?? 'Belum tersedia' }}
            </p>
        @endif
        <p class="mt-3 text-[0.7rem] font-bold uppercase tracking-[0.16em] text-ink-soft">{{ $label }}</p>
    </div>
</div>
