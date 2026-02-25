<?php

declare(strict_types=1);

namespace Database\Factories;

use Database\Factories\Traits\ResolvesSchool;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LearningGroup>
 */
class LearningGroupFactory extends Factory
{
    use ResolvesSchool;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
        ];
    }
}
