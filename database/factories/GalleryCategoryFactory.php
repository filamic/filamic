<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GalleryCategory>
 */
class GalleryCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'slug' => fn (array $attributes): string => str($attributes['name'])->slug()
                ->append('-' . fake()->unique()->numerify('####'))
                ->value(),
        ];
    }
}
