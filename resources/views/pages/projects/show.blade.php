@extends('layouts.public')

@section('title', $project->localized('title').' — '.$siteName)
@section('description', $project->localized('summary') ?? '')
@section('og_type', 'article')

@section('content')
    <article class="mx-auto max-w-3xl px-6 pt-20">
        <a href="{{ route('projects.index', ['locale' => app()->getLocale()]) }}"
           class="font-sans text-xs text-cloud-dark transition-colors hover:text-ink">&larr; All projects</a>

        <h1 class="mt-6 font-sans text-4xl font-bold leading-none tracking-tight text-ink sm:text-5xl">
            {{ $project->localized('title') }}
        </h1>

        @if ($project->localized('summary'))
            <p class="mt-6 font-serif text-xl leading-relaxed text-ink-soft">
                {{ $project->localized('summary') }}
            </p>
        @endif

        @if ($project->project_url || $project->repository_url)
            <div class="mt-8 flex flex-wrap gap-4">
                @if ($project->project_url)
                    <a href="{{ $project->project_url }}" class="btn-filled" rel="noopener noreferrer">Live site</a>
                @endif
                @if ($project->repository_url)
                    <a href="{{ $project->repository_url }}"
                       class="font-sans text-sm text-ink underline underline-offset-4"
                       rel="noopener noreferrer">Repository</a>
                @endif
            </div>
        @endif

        @if ($project->cover_image_path)
            <img src="{{ Storage::disk('public')->url($project->cover_image_path) }}" alt=""
                 class="mt-12 w-full rounded-3xl">
        @endif

        <div class="prose mt-12 max-w-none pb-20">
            {!! $content !!}
        </div>
    </article>
@endsection
