@props([
    'size' => 44,
    'loading' => 'eager',
])

<img src="/images/logo.png"
     alt="Logo MTs NU Al Syairiyah"
     width="{{ $size * 2 }}"
     height="{{ (int) round($size * 1.846) }}"
     loading="{{ $loading }}"
     decoding="async"
     style="width:{{ $size }}px"
     {{ $attributes->merge(['class' => 'block h-auto object-contain']) }}>
