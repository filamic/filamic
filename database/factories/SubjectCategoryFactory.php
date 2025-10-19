<?php

declare(strict_types=1);

namespace Database\Factories;

use Database\Factories\Traits\ResolvesSchool;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubjectCategory>
 */
class SubjectCategoryFactory extends Factory
{
    use ResolvesSchool;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
