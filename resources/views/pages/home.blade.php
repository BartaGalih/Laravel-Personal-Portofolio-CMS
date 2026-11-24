@extends('layouts.public')

@section('title', $profile?->localized('name') ?? $siteName)
@section('description', $profile?->localized('bio') ?? '')

@section('content')
    {{-- Asymmetric first screen: declarative sans heading beside editorial serif prose. --}}
    <section class="mx-auto max-w-5xl px-6 pt-24 pb-20">
        <div class="grid gap-12 lg:grid-cols-2 lg:gap-16">
            <div>
                <h1 class="font-sans text-4xl font-bold leading-none tracking-tight text-ink sm:text-5xl lg:text-6xl">
                    {{ $profile?->localized('name') ?? $siteName }}
                </h1>
                <p class="ui-label mt-6 text-cloud-dark">Portfolio &amp; notebook</p>
            </div>
            <div class="lg:pt-2">
                @if ($profile?->localized('bio'))
                    <p class="font-serif text-lg leading-relaxed text-ink">
                        {{ $profile->localized('bio') }}
                    </p>
                @endif
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('projects.index', ['locale' => app()->getLocale()]) }}" class="btn-filled">
                        See my work
                    </a>
                    <a href="{{ route('contact', ['locale' => app()->getLocale()]) }}"
                       class="font-sans text-sm text-ink underline underline-offset-4">
                        Get in touch
                    </a>
                </div>
            </div>
        </div>

        @if ($profile?->photo_path)
            <img src="{{ Storage::disk('public')->url($profile->photo_path) }}"
                 alt="{{ $profile->localized('name') }}"
                 class="mt-16 h-64 w-full rounded-3xl object-cover">
        @endif
    </section>

    @if ($projects->isNotEmpty())
        <section class="mx-auto max-w-5xl px-6 pb-20">
            <div class="mb-8 flex items-baseline justify-between gap-4 border-b border-stone pb-4">
                <h2 class="ui-label text-ink">Selected work</h2>
                <a href="{{ route('projects.index', ['locale' => app()->getLocale()]) }}"
                   class="font-sans text-xs text-cloud-dark transition-colors hover:text-ink">
                    All projects
                </a>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($posts->isNotEmpty())
        <section class="mx-auto max-w-5xl px-6 pb-20">
            <div class="mb-8 flex items-baseline justify-between gap-4 border-b border-stone pb-4">
                <h2 class="ui-label text-ink">Latest writing</h2>
                <a href="{{ route('posts.index', ['locale' => app()->getLocale()]) }}"
                   class="font-sans text-xs text-cloud-dark transition-colors hover:text-ink">
                    All posts
                </a>
            </div>
            <ul class="grid gap-6 sm:grid-cols-3">
                @foreach ($posts as $post)
                    <li class="rounded-3xl bg-card p-6">
                        <a href="{{ route('posts.show', ['locale' => app()->getLocale(), 'post' => $post->slug]) }}"
                           class="font-serif text-xl font-semibold leading-snug text-ink underline underline-offset-4">
                            {{ $post->localized('title') }}
                        </a>
                        @if ($post->published_at)
                            <p class="mt-3 font-sans text-xs text-cloud-dark">
                                {{ $post->published_at->format('j F Y') }}
                            </p>
                        @endif
                        @if ($post->localized('excerpt'))
                            <p class="mt-3 font-serif text-base text-ink-soft">
                                {{ $post->localized('excerpt') }}
                            </p>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
@endsection
