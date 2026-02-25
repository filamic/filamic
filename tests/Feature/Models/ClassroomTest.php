<?php

declare(strict_types=1);

use App\Enums\GradeEnum;
use App\Models\Classroom;
use App\Models\School;

test('it prevents mass assignment to guarded id', function () {
    // Arrange
    $customId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

    // Act
    $classroom = Classroom::create([
        'id' => $customId,
        'name' => 'Test Classroom',
        'school_id' => School::factory()->create()->getKey(),
        'grade' => GradeEnum::GRADE_10->value,
    ]);

    // Assert
    expect($classroom->getKey())
        ->toBeString()
        ->not->toBe($customId);
});

test('it casts the columns')
    ->expect(fn () => Classroom::factory()->create())
    ->grade->toBeInstanceOf(GradeEnum::class)
    ->is_moving_class->toBeBool();

test('school relationship returns the parent school', function () {
    // Arrange
    $school = School::factory()->create();
    $classroom = Classroom::factory()->for($school)->create();

    // Act
    $result = $classroom->school;

    // Assert
    expect($result)
        ->toBeInstanceOf(School::class)
        ->getKey()->toBe($school->getKey());
});

test('multiple classrooms can belong to the same school', function () {
    // Arrange
    $school = School::factory()->create();

    // Act
    Classroom::factory(3)->for($school)->create();

    // Assert
    expect($school->classrooms)->toHaveCount(3);
});
