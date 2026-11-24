@php($siteDescription = \App\Support\SiteSettings::metaDescription())
@php($pageTitle = trim($__env->yieldContent('title')) ?: $siteName)
{{-- A page that sets an empty description (no content yet) must still inherit the site default. --}}
@php($pageDescription = trim($__env->yieldContent('description')) ?: $siteDescription)
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    @if (filled($ogImage = \App\Support\SiteSettings::metaImageUrl()))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    @if (filled($favicon = \App\Support\SiteSettings::faviconUrl()))
        <link rel="icon" href="{{ $favicon }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    @foreach (App\Http\Middleware\SetLocale::SUPPORTED_LOCALES as $locale)
        @php($segments = request()->segments())
        @php($segments[0] = $locale)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ url('/'.implode('/', $segments)) }}">
    @endforeach
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-parchment font-serif text-ink antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:bg-card focus:px-4 focus:py-2 focus:text-ink">
        Skip to content
    </a>

    <header class="border-b border-stone">
        <nav class="mx-auto flex max-w-5xl items-center justify-between gap-6 px-6 py-5" aria-label="Main">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}"
               class="ui-label text-ink">
                @if (filled($headerLogo = \App\Support\SiteSettings::headerLogoUrl()))
                    <img src="{{ $headerLogo }}"
                         alt="{{ $siteName }}"
                         class="h-8 w-auto">
                @else
                    {{ $siteName }}
                @endif
            </a>

            <ul class="flex flex-wrap items-center gap-6">
                @foreach (['home' => 'Home', 'about' => 'About', 'projects.index' => 'Projects', 'posts.index' => 'Blog', 'contact' => 'Contact'] as $name => $label)
                    @php($isActive = request()->routeIs($name, str_replace('.index', '.*', $name)))
                    <li>
                        <a href="{{ route($name, ['locale' => app()->getLocale()]) }}"
                           @class([
                               'font-sans text-xs tracking-tight transition-colors',
                               'text-ink' => $isActive,
                               'text-cloud-dark hover:text-ink' => ! $isActive,
                           ])>
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="flex items-center gap-3">
                @foreach (App\Http\Middleware\SetLocale::SUPPORTED_LOCALES as $locale)
                    @php($segments = request()->segments())
                    @php($segments[0] = $locale)
                    <a href="{{ url('/'.implode('/', $segments)) }}"
                       hreflang="{{ $locale }}"
                       @class([
                           'font-sans text-xs tracking-tight transition-colors',
                           'text-ink' => app()->getLocale() === $locale,
                           'text-cloud-dark hover:text-ink' => app()->getLocale() !== $locale,
                       ])>
                        {{ strtoupper($locale) }}
                    </a>
                @endforeach
            </div>
        </nav>
    </header>

    <main id="main">
        @yield('content')
    </main>

    <footer class="mt-24 bg-ink text-card">
        <div class="mx-auto max-w-5xl px-6 py-14">
            <div class="flex flex-wrap justify-between gap-10">
                <div class="max-w-xs">
                    <p class="ui-label text-card">{{ $siteName }}</p>
                    <p class="mt-3 font-serif text-cloud">
                        {{ \App\Support\SiteSettings::tagline() ?? 'A personal portfolio and notebook.' }}
                    </p>
                </div>
                <nav aria-label="Footer">
                    <p class="ui-label text-card">Pages</p>
                    <ul class="mt-3 flex flex-col gap-2">
                        @foreach (['home' => 'Home', 'about' => 'About', 'projects.index' => 'Projects', 'posts.index' => 'Blog', 'contact' => 'Contact'] as $name => $label)
                            <li>
                                <a href="{{ route($name, ['locale' => app()->getLocale()]) }}"
                                   class="font-sans text-xs text-cloud transition-colors hover:text-card">
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
            <p class="mt-12 border-t border-ink-soft pt-6 font-sans text-xs text-cloud">
                &copy; {{ date('Y') }} {{ $siteName }}
            </p>
        </div>
    </footer>
</body>
</html>
