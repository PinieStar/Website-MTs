@php
    $site = config('site');
    $seo = $site['seo'];

    // Section inline sudah di-escape oleh Blade, jadi decode dulu agar tidak ter-escape dua kali.
    $pageTitle = trim(html_entity_decode($__env->yieldContent('title'), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $fullTitle = $pageTitle !== ''
        ? $pageTitle.' '.$seo['title_separator'].' '.$site['name']
        : $seo['default_title'];

    $metaDescription = trim(html_entity_decode($__env->yieldContent('meta_description'), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $metaDescription = $metaDescription !== '' ? $metaDescription : $seo['default_description'];

    $canonical = rtrim(url()->current(), '/').'/';
    $ogImage = url($seo['default_image']);

    $robotsContent = trim($__env->yieldContent('robots'));
    $robotsContent = $robotsContent !== ''
        ? $robotsContent
        : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

    $bodyClass = trim($__env->yieldContent('body_class'));

    // Ditulis terpisah agar "@context" tidak dibaca Blade sebagai directive.
    $schemaContext = '@'.'context';

    $schemaGraph = [
        [
            '@type' => 'Organization',
            '@id' => $canonical.'#organization',
            'name' => $site['name'],
            'alternateName' => $site['alternate_names'],
            'url' => $canonical,
            'logo' => url('/images/logo.png'),
            'description' => $site['description'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $site['contact']['address'],
                'addressLocality' => 'Limpung',
                'addressRegion' => 'Jawa Tengah',
                'addressCountry' => 'ID',
            ],
            'telephone' => $site['contact']['phone'],
            'email' => $site['contact']['email'],
            'sameAs' => array_values(array_filter($site['social'])),
        ],
        [
            '@type' => 'WebSite',
            '@id' => $canonical.'#website',
            'url' => $canonical,
            'name' => $site['name'],
            'description' => $site['description'],
            'inLanguage' => $seo['language'],
            'publisher' => ['@id' => $canonical.'#organization'],
        ],
        [
            '@type' => 'WebPage',
            '@id' => $canonical.'#webpage',
            'url' => $canonical,
            'name' => $fullTitle,
            'description' => $metaDescription,
            'inLanguage' => $seo['language'],
            'isPartOf' => ['@id' => $canonical.'#website'],
        ],
    ];
@endphp

<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ implode(', ', $site['keywords']) }}">
    <meta name="author" content="{{ $site['name'] }}">
    <meta name="publisher" content="{{ $site['name'] }}">
    <meta name="robots" content="{{ $robotsContent }}">
    <meta name="googlebot" content="{{ $robotsContent }}">
    <link rel="canonical" href="{{ $canonical }}">
    <link rel="alternate" hreflang="id" href="{{ $canonical }}">
    <link rel="alternate" hreflang="x-default" href="{{ $canonical }}">

    {{-- Penargetan lokal --}}
    <meta name="geo.region" content="{{ $seo['geo']['region'] }}">
    <meta name="geo.placename" content="{{ $seo['geo']['placename'] }}">
    <meta name="geo.position" content="{{ $seo['geo']['latitude'] }};{{ $seo['geo']['longitude'] }}">
    <meta name="ICBM" content="{{ $seo['geo']['latitude'] }}, {{ $seo['geo']['longitude'] }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $site['name'] }}">
    <meta property="og:locale" content="{{ $seo['locale'] }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="{{ $seo['image_width'] }}">
    <meta property="og:image:height" content="{{ $seo['image_height'] }}">
    <meta property="og:image:alt" content="{{ $site['name'] }}: {{ $site['tagline'] }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <meta name="twitter:image:alt" content="{{ $site['name'] }}: {{ $site['tagline'] }}">

    <meta name="theme-color" content="#042F2E" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0B1F1E" media="(prefers-color-scheme: dark)">

    <link rel="icon" type="image/png" sizes="64x64" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/icon-512.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">

    <link rel="preload" href="{{ Vite::asset('resources/fonts/plus-jakarta-sans-latin-wght-normal.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">

    <script type="application/ld+json">
        {!! json_encode([$schemaContext => 'https://schema.org', '@graph' => $schemaGraph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <script>
        // Tema: pasang sebelum halaman tampil agar tidak berkedip. Bawaan terang.
        (function () {
            var theme = 'light';
            try {
                if (localStorage.getItem('mts-theme') === 'dark') theme = 'dark';
            } catch (e) {}
            var root = document.documentElement;
            root.dataset.theme = theme;
            root.style.colorScheme = theme;
        })();
    </script>

    <script>
        // Panel transisi sudah menutup layar sejak dokumen baru mulai tampil.
        (function () {
            try {
                if (sessionStorage.getItem('mts-transition') === '1') {
                    document.documentElement.classList.add('is-transitioning');
                }
            } catch (e) {}
        })();
    </script>

    <script>
        // Progressive enhancement: content is only hidden for GSAP when JS runs.
        document.documentElement.classList.add('js');
        window.setTimeout(function () {
            if (!window.__mtsAnimationsReady) {
                document.documentElement.classList.remove('js');
            }
        }, 2500);
    </script>

    @vite(['resources/css/app.css', 'resources/js/public.js'])

    @stack('head')
</head>
<body class="public-site min-h-screen bg-paper text-ink antialiased {{ $bodyClass }}">
    <a href="#main-content" class="skip-link">Lewati ke konten utama</a>

    <x-public.navbar />

    <main id="main-content" tabindex="-1">
        @yield('content')
    </main>

    <x-public.footer />
    <x-public.whatsapp-button />
    <x-public.page-transition />

    @stack('scripts')
</body>
</html>
