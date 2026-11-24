<?php

use App\Http\Controllers\PortfolioController;
use App\Http\Middleware\SetLocale;
use App\Models\Post;
use App\Models\Project;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/en');

Route::prefix('{locale}')
    ->whereIn('locale', SetLocale::SUPPORTED_LOCALES)
    ->middleware(SetLocale::class)
    ->group(function (): void {
        Route::get('/', [PortfolioController::class, 'home'])->name('home');
        Route::get('/about', [PortfolioController::class, 'about'])->name('about');
        Route::get('/projects', [PortfolioController::class, 'projects'])->name('projects.index');
        Route::get('/projects/{project}', [PortfolioController::class, 'project'])
            ->where('project', '[a-z0-9-]+')
            ->name('projects.show');
        Route::get('/posts', [PortfolioController::class, 'posts'])->name('posts.index');
        Route::get('/posts/{post}', [PortfolioController::class, 'post'])
            ->where('post', '[a-z0-9-]+')
            ->name('posts.show');
        Route::get('/contact', [PortfolioController::class, 'contact'])->name('contact');
    });

Route::get('/sitemap.xml', function (): Response {
    $urls = collect(['/', '/about', '/projects', '/posts', '/contact'])
        ->merge(Project::posted()->pluck('slug')->map(fn (string $slug): string => "/projects/{$slug}"))
        ->merge(Post::posted()->pluck('slug')->map(fn (string $slug): string => "/posts/{$slug}"));

    $xml = view('sitemap', [
        'urls' => $urls,
        'locales' => SetLocale::SUPPORTED_LOCALES,
    ])->render();

    return response($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');
