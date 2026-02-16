<?php

declare(strict_types=1);

namespace Database\Factories;

use Database\Factories\Traits\HasActiveState;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolYear>
 */
class SchoolYearFactory extends Factory
{
    use HasActiveState;

    public function definition(): array
    {
        $startYear = fake()->unique()->numberBetween(2000, 2090);
        $startDate = Carbon::create($startYear, 7, 1);
        $endDate = Carbon::create($startYear + 1, 6, 30);

        return [
            'start_year' => $startYear,
            'end_year' => $startYear + 1,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => fake()->boolean(),
        ];
    }
}
