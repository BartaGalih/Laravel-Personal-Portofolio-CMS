<?php

use App\Filament\Pages\ManageProfile;
use App\Filament\Resources\Education\Pages\CreateEducation;
use App\Filament\Resources\Experiences\Pages\CreateExperience;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Skills\Pages\CreateSkill;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Setting;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

test('every admin resource list renders', function (string $path): void {
    $this->get($path)->assertSuccessful();
})->with([
    '/admin/projects',
    '/admin/projects/create',
    '/admin/posts',
    '/admin/posts/create',
    '/admin/education',
    '/admin/education/create',
    '/admin/experiences',
    '/admin/experiences/create',
    '/admin/skills',
    '/admin/skills/create',
    '/admin/contact-messages',
    '/admin/manage-settings',
    '/admin/manage-profile',
]);

/*
 * The create/edit root form schema is a 2-column grid, and an unspanned child
 * only occupies the first column — leaving half the page empty on desktop.
 */
test('the resource forms fill the page width', function (string $page): void {
    $grid = Livewire::test($page)->instance()->getSchema('form')->getComponents()[0];

    expect($grid->getColumnSpan('default'))->toBe('full');
})->with([
    CreateProject::class,
    CreatePost::class,
    CreateEducation::class,
    CreateExperience::class,
    CreateSkill::class,
]);

test('the edit pages of existing content render', function (): void {
    $project = Project::factory()->create();
    $post = Post::factory()->create();
    $education = Education::factory()->create();

    $this->get("/admin/projects/{$project->getKey()}/edit")->assertSuccessful();
    $this->get("/admin/posts/{$post->getKey()}/edit")->assertSuccessful();
    $this->get("/admin/education/{$education->getKey()}/edit")->assertSuccessful();
});

test('a project saves its bilingual fields and slug', function (): void {
    Livewire::test(CreateProject::class)
        ->fillForm([
            'title_en' => 'My Project',
            'title_id' => 'Proyek Saya',
            'slug' => 'my-project',
            'content_en' => '# Hello',
            'content_id' => '# Halo',
            'status' => 'posted',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $project = Project::sole();

    expect($project->title_en)->toBe('My Project')
        ->and($project->title_id)->toBe('Proyek Saya')
        ->and($project->slug)->toBe('my-project')
        ->and($project->status->value)->toBe('posted');
});

test('education and experience forms persist their fields', function (): void {
    Livewire::test(CreateEducation::class)
        ->fillForm([
            'institution_en' => 'State University',
            'institution_id' => 'Universitas Negeri',
            'degree_en' => 'BSc',
            'field_en' => 'Computer Science',
            'location' => 'Jakarta',
            'start_date' => '2018-09-01',
            'end_date' => '2022-07-01',
            'sort_order' => 1,
            'is_visible' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $education = Education::sole();

    expect($education->institution_en)->toBe('State University')
        ->and($education->degree_en)->toBe('BSc')
        ->and($education->start_date->format('Y-m-d'))->toBe('2018-09-01')
        ->and($education->is_visible)->toBeTrue();

    Livewire::test(CreateExperience::class)
        ->fillForm([
            'company_en' => 'Acme',
            'role_en' => 'Engineer',
            'start_date' => '2022-08-01',
            'sort_order' => 1,
            'is_visible' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Experience::sole()->company_en)->toBe('Acme');
});

test('the profile page stores identity and social links', function (): void {
    Livewire::test(ManageProfile::class)
        ->fillForm([
            'name_en' => 'Barta Galih',
            'name_id' => 'Barta Galih',
            'bio_en' => 'Builder.',
            'email' => 'me@example.com',
            'social_links' => [
                ['label' => 'GitHub', 'url' => 'https://github.com/example'],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $profile = Profile::sole();

    expect($profile->name_en)->toBe('Barta Galih')
        ->and($profile->social_links)->toEqual([
            ['label' => 'GitHub', 'url' => 'https://github.com/example'],
        ]);
});

test('saving the profile twice updates rather than duplicates', function (): void {
    Livewire::test(ManageProfile::class)
        ->fillForm(['name_en' => 'First'])
        ->call('save');

    Livewire::test(ManageProfile::class)
        ->fillForm(['name_en' => 'Second'])
        ->call('save');

    expect(Profile::count())->toBe(1)
        ->and(Profile::sole()->name_en)->toBe('Second');
});

test('settings can be read and written through the model', function (): void {
    Setting::put('site.tagline', ['en' => 'Hello', 'id' => 'Halo']);

    expect(Setting::get('site.tagline'))->toBe(['en' => 'Hello', 'id' => 'Halo'])
        ->and(Setting::get('missing', 'fallback'))->toBe('fallback');
});

test('put updates an existing setting instead of duplicating it', function (): void {
    Setting::put('site.tagline', ['en' => 'First']);
    Setting::put('site.tagline', ['en' => 'Second']);

    expect(Setting::query()->where('key', 'site.tagline')->count())->toBe(1)
        ->and(Setting::get('site.tagline'))->toBe(['en' => 'Second']);
});
