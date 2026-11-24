<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
    @foreach ($urls as $path)
        @foreach ($locales as $locale)
            <url>
                <loc>{{ url("/{$locale}{$path}") }}</loc>
                @foreach ($locales as $alternate)
                    <xhtml:link rel="alternate" hreflang="{{ $alternate }}" href="{{ url("/{$alternate}{$path}") }}" />
                @endforeach
            </url>
        @endforeach
    @endforeach
</urlset>
