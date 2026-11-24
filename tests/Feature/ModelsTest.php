<?php

use App\Enums\ContentStatus;
use App\Models\Post;
use App\Models\Project;

it('returns only posted projects and posts', function (): void {
    Project::factory()->create(['status' => ContentStatus::Draft]);
    $project = Project::factory()->create(['status' => ContentStatus::Posted]);
    Post::factory()->create(['status' => ContentStatus::Archived]);
    $post = Post::factory()->create(['status' => ContentStatus::Posted]);

    expect(Project::posted()->pluck('id'))->toContain($project->id)
        ->and(Post::posted()->pluck('id'))->toContain($post->id)
        ->and(Project::posted()->count())->toBe(1)
        ->and(Post::posted()->count())->toBe(1);
});

it('falls back to english when an indonesian field is empty', function (): void {
    $project = Project::factory()->create([
        'title_en' => 'English title',
        'title_id' => null,
    ]);

    expect($project->localized('title', 'id'))->toBe('English title')
        ->and($project->localized('title', 'en'))->toBe('English title');
});

it('uses the indonesian value when it exists', function (): void {
    $project = Project::factory()->create([
        'title_en' => 'English title',
        'title_id' => 'Judul Indonesia',
    ]);

    expect($project->localized('title', 'id'))->toBe('Judul Indonesia');
});
