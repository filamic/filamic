<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\StudentEnrollmentStatusEnum;
use App\Models\Classroom;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use App\Models\Student;
use Database\Factories\Traits\ResolveBranch;
use Database\Factories\Traits\ResolvesSchool;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentEnrollment>
 */
class StudentEnrollmentFactory extends Factory
{
    // use ResolveBranch;
    // use ResolvesSchool;

    public function definition(): array
    {
        return [
            'classroom_id' => Classroom::factory(),
            'school_year_id' => fn () => SchoolYear::first() ?? SchoolYear::factory()->create(),
            'school_term_id' => fn () => SchoolTerm::first() ?? SchoolTerm::factory()->create(),
            'student_id' => Student::factory(),
            'status' => StudentEnrollmentStatusEnum::ENROLLED,
        ];
    }

    public function active(): static
    {
        return $this->state([
            'status' => StudentEnrollmentStatusEnum::ENROLLED,
            'school_year_id' => SchoolYear::getActive()?->id ?? SchoolYear::factory()->active(),
            'school_term_id' => SchoolTerm::getActive()?->id ?? SchoolTerm::factory()->active(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state([
            'status' => StudentEnrollmentStatusEnum::MOVED_EXTERNAL,
        ]);
    }

    public function enrolled(): static
    {
        return $this->state([
            'status' => StudentEnrollmentStatusEnum::ENROLLED,
        ]);
    }
}
