<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use App\Support\MarkdownRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;

class PortfolioController extends Controller
{
    public function __construct(private readonly MarkdownRenderer $markdown) {}

    public function home(): View
    {
        return view('pages.home', [
            'profile' => Profile::first(),
            'projects' => Project::posted()->orderBy('sort_order')->limit(3)->get(),
            'posts' => Post::posted()->latest('published_at')->limit(3)->get(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'profile' => Profile::first(),
            'educations' => Education::where('is_visible', true)->orderBy('sort_order')->get(),
            'experiences' => Experience::where('is_visible', true)->orderByDesc('start_date')->get(),
            'skills' => Skill::where('is_visible', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function projects(): View
    {
        return view('pages.projects.index', [
            'projects' => Project::posted()->orderBy('sort_order')->get(),
        ]);
    }

    public function project(string $locale, string $project): View
    {
        $project = Project::posted()->where('slug', $project)->firstOrFail();

        return view('pages.projects.show', [
            'project' => $project,
            'content' => $this->markdown->render($project->localized('content', App::getLocale())),
        ]);
    }

    public function posts(): View
    {
        return view('pages.posts.index', [
            'posts' => Post::posted()->latest('published_at')->get(),
        ]);
    }

    public function post(string $locale, string $post): View
    {
        $post = Post::posted()->where('slug', $post)->firstOrFail();

        return view('pages.posts.show', [
            'post' => $post,
            'content' => $this->markdown->render($post->localized('content', App::getLocale())),
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact');
    }
}
