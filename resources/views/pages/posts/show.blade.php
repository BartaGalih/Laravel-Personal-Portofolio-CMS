@extends('layouts.public')

@section('title', $post->localized('title').' — '.$siteName)
@section('description', $post->localized('excerpt') ?? '')
@section('og_type', 'article')

@section('content')
    <article class="mx-auto max-w-3xl px-6 pt-20">
        <a href="{{ route('posts.index', ['locale' => app()->getLocale()]) }}"
           class="font-sans text-xs text-cloud-dark transition-colors hover:text-ink">&larr; All posts</a>

        <h1 class="mt-6 font-sans text-4xl font-bold leading-none tracking-tight text-ink sm:text-5xl">
            {{ $post->localized('title') }}
        </h1>

        @if ($post->published_at)
            <p class="mt-6 font-sans text-xs text-cloud-dark">{{ $post->published_at->format('j F Y') }}</p>
        @endif

        @if ($post->cover_image_path)
            <img src="{{ Storage::disk('public')->url($post->cover_image_path) }}" alt=""
                 class="mt-12 w-full rounded-3xl">
        @endif

        <div class="prose mt-12 max-w-none pb-20">
            {!! $content !!}
        </div>
    </article>
@endsection
