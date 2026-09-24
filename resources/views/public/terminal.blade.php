<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('terminal.page_title') }}</title>
        <meta name="robots" content="noindex">

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
    </head>
    <body class="page is-landing is-running">
        @include('public.partials.header')

        <div class="hero">
            @include('public.partials.hero-window')

            <div class="landing-stage">
                <x-inertia::app />
            </div>
        </div>
    </body>
</html>
