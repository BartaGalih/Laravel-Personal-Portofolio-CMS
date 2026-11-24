@extends('layouts.public')

@section('title', '404 — '.$siteName)

@section('content')
    <div class="mx-auto max-w-3xl px-6 pt-24 pb-20">
        <p class="ui-label text-cloud-dark">Error 404</p>
        <h1 class="mt-4 font-sans text-4xl font-bold leading-none tracking-tight text-ink sm:text-5xl">
            This page could not be found.
        </h1>
        <p class="mt-6 font-serif text-xl leading-relaxed text-ink-soft">
            The link may be broken, or the page may have moved.
        </p>
        <a href="{{ url('/') }}" class="btn-filled mt-10">Back home</a>
    </div>
@endsection
