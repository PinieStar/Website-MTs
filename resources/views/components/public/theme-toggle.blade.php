@props(['class' => ''])

<button type="button"
        class="theme-toggle items-center justify-center rounded-full transition-colors {{ $class }}"
        aria-label="Ganti ke tema gelap"
        title="Tema gelap"
        aria-pressed="false"
        data-theme-toggle>
    <x-public.icon name="moon" :size="18" class="theme-icon-moon" />
    <x-public.icon name="sun" :size="18" class="theme-icon-sun" />
</button>
