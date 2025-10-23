<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico?v={{ time() }}" sizes="any">
<link rel="icon" href="/favicon.svg?v={{ time() }}" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png?v={{ time() }}">
<!-- Descripción corta del contenido de la página -->
<meta property="og:description" content="Atencion Ciudadana - Municipalidad de Mercedes">
<meta property="og:image" content="{{ asset('fotos/Recurso_13.png') }}">

<!-- El tipo de contenido (generalmente 'website') -->
<meta property="og:type" content="website">

<!-- La URL canónica de la página compartida -->
<meta property="og:url" content="{{ url()->current() }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
