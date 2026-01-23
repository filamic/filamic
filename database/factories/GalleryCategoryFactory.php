<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GalleryCategory>
 */
class GalleryCategoryFactory extends Factory
{   
    public function definition(): array
    {
        $name = fake()->word();
        return [
            'name' => $name,
            'slug' => str($name)->slug(),
        ];
    }
}
