<?php

use App\Enums\ContentStatus;
use App\Models\Education;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;

test('the root redirects to the default english locale', function (): void {
    $this->get('/')->assertRedirect('/en');
});

test('both locales render the home page', function (string $locale): void {
    $this->get("/{$locale}")->assertSuccessful();
})->with(['en', 'id']);

test('an unsupported locale is not routed', function (): void {
    $this->get('/fr')->assertNotFound();
});

test('the public layout loads the self-hosted fonts', function (): void {
    $html = $this->get('/en')->assertSuccessful()->getContent();

    expect(substr_count($html, '@font-face'))->toBeGreaterThan(0)
        ->and($html)->toContain('Instrument Sans')
        ->and($html)->toContain('Source Serif 4')
        ->and($html)->not->toContain('fonts.bunny.net')
        ->and($html)->not->toContain('fonts.googleapis.com');
});

test('public assets use the forwarded https scheme', function (): void {
    $html = $this->withHeader('X-Forwarded-Proto', 'https')
        ->get('/en')
        ->assertSuccessful()
        ->getContent();

    expect($html)
        ->toContain('https://localhost/build/')
        ->not->toContain('http://localhost/build/');
});

test('all public pages render', function (string $path): void {
    $this->get($path)->assertSuccessful();
})->with([
    '/en/about',
    '/en/projects',
    '/en/posts',
    '/en/contact',
    '/id/about',
    '/id/projects',
    '/id/posts',
    '/id/contact',
]);

test('only posted projects are listed publicly', function (): void {
    Project::factory()->create(['status' => ContentStatus::Posted, 'title_en' => 'Visible Project']);
    Project::factory()->create(['status' => ContentStatus::Draft, 'title_en' => 'Hidden Draft']);
    Project::factory()->create(['status' => ContentStatus::Archived, 'title_en' => 'Hidden Archive']);

    $this->get('/en/projects')
        ->assertSee('Visible Project')
        ->assertDontSee('Hidden Draft')
        ->assertDontSee('Hidden Archive');
});

test('a draft project detail page is not reachable', function (): void {
    $project = Project::factory()->create([
        'status' => ContentStatus::Draft,
        'slug' => 'draft-project',
    ]);

    $this->get("/en/projects/{$project->slug}")->assertNotFound();
});

test('a posted project detail page is reachable', function (): void {
    $project = Project::factory()->create([
        'status' => ContentStatus::Posted,
        'slug' => 'live-project',
        'title_en' => 'Live Project',
        'content_en' => '# Heading',
    ]);

    $this->get("/en/projects/{$project->slug}")
        ->assertSuccessful()
        ->assertSee('Live Project')
        ->assertSeeHtml('<h1>Heading</h1>');
});

test('only posted posts are listed publicly', function (): void {
    Post::factory()->create(['status' => ContentStatus::Posted, 'title_en' => 'Visible Post']);
    Post::factory()->create(['status' => ContentStatus::Draft, 'title_en' => 'Hidden Post']);

    $this->get('/en/posts')
        ->assertSee('Visible Post')
        ->assertDontSee('Hidden Post');
});

test('an empty indonesian field falls back to english', function (): void {
    Project::factory()->create([
        'status' => ContentStatus::Posted,
        'title_en' => 'English Only Title',
        'title_id' => null,
    ]);

    $this->get('/id/projects')->assertSee('English Only Title');
});

test('an indonesian translation is shown on the indonesian page', function (): void {
    Project::factory()->create([
        'status' => ContentStatus::Posted,
        'title_en' => 'English Title',
        'title_id' => 'Judul Indonesia',
    ]);

    $this->get('/id/projects')
        ->assertSee('Judul Indonesia')
        ->assertDontSee('English Title');
});

test('visible education history appears on the about page', function (): void {
    Education::factory()->create([
        'institution_en' => 'Visible University',
        'is_visible' => true,
    ]);
    Education::factory()->create([
        'institution_en' => 'Hidden University',
        'is_visible' => false,
    ]);

    $this->get('/en/about')
        ->assertSee('Visible University')
        ->assertDontSee('Hidden University');
});

test('the language switcher keeps the current page', function (): void {
    $this->get('/en/projects')->assertSee('/id/projects');
});

test('uploaded media is served from the public disk', function (): void {
    Project::factory()->create([
        'status' => ContentStatus::Posted,
        'slug' => 'with-cover',
        'cover_image_path' => 'covers/example.jpg',
    ]);
    Post::factory()->create([
        'status' => ContentStatus::Posted,
        'slug' => 'with-cover',
        'cover_image_path' => 'covers/example.jpg',
    ]);

    $this->get('/en/projects/with-cover')
        ->assertSuccessful()
        ->assertSee('/storage/covers/example.jpg');

    $this->get('/en/posts/with-cover')
        ->assertSuccessful()
        ->assertSee('/storage/covers/example.jpg');
});

test('the home page renders the profile', function (): void {
    Profile::create([
        'name_en' => 'Barta Galih',
        'name_id' => null,
        'bio_en' => 'Builder of things.',
        'photo_path' => 'profile/me.jpg',
    ]);

    $this->get('/en')
        ->assertSuccessful()
        ->assertSee('Barta Galih')
        ->assertSee('Builder of things.')
        ->assertSee('/storage/profile/me.jpg');

    $this->get('/id')
        ->assertSuccessful()
        ->assertSee('Barta Galih');
});

test('the sitemap only contains posted content', function (): void {
    Project::factory()->create(['status' => ContentStatus::Posted, 'slug' => 'posted-one']);
    Project::factory()->create(['status' => ContentStatus::Draft, 'slug' => 'draft-one']);

    $this->get('/sitemap.xml')
        ->assertSuccessful()
        ->assertSee('posted-one')
        ->assertDontSee('draft-one');
});
