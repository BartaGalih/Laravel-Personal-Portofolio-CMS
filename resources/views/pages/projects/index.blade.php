@extends('layouts.public')

@section('title', 'Projects — '.$siteName)

@section('content')
    <div class="mx-auto max-w-5xl px-6 pt-20">
        <h1 class="font-sans text-4xl font-bold leading-none tracking-tight text-ink sm:text-5xl">Projects</h1>
    </div>

    <div class="mx-auto max-w-5xl px-6 pt-12 pb-20">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($projects as $project)
                <x-project-card :project="$project" />
            @empty
                <p class="font-serif text-lg text-cloud-dark">No projects yet.</p>
            @endforelse
        </div>
    </div>
@endsection
