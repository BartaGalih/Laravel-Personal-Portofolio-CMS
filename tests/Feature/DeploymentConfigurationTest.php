<?php

test('the application defaults to english', function (): void {
    expect(config('app.locale'))->toBe('en')
        ->and(config('app.fallback_locale'))->toBe('en');
});

test('the public disk serves uploaded media without a private root', function (): void {
    $disk = config('filesystems.disks.public');

    expect($disk['driver'])->toBe('local')
        ->and($disk['visibility'])->toBe('public')
        ->and($disk['root'])->toBe(storage_path('app/public'))
        ->and($disk['url'])->toEndWith('/storage');
});

test('the database connection is not sqlite', function (): void {
    expect(config('database.default'))->not->toBe('sqlite');
});

test('the sitemap is published', function (): void {
    $this->get('/sitemap.xml')
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'application/xml');
});
