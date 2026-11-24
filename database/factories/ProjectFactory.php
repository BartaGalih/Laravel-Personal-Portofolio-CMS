<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Project> */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title_en' => $title,
            'title_id' => null,
            'slug' => fake()->unique()->slug(),
            'summary_en' => fake()->sentence(),
            'summary_id' => null,
            'content_en' => fake()->paragraph(),
            'content_id' => null,
            'status' => ContentStatus::Draft,
            'sort_order' => 0,
        ];
    }
}
