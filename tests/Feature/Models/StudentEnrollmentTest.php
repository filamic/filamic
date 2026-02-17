<?php

declare(strict_types=1);

use App\Enums\StudentEnrollmentStatusEnum;
use App\Models\Classroom;
use App\Models\School;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentEnrollment;

it('casts status to StudentEnrollmentStatusEnum', function () {
    // Arrange
    $enrollment = StudentEnrollment::factory()->create();

    // Act & Assert
    expect($enrollment->status)->toBeInstanceOf(StudentEnrollmentStatusEnum::class);
});

it('auto-fills branch_id and school_id from student school on creating', function () {
    // Arrange
    $school = School::factory()->create();
    $student = Student::factory()->for($school)->create();
    $classroom = Classroom::factory()->for($school)->create();
    $schoolYear = SchoolYear::factory()->create();
    $schoolTerm = SchoolTerm::factory()->create();

    // Act
    $enrollment = StudentEnrollment::create([
        'classroom_id' => $classroom->id,
        'school_year_id' => $schoolYear->id,
        'school_term_id' => $schoolTerm->id,
        'student_id' => $student->id,
        'status' => StudentEnrollmentStatusEnum::ENROLLED,
    ]);

    // Assert
    expect($enrollment->branch_id)->toBe($school->branch_id)
        ->and($enrollment->school_id)->toBe($school->id);
});

it('triggers syncActiveStatus on student when enrollment is created', function () {
    // Arrange
    $activeYear = SchoolYear::first() ?? SchoolYear::factory()->active()->create();
    $activeTerm = SchoolTerm::first() ?? SchoolTerm::factory()->odd()->active()->create();
    $school = School::factory()->create();
    $student = Student::factory()->for($school)->create(['is_active' => false]);
    $classroom = Classroom::factory()->for($school)->create();

    // Act
    StudentEnrollment::create([
        'classroom_id' => $classroom->id,
        'school_year_id' => $activeYear->id,
        'school_term_id' => $activeTerm->id,
        'student_id' => $student->id,
        'status' => StudentEnrollmentStatusEnum::ENROLLED,
    ]);

    // Assert
    expect($student->refresh()->is_active)->toBeTrue();
});

it('triggers syncActiveStatus on student when enrollment status changes', function () {
    // Arrange
    $activeYear = SchoolYear::first() ?? SchoolYear::factory()->active()->create();
    $activeTerm = SchoolTerm::first() ?? SchoolTerm::factory()->odd()->active()->create();
    $school = School::factory()->create();
    $student = Student::factory()->for($school)->create(['is_active' => true]);
    $classroom = Classroom::factory()->for($school)->create();

    $enrollment = StudentEnrollment::create([
        'classroom_id' => $classroom->id,
        'school_year_id' => $activeYear->id,
        'school_term_id' => $activeTerm->id,
        'student_id' => $student->id,
        'status' => StudentEnrollmentStatusEnum::ENROLLED,
    ]);

    // Act — change status to inactive
    $enrollment->update(['status' => StudentEnrollmentStatusEnum::MOVED_EXTERNAL]);

    // Assert
    expect($student->refresh()->is_active)->toBeFalse();
});

it('active scope returns enrollments matching active year term and status', function () {
    // Arrange
    $activeYear = SchoolYear::first() ?? SchoolYear::factory()->active()->create();
    $activeTerm = SchoolTerm::first() ?? SchoolTerm::factory()->odd()->active()->create();
    $inactiveYear = SchoolYear::factory()->inactive()->create();

    $activeEnrollment = StudentEnrollment::factory()->create([
        'school_year_id' => $activeYear->id,
        'school_term_id' => $activeTerm->id,
        'status' => StudentEnrollmentStatusEnum::ENROLLED,
    ]);
    StudentEnrollment::factory()->create([
        'school_year_id' => $inactiveYear->id,
        'school_term_id' => $activeTerm->id,
        'status' => StudentEnrollmentStatusEnum::ENROLLED,
    ]);

    // Act
    $result = StudentEnrollment::active()->get();

    // Assert
    expect($result)
        ->toHaveCount(1)
        ->first()->id->toBe($activeEnrollment->id);
});

it('active scope returns empty when no active school year exists', function () {
    // Arrange
    $inactiveYear = SchoolYear::factory()->inactive()->create();
    SchoolYear::query()->update(['is_active' => false]);

    $activeTerm = SchoolTerm::first() ?? SchoolTerm::factory()->odd()->active()->create();

    StudentEnrollment::factory()
        ->for($inactiveYear)
        ->create([
            'status' => StudentEnrollmentStatusEnum::ENROLLED,
        ]);

    // Act
    $result = StudentEnrollment::active()->get();

    // Assert
    expect($result)->toHaveCount(0);
});

it('active scope excludes inactive enrollment statuses', function () {
    // Arrange
    $activeYear = SchoolYear::first() ?? SchoolYear::factory()->active()->create();
    $activeTerm = SchoolTerm::first() ?? SchoolTerm::factory()->odd()->active()->create();

    StudentEnrollment::factory()->create([
        'school_year_id' => $activeYear->id,
        'school_term_id' => $activeTerm->id,
        'status' => StudentEnrollmentStatusEnum::MOVED_EXTERNAL,
    ]);

    // Act
    $result = StudentEnrollment::active()->get();

    // Assert
    expect($result)->toHaveCount(0);
});

it('inactive scope returns enrollments with inactive status or mismatched year/term', function () {
    // Arrange
    $activeYear = SchoolYear::first() ?? SchoolYear::factory()->active()->create();
    $activeTerm = SchoolTerm::first() ?? SchoolTerm::factory()->odd()->active()->create();
    $inactiveYear = SchoolYear::factory()->inactive()->create();

    // Active enrollment (should NOT be in inactive scope)
    StudentEnrollment::factory()->create([
        'school_year_id' => $activeYear->id,
        'school_term_id' => $activeTerm->id,
        'status' => StudentEnrollmentStatusEnum::ENROLLED,
    ]);

    // Inactive status
    $inactiveByStatus = StudentEnrollment::factory()->create([
        'school_year_id' => $activeYear->id,
        'school_term_id' => $activeTerm->id,
        'status' => StudentEnrollmentStatusEnum::GRADUATED,
    ]);

    // Mismatched year
    $inactiveByYear = StudentEnrollment::factory()->create([
        'school_year_id' => $inactiveYear->id,
        'school_term_id' => $activeTerm->id,
        'status' => StudentEnrollmentStatusEnum::ENROLLED,
    ]);

    // Act
    $result = StudentEnrollment::inactive()->get();

    // Assert
    expect($result->pluck('id')->sort()->values()->toArray())
        ->toBe(collect([$inactiveByStatus->id, $inactiveByYear->id])->sort()->values()->toArray());
});
