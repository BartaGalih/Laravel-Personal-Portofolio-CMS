<?php

use App\Support\MarkdownRenderer;

it('renders headings and emphasis', function (): void {
    $html = (new MarkdownRenderer)->render("# Title\n\n**bold** and *italic*");

    expect($html)->toContain('<h1>Title</h1>')
        ->and($html)->toContain('<strong>bold</strong>')
        ->and($html)->toContain('<em>italic</em>');
});

it('strips raw script tags', function (): void {
    $html = (new MarkdownRenderer)->render('<script>alert(1)</script>');

    expect($html)->not->toContain('<script')
        ->and($html)->not->toContain('alert(1)');
});

it('blocks unsafe links but keeps https links', function (): void {
    $html = (new MarkdownRenderer)->render('[x](javascript:alert(1)) [ok](https://example.com)');

    expect($html)->not->toContain('javascript:')
        ->and($html)->toContain('href="https://example.com"');
});
