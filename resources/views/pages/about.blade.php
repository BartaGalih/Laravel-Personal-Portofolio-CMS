@extends('layouts.public')

@section('title', 'About — '.$siteName)
@section('description', $profile?->localized('bio') ?? '')

@section('content')
    <div class="mx-auto max-w-5xl px-6 pt-20">
        <h1 class="font-sans text-4xl font-bold leading-none tracking-tight text-ink sm:text-5xl">About</h1>

        @if ($profile?->localized('bio'))
            <p class="mt-8 max-w-2xl font-serif text-xl leading-relaxed text-ink">
                {{ $profile->localized('bio') }}
            </p>
        @endif
    </div>

    @if ($experiences->isNotEmpty())
        <section class="mx-auto max-w-5xl px-6 pt-20">
            <h2 class="ui-label border-b border-stone pb-4 text-ink">Experience</h2>
            <ul class="mt-8 flex flex-col gap-10">
                @foreach ($experiences as $experience)
                    <li class="grid gap-2 sm:grid-cols-[10rem_1fr] sm:gap-8">
                        <p class="font-sans text-xs text-cloud-dark">
                            {{ $experience->start_date?->format('M Y') }}
                            &ndash;
                            {{ $experience->end_date?->format('M Y') ?? 'Present' }}
                        </p>
                        <div>
                            <p class="font-serif text-xl font-semibold text-ink">
                                {{ $experience->localized('role') }}
                            </p>
                            <p class="font-sans text-sm text-cloud-dark">{{ $experience->localized('company') }}</p>
                            @if ($experience->localized('description'))
                                <p class="mt-3 font-serif text-base leading-relaxed text-ink-soft">
                                    {{ $experience->localized('description') }}
                                </p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($educations->isNotEmpty())
        <section class="mx-auto max-w-5xl px-6 pt-20">
            <h2 class="ui-label border-b border-stone pb-4 text-ink">Education</h2>
            <ul class="mt-8 flex flex-col gap-10">
                @foreach ($educations as $education)
                    <li class="grid gap-2 sm:grid-cols-[10rem_1fr] sm:gap-8">
                        <p class="font-sans text-xs text-cloud-dark">
                            {{ $education->start_date?->format('Y') }}
                            @if ($education->end_date)
                                &ndash; {{ $education->end_date->format('Y') }}
                            @endif
                        </p>
                        <div>
                            <p class="font-serif text-xl font-semibold text-ink">
                                {{ $education->localized('institution') }}
                            </p>
                            <p class="font-sans text-sm text-cloud-dark">
                                {{ collect([
                                    $education->localized('degree'),
                                    $education->localized('field'),
                                    $education->location,
                                ])->filter()->implode(' · ') }}
                            </p>
                            @if ($education->localized('description'))
                                <p class="mt-3 font-serif text-base leading-relaxed text-ink-soft">
                                    {{ $education->localized('description') }}
                                </p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($skills->isNotEmpty())
        <section class="mx-auto max-w-5xl px-6 pt-20">
            <h2 class="ui-label border-b border-stone pb-4 text-ink">Skills</h2>
            <ul class="mt-8 flex flex-wrap gap-2">
                @foreach ($skills as $skill)
                    <li class="rounded-xl border border-stone px-3 py-1 font-sans text-sm text-ink">
                        {{ $skill->name }}
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
@endsection
