<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Branch;
use App\Models\ProductItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductStock>
 */
class ProductStockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_item_id' => ProductItem::factory(),
            'branch_id' => Branch::factory(),
            'quantity' => fake()->numberBetween(0, 100),
        ];
    }
}
