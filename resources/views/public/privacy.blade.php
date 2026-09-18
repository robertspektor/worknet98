@extends('public.layout')

@section('title', __('legal.privacy') . ' - ' . config('app.name'))
@section('description', __('legal.privacy'))

@section('content')
    <article class="prose">
        <h1>{{ __('legal.privacy') }}</h1>
        <p class="placeholder">{{ __('legal.placeholder') }}</p>
    </article>
@endsection
