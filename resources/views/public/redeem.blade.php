@extends('public.layout')

@section('title', __('redeem.title') . ' - ' . config('app.name'))
@section('description', __('redeem.body'))

@section('content')
    <article class="prose">
        <h1>{{ __('redeem.title') }}</h1>
        <p>{{ __('redeem.body') }}</p>

        <form method="POST" action="{{ route('login.store', $token) }}" data-auto-submit>
            @csrf
            <button class="button" type="submit">{{ __('redeem.button') }}</button>
        </form>
    </article>
@endsection
