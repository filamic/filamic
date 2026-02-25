<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Student;
use App\Models\StudentPaymentAccount;
use Database\Factories\Traits\ResolvesSchool;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentPaymentAccount>
 */
class StudentPaymentAccountFactory extends Factory
{
    use ResolvesSchool;

    protected $model = StudentPaymentAccount::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'monthly_fee_virtual_account' => fake()->unique()->numerify('########'),
            'book_fee_virtual_account' => fake()->unique()->numerify('########'),
            'monthly_fee_amount' => fake()->numberBetween(100_000, 500_000),
            'book_fee_amount' => fake()->numberBetween(50_000, 200_000),
        ];
    }

    public function withoutMonthlyFee(): static
    {
        return $this->state([
            'monthly_fee_virtual_account' => null,
            'monthly_fee_amount' => 0,
        ]);
    }

    public function withoutBookFee(): static
    {
        return $this->state([
            'book_fee_virtual_account' => null,
            'book_fee_amount' => 0,
        ]);
    }
}
