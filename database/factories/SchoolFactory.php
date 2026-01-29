<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LevelEnum;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Context;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    public function configure(): self
    {
        return parent::configure()
            ->forBranch();
    }

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'level' => fake()->randomElement(LevelEnum::cases())->value,
            'address' => fake()->address(),
            'npsn' => fake()->unique()->randomNumber(9),
            'nis_nss_nds' => fake()->unique()->randomNumber(9),
            'telp' => fake()->numerify('08##########'),
            'postal_code' => fake()->postcode(),
            'village' => fake()->cityPrefix(),
            'subdistrict' => fake()->citySuffix(),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'website' => fake()->url(),
            'email' => fake()->safeEmail(),
        ];
    }

    public function forBranch(?Branch $branch = null): self
    {
        $branch ??= Context::get('branch') ?? Branch::factory();

        return $this->for($branch);
    }
}
