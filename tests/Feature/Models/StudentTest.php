<?php

declare(strict_types=1);

use App\Enums\GenderEnum;
use App\Enums\ReligionEnum;
use App\Enums\StatusInFamilyEnum;
use App\Enums\StudentEnrollmentStatusEnum;
use App\Models\Classroom;
use App\Models\Invoice;
use App\Models\School;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentEnrollment;

it('casts the columns')
    ->expect(fn () => Student::factory()->create())
    ->gender->toBeInstanceOf(GenderEnum::class)
    ->status_in_family->toBeInstanceOf(StatusInFamilyEnum::class)
    ->religion->toBeInstanceOf(ReligionEnum::class);

it('syncActiveStatus activates student when active enrollment exists', function () {
    // Arrange
    $activeYear = SchoolYear::factory()->active()->create();
    $activeTerm = SchoolTerm::factory()->odd()->active()->create();
    $school = School::factory()->create();
    $student = Student::factory()->for($school)->create(['is_active' => false]);
    $classroom = Classroom::factory()->for($school)->create();

    StudentEnrollment::factory()->create([
        'classroom_id' => $classroom->id,
        'school_year_id' => $activeYear->id,
        'school_term_id' => $activeTerm->id,
        'student_id' => $student->id,
        'status' => StudentEnrollmentStatusEnum::ENROLLED,
    ]);

    // Act
    $student->syncActiveStatus();

    // Assert
    expect($student->refresh()->is_active)->toBeTrue();
});

it('syncActiveStatus deactivates student when no active enrollment exists', function () {
    // Arrange
    SchoolYear::factory()->active()->create();
    SchoolTerm::factory()->odd()->active()->create();
    $student = Student::factory()->create(['is_active' => true]);

    // Act — no active enrollment
    $student->syncActiveStatus();

    // Assert
    expect($student->refresh()->is_active)->toBeFalse();
});

it('syncActiveStatus returns early when no active school year', function () {
    // Arrange
    SchoolYear::factory()->inactive()->create();
    SchoolTerm::factory()->odd()->active()->create();
    $student = Student::factory()->create(['is_active' => true]);

    // Act
    $student->syncActiveStatus();

    // Assert — should not change because method returns early
    expect($student->refresh()->is_active)->toBeTrue();
});

it('syncActiveStatus returns early when no active school term', function () {
    // Arrange
    SchoolYear::factory()->active()->create();
    SchoolTerm::factory()->odd()->inactive()->create();
    $student = Student::factory()->create(['is_active' => true]);

    // Act
    $student->syncActiveStatus();

    // Assert — should not change because method returns early
    expect($student->refresh()->is_active)->toBeTrue();
});

it('hasUnpaidMonthlyFee returns true when unpaid monthly fee exists', function () {
    // Arrange
    $student = Student::factory()->create();
    Invoice::factory()->for($student)->monthlyFee()->unpaid()->create();

    // Act & Assert
    expect($student->hasUnpaidMonthlyFee())->toBeTrue();
});

it('hasUnpaidMonthlyFee returns false when no unpaid monthly fee exists', function () {
    // Arrange
    $student = Student::factory()->create();
    Invoice::factory()->for($student)->monthlyFee()->paid()->create();

    // Act & Assert
    expect($student->hasUnpaidMonthlyFee())->toBeFalse();
});

it('hasPaidMonthlyFee returns true when paid monthly fee exists', function () {
    // Arrange
    $student = Student::factory()->create();
    Invoice::factory()->for($student)->monthlyFee()->paid()->create();

    // Act & Assert
    expect($student->hasPaidMonthlyFee())->toBeTrue();
});

it('hasPaidMonthlyFee returns false when no paid monthly fee exists', function () {
    // Arrange
    $student = Student::factory()->create();
    Invoice::factory()->for($student)->monthlyFee()->unpaid()->create();

    // Act & Assert
    expect($student->hasPaidMonthlyFee())->toBeFalse();
});

it('currentEnrollment returns only active enrollment', function () {
    // Arrange
    $activeYear = SchoolYear::factory()->active()->create();
    $activeTerm = SchoolTerm::first() ?? SchoolTerm::factory()->odd()->active()->create();
    $school = School::factory()->create();
    $student = Student::factory()->for($school)->create();
    $classroom = Classroom::factory()->for($school)->create();

    $activeEnrollment = StudentEnrollment::factory()->create([
        'classroom_id' => $classroom->id,
        'school_year_id' => $activeYear->id,
        'school_term_id' => $activeTerm->id,
        'student_id' => $student->id,
        'status' => StudentEnrollmentStatusEnum::ENROLLED,
    ]);

    StudentEnrollment::factory()->create([
        'student_id' => $student->id,
        'school_year_id' => SchoolYear::factory()->inactive(),
        'status' => StudentEnrollmentStatusEnum::PROMOTED,
    ]);

    // Act
    $current = $student->currentEnrollment;

    // Assert
    expect($current)
        ->not->toBeNull()
        ->id->toBe($activeEnrollment->id);
});

it('currentEnrollment returns null when no active enrollment exists', function () {
    // Arrange
    SchoolYear::factory()->active()->create();
    SchoolTerm::factory()->odd()->active()->create();
    $student = Student::factory()->create();

    // Act
    $current = $student->currentEnrollment;

    // Assert
    expect($current)->toBeNull();
});
