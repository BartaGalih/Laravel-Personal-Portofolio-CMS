<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Post> */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title_en' => fake()->sentence(3),
            'title_id' => null,
            'slug' => fake()->unique()->slug(),
            'excerpt_en' => fake()->sentence(),
            'excerpt_id' => null,
            'content_en' => fake()->paragraph(),
            'content_id' => null,
            'status' => ContentStatus::Draft,
            'published_at' => null,
        ];
    }
}
