<?php

use App\Models\User;

test('guests are redirected from the admin panel', function (): void {
    $this->get('/admin')->assertRedirect('/admin/login');
});

test('the administrator can open the content resources', function (string $path): void {
    $this->actingAs(User::factory()->create())
        ->get($path)
        ->assertSuccessful();
})->with([
    '/admin/projects',
    '/admin/posts',
    '/admin/education',
    '/admin/experiences',
    '/admin/skills',
    '/admin/contact-messages',
]);

test('the admin panel uses the brand palette and self-hosted fonts', function (): void {
    $html = $this->get('/admin/login')->assertSuccessful()->getContent();

    // Clay accent and warm neutral ramp, not Filament's default amber/zinc.
    expect($html)
        ->toContain('--primary-500:oklch')
        ->toContain('--gray-50:oklch')
        ->not->toContain('fonts.bunny.net')
        ->not->toContain('fonts.googleapis.com');

    expect(substr_count($html, '@font-face'))->toBeGreaterThan(0);
});

test('the admin panel is pinned to light mode like the public site', function (): void {
    $html = $this->get('/admin/login')->assertSuccessful()->getContent();

    /*
     * darkMode(false) emits a script that pins Filament's theme mode to light.
     * Asserting on that is meaningful; the theme switcher only renders in the
     * authenticated user menu, so checking for its absence here proves nothing.
     */
    expect($html)
        ->toContain("localStorage.setItem('filament:theme:mode', 'light')")
        ->not->toContain('loadDarkMode');
});
