<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SubjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subject_category_id' => SubjectCategory::factory(),
            'name' => fake()->word(),
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
