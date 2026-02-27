<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ProductVariation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariationOption>
 */
class ProductVariationOptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_variation_id' => ProductVariation::factory(),
            'name' => fake()->word(),
        ];
    }
}
