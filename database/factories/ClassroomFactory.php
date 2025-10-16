<?php

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
            'name' => $this->faker->word(),
            'school_id' => School::inRandomOrder()->first()->getKey(),
            'grade' => $this->faker->numberBetween(1, 12),
            'phase' => $this->faker->randomElement(['A', 'B', 'C','D']),
            'is_moving_class' => $this->faker->boolean(),
        ];
    }
}
