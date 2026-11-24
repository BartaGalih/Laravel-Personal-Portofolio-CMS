<?php

namespace Database\Factories;

use App\Models\Education;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Education> */
class EducationFactory extends Factory
{
    protected $model = Education::class;

    public function definition(): array
    {
        return [
            'institution_en' => fake()->company().' University',
            'institution_id' => null,
            'degree_en' => fake()->randomElement(['Bachelor', 'Master', 'Diploma']),
            'degree_id' => null,
            'field_en' => fake()->word(),
            'field_id' => null,
            'description_en' => fake()->sentence(),
            'description_id' => null,
            'location' => fake()->city(),
            'start_date' => fake()->date(),
            'end_date' => null,
            'sort_order' => 0,
            'is_visible' => true,
        ];
    }
}
