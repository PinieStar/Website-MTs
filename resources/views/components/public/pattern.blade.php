@props([
    'variant' => 'star',
    'class' => '',
    'opacity' => 0.08,
])

@php($id = 'pat-'.substr(md5(uniqid('', true)), 0, 7))

<svg class="pointer-events-none absolute inset-0 h-full w-full {{ $class }}"
     style="opacity:{{ $opacity }}"
     aria-hidden="true"
     focusable="false"
     preserveAspectRatio="xMidYMid slice">
    <defs>
        @if ($variant === 'star')
            <pattern id="{{ $id }}" width="80" height="80" patternUnits="userSpaceOnUse">
                <g fill="none" stroke="currentColor" stroke-width="1">
                    <path d="M40 0 L47 26 L70 10 L59 33 L80 40 L59 47 L70 70 L47 54 L40 80 L33 54 L10 70 L21 47 L0 40 L21 33 L10 10 L33 26 Z" />
                    <circle cx="40" cy="40" r="6" />
                </g>
            </pattern>
        @elseif ($variant === 'arch')
            <pattern id="{{ $id }}" width="72" height="84" patternUnits="userSpaceOnUse">
                <g fill="none" stroke="currentColor" stroke-width="1">
                    <path d="M8 84 V40a28 28 0 0 1 56 0v44" />
                    <path d="M20 84 V40a16 16 0 0 1 32 0v44" />
                </g>
            </pattern>
        @else
            <pattern id="{{ $id }}" width="48" height="48" patternUnits="userSpaceOnUse">
                <path d="M48 0H0V48" fill="none" stroke="currentColor" stroke-width="1" />
                <circle cx="0" cy="0" r="2" fill="currentColor" />
            </pattern>
        @endif
    </defs>
    <rect width="100%" height="100%" fill="url(#{{ $id }})" />
</svg>
