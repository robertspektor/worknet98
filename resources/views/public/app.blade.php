<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('landing.meta_title') }}</title>
        <meta name="description" content="{{ __('landing.meta_description') }}">

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
    </head>
    <body class="page">
        @include('public.partials.header')

        <div class="landing-intro">
            <h1>{{ __('landing.hero_title') }}</h1>
            <p>{{ __('landing.hero_lead') }}</p>
        </div>

        <div class="landing-stage">
            <x-inertia::app />
        </div>

        <main class="page-main">
            <p class="stage-hint">{{ __('landing.stage_hint') }}</p>

            <section class="cards">
                @foreach (['life', 'career', 'city', 'path'] as $card)
                    <article class="os card-window">
                        <div class="window is-focused">
                            <header class="title-bar">
                                <span class="title-bar-text">
                                    <span>{{ __("landing.card_{$card}_title") }}</span>
                                </span>
                            </header>
                            <div class="window-body card-body">
                                <p>{{ __("landing.card_{$card}_body") }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <article class="prose">
                <h2>{{ __('landing.about_title') }}</h2>
                <p>{{ __('landing.about_body') }}</p>

                <h2>{{ __('landing.loop_title') }}</h2>
                <p>{{ __('landing.loop_body') }}</p>

                <h2>{{ __('landing.world_title') }}</h2>
                <p>{{ __('landing.world_body') }}</p>

                <h2>{{ __('landing.money_title') }}</h2>
                <p>{{ __('landing.money_body') }}</p>

                <h2>{{ __('landing.access_title') }}</h2>
                <p>{{ __('landing.access_body') }}</p>
            </article>
        </main>

        @include('public.partials.footer')
    </body>
</html>
