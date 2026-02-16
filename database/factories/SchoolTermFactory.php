<?php

declare(strict_types=1);

namespace Database\Factories;

use Database\Factories\Traits\HasActiveState;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolTerm>
 */
class SchoolTermFactory extends Factory
{
    use HasActiveState;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([1, 2]),
            'is_active' => fake()->boolean(),
        ];
    }

    public function odd(): static
    {
        return $this->state([
            'name' => 1,
        ]);
    }

    public function even(): static
    {
        return $this->state([
            'name' => 2,
        ]);
    }
}
