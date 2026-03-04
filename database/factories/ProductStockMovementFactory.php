<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\StockMovementTypeEnum;
use App\Models\Branch;
use App\Models\ProductItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductStockMovement>
 */
class ProductStockMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'branch_id' => Branch::factory(),
            'product_item_id' => ProductItem::factory(),
            'type' => StockMovementTypeEnum::STOCK_IN,
            'quantity' => fake()->numberBetween(1, 50),
            'purchase_price' => fake()->numberBetween(10000, 100000),
            'sale_price' => fake()->numberBetween(15000, 150000),
            'transaction_date' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
