<!DOCTYPE html>
<html lang="{{ app()->getLocale() == 'kh' ? 'km' : 'en' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ __('auth.pos_system') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('Uploads/products/Yotta_Icon.png') }}">

    @vite(['resources/css/app.css'])

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loading-overlay.css') }}">

    @stack('styles')
</head>

<body class="@yield('body-class')">
    <!-- Global loading overlay -->
    <div id="global-loading-overlay" class="global-loading-overlay d-none" aria-hidden="true" aria-live="polite">
        <div class="global-loading-overlay__content">
            <div class="spinner-border text-light global-loading-overlay__spinner" role="status">
                <span class="visually-hidden">{{ __('Loading...') }}</span>
            </div>
        </div>
    </div>

    @yield('content')

    @vite(['resources/js/app.js'])
    <script src="{{ asset('js/layouts/loading-overlay.js') }}" defer></script>
    @stack('scripts')
</body>

</html>
