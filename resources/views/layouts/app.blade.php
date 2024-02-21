<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FCE Webapp') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Cabin&family=Montserrat:wght@300;400;500;600;700;900&display=swap" rel="stylesheet"> 

        <!-- Styles -->
        @vite('resources/styles/app/app.scss')
        @yield('styles')
    </head>
    <body>
        <div id="app">
            @include('partials.navigation')
            
            @yield('content')
        </div>

        <!-- Scripts -->
        @vite('resources/js/app.js')
        @yield('scripts')
    </body>
</html>
