<article class="flex h-full flex-col rounded-3xl bg-card p-6">
    <a href="{{ route('projects.show', ['locale' => app()->getLocale(), 'project' => $project->slug]) }}"
       class="font-serif text-xl font-semibold leading-snug text-ink underline underline-offset-4">
        {{ $project->localized('title') }}
    </a>
    @if ($project->localized('summary'))
        <p class="mt-3 font-serif text-base leading-relaxed text-ink-soft">
            {{ Str::limit($project->localized('summary'), 140) }}
        </p>
    @endif
</article>
