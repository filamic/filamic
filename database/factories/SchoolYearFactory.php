<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolYear>
 */
class SchoolYearFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-10 years', '+10 years');
        $endDate = (clone $startDate)->modify('+1 year');

        $startYear = (int) $startDate->format('Y');

        return [
            'name' => $startYear . '/' . ($startYear + 1),
            'semester' => fake()->numberBetween(1, 2),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => fake()->boolean(),
        ];
    }

    public function active(): static
    {
        return $this->state([
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state([
            'is_active' => false,
        ]);
    }

    public function semester(int $semester): static
    {
        return $this->state([
            'semester' => $semester,
        ]);
    }
}
