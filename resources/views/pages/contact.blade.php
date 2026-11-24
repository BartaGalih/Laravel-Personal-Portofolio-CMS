@extends('layouts.public')

@section('title', 'Contact — '.$siteName)

@section('content')
    <div class="mx-auto max-w-3xl px-6 pt-20 pb-20">
        <h1 class="font-sans text-4xl font-bold leading-none tracking-tight text-ink sm:text-5xl">Contact</h1>
        <p class="mt-6 font-serif text-xl leading-relaxed text-ink-soft">
            Get in touch by filling in the form below.
        </p>

        <div class="mt-12">
            <livewire:contact-form />
        </div>
    </div>
@endsection
