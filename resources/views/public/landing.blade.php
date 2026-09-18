@extends('public.layout')

@section('title', __('landing.meta_title'))
@section('description', __('landing.meta_description'))

@section('content')
    <section class="hero">
        <div class="hero-text">
            <h1>{{ __('landing.hero_title') }}</h1>
            <p class="hero-lead">{{ __('landing.hero_lead') }}</p>
        </div>

        <div class="catalogue" id="catalogue">
            <div class="monitor">
                <div class="monitor-screen">
                    <p class="screen-title">{{ __('landing.catalogue_title') }}</p>
                    <p class="screen-note">{{ __('landing.catalogue_note') }}</p>

                    <form class="order-form" method="POST" action="{{ route('sign-in.store') }}" novalidate>
                        @csrf

                        <label class="field" for="order-locale">
                            <span>{{ __('setup.language') }}</span>
                            <select class="input" id="order-locale" name="locale">
                                @foreach ($locales as $code => $label)
                                    <option value="{{ $code }}" @selected($code === app()->getLocale())>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="field" for="order-email">
                            <span>{{ __('setup.email') }}</span>
                            <input class="input" id="order-email" name="email" type="email" autocomplete="email" value="{{ old('email') }}">
                            @error('email')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="checkbox-field">
                            <input type="checkbox" name="age_confirmed" value="1" @checked(old('age_confirmed'))>
                            <span>{{ __('setup.age_confirmed') }}</span>
                        </label>
                        @error('age_confirmed')
                            <span class="field-error">{{ $message }}</span>
                        @enderror

                        <button class="button" type="submit">{{ __('landing.order_submit') }}</button>
                    </form>

                    @if (session('status') === 'login-link-sent')
                        <p class="screen-status">{{ __('setup.sent_body') }}</p>
                    @endif
                </div>
            </div>
            <p class="catalogue-hint">{{ __('landing.returning_hint') }}</p>
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
