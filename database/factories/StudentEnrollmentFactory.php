<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\StudentEnrollmentStatusEnum;
use App\Models\Classroom;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentEnrollment>
 */
class StudentEnrollmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'classroom_id' => Classroom::factory(),
            'school_year_id' => fn () => SchoolYear::first() ?? SchoolYear::factory()->create(),
            'student_id' => Student::factory(),
            'status' => StudentEnrollmentStatusEnum::ENROLLED,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (StudentEnrollment $enrollment) {
            if ($enrollment->classroom_id && (! $enrollment->school_id || ! $enrollment->branch_id)) {
                $classroom = Classroom::with('school')->find($enrollment->classroom_id);

                if ($classroom?->school) {
                    $enrollment->school_id ??= $classroom->school_id;
                    $enrollment->branch_id ??= $classroom->school->branch_id;
                }
            }
        });
    }

    public function active(): static
    {
        return $this->state([
            'status' => StudentEnrollmentStatusEnum::ENROLLED,
            'school_year_id' => SchoolYear::getActive()?->id ?? SchoolYear::factory()->active(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state([
            'status' => StudentEnrollmentStatusEnum::INACTIVE,
        ]);
    }

    public function graduated(): static
    {
        return $this->state([
            'status' => StudentEnrollmentStatusEnum::GRADUATED,
        ]);
    }

    public function enrolled(): static
    {
        return $this->state([
            'status' => StudentEnrollmentStatusEnum::ENROLLED,
        ]);
    }
}
