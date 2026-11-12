<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{ Vite::fonts() }}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-paper px-4 py-8">
            <div class="mb-6 text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gold font-display text-base font-bold text-teal-950">
                    MTs
                </span>
                <p class="mt-3 font-display text-lg font-semibold text-teal-950">MTs NU Al Syairiyah</p>
                <p class="text-xs uppercase tracking-[0.18em] text-ink-soft">Panel Admin</p>
            </div>

            <div class="w-full overflow-hidden rounded-xl border border-line bg-bone px-6 py-6 shadow-float sm:max-w-md">
                {{ $slot }}
            </div>

            <p class="mt-6 text-xs text-ink-soft">
                &copy; {{ date('Y') }} MTs NU Al Syairiyah
            </p>
        </div>
    </body>
</html>
