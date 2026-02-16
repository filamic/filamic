<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'phone' => fake()->optional()->phoneNumber(),
            'whatsapp' => fake()->optional()->e164PhoneNumber(),
            'address' => fake()->optional()->address(),
        ];
    }
}
