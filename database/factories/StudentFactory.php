<?php

declare(strict_types=1);

namespace Database\Factories;

use Database\Factories\Traits\ResolveBranch;
use Database\Factories\Traits\ResolvesSchool;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    use ResolveBranch;
    use ResolvesSchool;

    public function configure(): self
    {
        return parent::configure()
            ->forSchool();
    }

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'gender' => \App\Enums\GenderEnum::MALE,
            'is_active' => true,
            'birth_place' => $this->faker->city(),
            'birth_date' => $this->faker->date(),
            'nisn' => $this->faker->unique()->numerify('##########'),
            'nis' => $this->faker->numerify('#####'),
        ];
    }
}
