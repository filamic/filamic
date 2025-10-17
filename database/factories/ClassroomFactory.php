<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class ClassroomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'school_id' => School::factory(),
            'grade' => fake()->numberBetween(1, 12),
            'phase' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'is_moving_class' => fake()->boolean(),
        ];
    }

    public function forSchool(?School $school = null): static
    {
        return $this->for($school ?? School::factory(), 'school');
    }

    public function forGrade(int $grade): static
    {
        return $this->state(fn (array $attributes) => [
            'grade' => $grade,
        ]);
    }

    public function forPhase(string $phase): static
    {
        return $this->state(fn (array $attributes) => [
            'phase' => $phase,
        ]);
    }

    public function setAsMovingClass(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_moving_class' => true,
        ]);
    }

    public function setAsRegularClass(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_moving_class' => false,
        ]);
    }
}
