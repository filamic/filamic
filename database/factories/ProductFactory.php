<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\GradeEnum;
use App\Enums\LevelEnum;
use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $level = fake()->optional(0.8)->randomElement(LevelEnum::cases());
        $grades = $level ? GradeEnum::forLevel($level) : [];

        return [
            'supplier_id' => Supplier::factory(),
            'product_category_id' => ProductCategory::factory(),
            'level' => $level,
            'grade' => $grades ? fake()->randomElement($grades) : null,
            'name' => fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
