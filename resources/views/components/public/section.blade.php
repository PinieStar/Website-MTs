@props([
    'id' => null,
    'class' => '',
    'containerClass' => '',
    'labelledby' => null,
])

<section @if ($id) id="{{ $id }}" @endif class="relative py-section {{ $class }}" @if ($labelledby) aria-labelledby="{{ $labelledby }}" @endif>
    <div class="container-page {{ $containerClass }}">
        {{ $slot }}
    </div>
</section>
