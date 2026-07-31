<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Taller Automotriz</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Estilos del Dashboard y 3D -->
     <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ filemtime(public_path('css/dashboard.css')) }}">
     <link rel="stylesheet" href="{{ asset('css/client.css') }}?v={{ filemtime(public_path('css/client.css')) }}">
     <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ filemtime(public_path('css/landing.css')) }}">
    
    <!-- Fuentes operativas -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
     <!-- CSS operativo (después de dashboard.css) -->
     <link rel="stylesheet" href="{{ asset('css/operativo.css') }}?v={{ filemtime(public_path('css/operativo.css')) }}">
</head>
<body>

    {{-- ================================================
         PANTALLA DE PRESENTACIÓN (LANDING PAGE)
    ================================================ --}}
    @include('partials.landing')

    {{-- ================================================
         PANTALLA DE LOGIN
    ================================================ --}}
    @include('partials.login')

    {{-- ================================================
         BARRA LATERAL DE NAVEGACIÓN
    ================================================ --}}
    @include('partials.sidebar')

    {{-- ================================================
         CONTENIDO PRINCIPAL DEL DASHBOARD (ADMIN)
    ================================================ --}}
    @include('partials.dashboard')

    {{-- ================================================
         PORTAL DEL CLIENTE
    ================================================ --}}
    @include('partials.client_dashboard')

    {{-- ================================================
         PORTALES OPERATIVOS (Recepcionista y Mecánico)
    ================================================ --}}
    @include('partials.recepcion')
    @include('partials.mecanico')

    {{-- ================================================
         MODALES Y NOTIFICACIONES
    ================================================ --}}
    @include('partials.modals')

    <!-- Bootstrap 5 Bundle JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
     <!-- Lógica principal de la aplicación -->
     <script defer src="{{ asset('js/dashboard.js') }}?v={{ filemtime(public_path('js/dashboard.js')) }}"></script>
     <script defer src="{{ asset('js/client.js') }}?v={{ filemtime(public_path('js/client.js')) }}"></script>
     <script defer src="{{ asset('js/recepcion.js') }}?v={{ filemtime(public_path('js/recepcion.js')) }}"></script>
     <script defer src="{{ asset('js/mecanico.js') }}?v={{ filemtime(public_path('js/mecanico.js')) }}"></script>

</body>
</html>
