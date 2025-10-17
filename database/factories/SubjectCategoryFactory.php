<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubjectCategory>
 */
class SubjectCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'name' => fake()->word(),
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
