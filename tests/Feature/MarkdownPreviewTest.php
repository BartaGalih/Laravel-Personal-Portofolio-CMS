<?php

use App\Livewire\MarkdownPreview;
use Livewire\Livewire;

test('the preview renders markdown as html', function (): void {
    Livewire::test(MarkdownPreview::class)
        ->set('markdown', "## Hello\n\n**world**")
        ->assertSee('Hello')
        ->assertSee('world')
        ->assertSeeHtml('<h2>Hello</h2>');
});

test('the preview does not render unsafe html', function (): void {
    Livewire::test(MarkdownPreview::class)
        ->set('markdown', '<script>alert(1)</script>')
        ->assertDontSeeHtml('<script>');
});
