@use('Illuminate\Support\Number')
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
    <body class="page is-landing">
        @include('public.partials.header')

        <div class="hero">
            @include('public.partials.hero-window')

            <div class="landing-intro">
                <h1>
                    {{ __('landing.hero_title') }}<br class="hero-break">
                    <span class="hero-accent">{{ __('landing.hero_accent') }}</span>
                </h1>
                <p>
                    {{ $city === null ? __('landing.hero_lead_anywhere') : __('landing.hero_lead', ['city' => $city]) }}<br>
                    <span class="hero-action">{{ __('landing.hero_action') }}</span>
                </p>
            </div>

            <div class="landing-stage">
                <x-inertia::app />
            </div>

        </div>

        <main class="page-main">
            <section class="cards">
                @foreach (['life', 'career', 'city', 'path'] as $index => $card)
                    <article class="card-item">
                        <span class="card-index">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <h2 class="card-title">{{ __("landing.card_{$card}_title") }}</h2>
                        <p class="card-body">{{ __("landing.card_{$card}_body") }}</p>
                    </article>
                @endforeach
            </section>

            <section class="showcase">
                <h2 class="showcase-title">{{ __('landing.progress_title') }}</h2>
                <p class="showcase-lead">{{ __('landing.progress_lead') }}</p>

                <div class="progress-pair">
                    <article class="spec">
                        <span class="spec-label">{{ __('landing.progress_start_label') }}</span>
                        <ul class="spec-list">
                            @foreach (range(1, 4) as $line)
                                <li>{{ __("landing.progress_start_{$line}") }}</li>
                            @endforeach
                        </ul>
                    </article>

                    <span class="progress-arrow" aria-hidden="true"></span>

                    <article class="spec is-later">
                        <span class="spec-label">{{ __('landing.progress_later_label') }}</span>
                        <ul class="spec-list">
                            @foreach (range(1, 5) as $line)
                                <li>{{ __("landing.progress_later_{$line}") }}</li>
                            @endforeach
                        </ul>
                    </article>
                </div>

                <p class="showcase-caption">{{ __('landing.progress_caption') }}</p>
            </section>

            @if ($figures !== null)
                <section class="showcase">
                    <h2 class="showcase-title">{{ __('landing.city_title', ['city' => $figures->city]) }}</h2>

                    <div class="city-map" aria-hidden="true"></div>

                    <div class="figures">
                        @foreach ([
                            'residents' => $figures->residents,
                            'companies' => $figures->companies,
                            'jobs' => $figures->openPositions,
                            'deliveries' => $figures->deliveries,
                        ] as $figure => $value)
                            <div class="figure">
                                <span class="figure-value">{{ Number::format($value, locale: app()->getLocale()) }}</span>
                                <span class="figure-label">{{ __("landing.city_{$figure}") }}</span>
                            </div>
                        @endforeach
                    </div>

                    <p class="showcase-body">{{ __('landing.city_body') }}</p>
                </section>
            @endif

            <section class="showcase">
                <h2 class="showcase-title">{{ __('landing.mystery_title') }}</h2>

                <pre class="terminal" aria-hidden="true">A:\> DIR

 SETUP    EXE     14208
 README   TXT      2104
<span class="terminal-flag"> M441     DAT     18336</span>

A:\> <span class="terminal-caret">_</span></pre>

                <p class="showcase-body">{{ __('landing.mystery_body') }}</p>
                <p class="showcase-caption">{{ __('landing.mystery_caption') }}</p>
            </section>

            <section class="showcase">
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
            </section>
        </main>

        @include('public.partials.footer')
    </body>
</html>
