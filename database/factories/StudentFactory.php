<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\GenderEnum;
use App\Enums\ReligionEnum;
use App\Enums\StatusInFamilyEnum;
use Database\Factories\Traits\HasActiveState;
use Database\Factories\Traits\ResolveBranch;
use Database\Factories\Traits\ResolvesSchool;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    use HasActiveState;
    use ResolveBranch;
    use ResolvesSchool;

    public function configure(): self
    {
        return parent::configure()
            ->forSchool()
            ->forBranch();
    }

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'gender' => fake()->randomElement(GenderEnum::cases()),
            'status_in_family' => fake()->randomElement(StatusInFamilyEnum::cases()),
            'religion' => fake()->randomElement(ReligionEnum::cases()),
            'is_active' => fake()->boolean(),
            'birth_place' => $this->faker->city(),
            'birth_date' => $this->faker->date(),
            'nisn' => $this->faker->unique()->numerify('##########'),
            'nis' => $this->faker->numerify('#####'),
        ];
    }
}
