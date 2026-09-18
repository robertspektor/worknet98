<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name'))</title>
        <meta name="description" content="@yield('description', '')">

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts
        @vite(['resources/css/landing.css', 'resources/js/landing.ts'])
    </head>
    <body class="page">
        <header class="page-header">
            <a class="page-brand" href="{{ route('landing') }}">
                <span class="page-brand-name">{{ config('app.name') }}</span>
                <span class="page-brand-tag">{{ __('landing.tagline') }}</span>
            </a>
        </header>

        <main class="page-main">
            @yield('content')
        </main>

        <footer class="page-footer">
            <nav class="page-footer-links">
                <a href="{{ route('imprint') }}">{{ __('legal.imprint') }}</a>
                <a href="{{ route('privacy') }}">{{ __('legal.privacy') }}</a>
            </nav>
            <p class="page-footer-note">{{ __('landing.footer_note') }}</p>
        </footer>
    </body>
</html>
