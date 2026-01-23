<?php

declare(strict_types=1);

namespace Database\Factories;

use Database\Factories\Traits\ResolvesSchool;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolEvent>
 */
class SchoolEventFactory extends Factory
{
    use ResolvesSchool;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-5 days', '+10 days');
        $end = (clone $start)->modify('+' . fake()->numberBetween(1, 72) . ' hours');

        return [
            'name' => fake()->sentence(3),
            'location' => fake()->city(),
            'starts_at' => $start,
            'ends_at' => $end,
        ];
    }
}
