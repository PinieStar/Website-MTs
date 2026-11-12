@props([
    'eyebrow',
    'title',
    'lead' => null,
    'class' => '',
    'headingId' => null,
])

<header class="relative overflow-hidden pb-10 pt-32 sm:pt-36 lg:pb-14 lg:pt-40 {{ $class }}">
    <x-public.pattern variant="grid" class="text-teal-deep" :opacity="0.045" />

    <div class="container-page relative">
        <div class="grid items-end gap-8 lg:grid-cols-12">
            <div class="lg:col-span-12">
                <p class="eyebrow" data-reveal="fade">
                    <span class="gold-rule" data-gold-line aria-hidden="true"></span>
                    {{ $eyebrow }}
                </p>

                <h1 @if ($headingId) id="{{ $headingId }}" @endif class="mt-6 max-w-4xl font-display text-hero font-extrabold text-teal-deep" data-reveal="lines">
                    {{ $title }}
                </h1>

                @if ($lead)
                    <p class="measure mt-6 text-lead text-ink-soft" data-reveal="fade">
                        {{ $lead }}
                    </p>
                @endif

                @isset($actions)
                    <div class="mt-8" data-reveal="fade">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        </div>
    </div>
</header>
