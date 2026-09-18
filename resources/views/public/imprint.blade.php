@extends('public.layout')

@section('title', __('legal.imprint') . ' - ' . config('app.name'))
@section('description', __('legal.imprint'))

@section('content')
    <article class="prose">
        <h1>{{ __('legal.imprint') }}</h1>
        <p class="placeholder">{{ __('legal.placeholder') }}</p>
    </article>
@endsection
