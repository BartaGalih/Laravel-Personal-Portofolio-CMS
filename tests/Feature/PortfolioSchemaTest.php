<?php

use Illuminate\Support\Facades\Schema;

test('portfolio tables expose the required content fields', function (): void {
    expect(Schema::hasTable('profiles'))->toBeTrue()
        ->and(Schema::hasTable('projects'))->toBeTrue()
        ->and(Schema::hasTable('posts'))->toBeTrue()
        ->and(Schema::hasTable('skills'))->toBeTrue()
        ->and(Schema::hasTable('experiences'))->toBeTrue()
        ->and(Schema::hasTable('educations'))->toBeTrue()
        ->and(Schema::hasTable('contact_messages'))->toBeTrue()
        ->and(Schema::hasTable('settings'))->toBeTrue();

    expect(Schema::hasColumns('projects', ['title_en', 'title_id', 'content_en', 'content_id', 'slug', 'status']))->toBeTrue()
        ->and(Schema::hasColumns('posts', ['title_en', 'title_id', 'content_en', 'content_id', 'slug', 'status']))->toBeTrue()
        ->and(Schema::hasColumns('educations', ['institution_en', 'institution_id', 'degree_en', 'degree_id']))->toBeTrue()
        ->and(Schema::hasColumns('contact_messages', ['email', 'message', 'is_read']))->toBeTrue();
});
