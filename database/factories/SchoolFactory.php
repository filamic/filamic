<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'address' => fake()->address(),
            'npsn' => fake()->unique()->randomNumber(9),
            'nis_nss_nds' => fake()->unique()->randomNumber(9),
            'telp' => fake()->phoneNumber(),
            'postal_code' => fake()->postcode(),
            'village' => fake()->cityPrefix(),
            'subdistrict' => fake()->citySuffix(),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'website' => fake()->url(),
            'email' => fake()->safeEmail(),
        ];
    }
}
