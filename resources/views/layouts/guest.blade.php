<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Étude Rapide')</title>
    <meta name="description" content="@yield('description', 'Plateforme d’apprentissage premium')">

    <link rel="icon" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css','resources/js/app.js'])

    @stack('head')
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    @include('partials.header')

    <main class="min-h-screen">
        @yield('content')
    </main>

    @include('partials.footer')

    @include('components.chat-widget')

    @stack('scripts')
</body>
</html>
