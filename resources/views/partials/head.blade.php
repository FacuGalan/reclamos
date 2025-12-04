<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>{{ $title ?? config('app.name') }}</title>

<!-- Meta description para Google -->
<meta name="description" content="Accede a información sobre trámites, horarios de atención y contactos útiles.">

<!-- Favicons -->
<link rel="icon" href="/favicon.ico" sizes="48x48">
<link rel="icon" href="/favicon-48x48.png" type="image/png" sizes="48x48">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<meta name="google-site-verification" content="f3nft3FUu_BsGg83k7ms9BDf7EcCySc4QZdNZsd_n1w" />

<!-- Open Graph -->
<meta property="og:title" content="{{ $title ?? config('app.name') }}">
<meta property="og:description" content="Atención Ciudadana - Municipalidad de Mercedes">
<meta property="og:image" content="{{ asset('fotos/Recurso_13.png') }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance