@extends('public.layout')

@section('title', __('landing.meta_title'))
@section('description', __('landing.meta_description'))

@section('content')
    <section class="hero">
        <div class="hero-text">
            <h1>{{ __('landing.hero_title') }}</h1>
            <p class="hero-lead">{{ __('landing.hero_lead') }}</p>
            <p class="hero-hint">{{ __('landing.returning_hint') }}</p>
        </div>

        <div class="hero-machine">
            <div class="catalogue" id="catalogue" data-props="{{ json_encode($catalogue) }}"></div>
            <noscript>
                <p class="hero-hint">{{ __('landing.needs_javascript') }}</p>
            </noscript>
        </div>
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
@endsection
