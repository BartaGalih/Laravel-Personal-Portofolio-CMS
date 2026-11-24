@extends('layouts.public')

@section('title', 'Blog — '.$siteName)

@section('content')
    <div class="mx-auto max-w-5xl px-6 pt-20">
        <h1 class="font-sans text-4xl font-bold leading-none tracking-tight text-ink sm:text-5xl">Blog</h1>
    </div>

    <div class="mx-auto max-w-3xl px-6 pt-12 pb-20">
        <ul class="flex flex-col">
            @forelse ($posts as $post)
                <li class="border-b border-stone py-8 first:pt-0 last:border-b-0">
                    @if ($post->published_at)
                        <p class="font-sans text-xs text-cloud-dark">{{ $post->published_at->format('j F Y') }}</p>
                    @endif
                    <a href="{{ route('posts.show', ['locale' => app()->getLocale(), 'post' => $post->slug]) }}"
                       class="mt-2 block font-serif text-2xl font-semibold leading-snug text-ink underline underline-offset-4">
                        {{ $post->localized('title') }}
                    </a>
                    @if ($post->localized('excerpt'))
                        <p class="mt-3 font-serif text-lg leading-relaxed text-ink-soft">
                            {{ $post->localized('excerpt') }}
                        </p>
                    @endif
                </li>
            @empty
                <li class="font-serif text-lg text-cloud-dark">No posts yet.</li>
            @endforelse
        </ul>
    </div>
@endsection
