<?php

declare(strict_types=1);

use App\Models\Classroom;
use App\Models\School;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;

test('can mass assign fillable attributes', function () {
    // Arrange
    $school = School::factory()->create();
    $attributes = [
        'name' => 'Test Classroom',
        'school_id' => $school->id,
        'grade' => 10,
        'phase' => 'A',
        'is_moving_class' => true,
    ];

    // Act
    $classroom = Classroom::create($attributes);

    // Assert
    expect($classroom)
        ->name->toBe('Test Classroom')
        ->school_id->toBe($school->id)
        ->grade->toBe(10)
        ->phase->toBe('A')
        ->is_moving_class->toBeTrue();
});

test('id is guarded from mass assignment', function () {
    // Arrange
    $school = School::factory()->create();

    // Act - Try to mass assign id
    $classroom = new Classroom;
    $classroom->fill([
        'id' => 999,
        'name' => 'Test Classroom',
        'school_id' => $school->id,
    ]);
    $classroom->save();

    // Assert - id should be auto-generated, not 999 (guarded works)
    expect($classroom->id)->not->toBe(999);
});

test('is_moving_class is cast to boolean', function () {
    // Arrange & Act - Create with truthy value
    $classroomTrue = Classroom::factory()->forSchool()->create([
        'is_moving_class' => 1,
    ]);

    // Assert
    expect($classroomTrue->is_moving_class)
        ->toBeTrue()
        ->toBeBool();

    // Arrange & Act - Create with falsy value
    $classroomFalse = Classroom::factory()->forSchool()->create([
        'is_moving_class' => 0,
    ]);

    // Assert
    expect($classroomFalse->is_moving_class)
        ->toBeFalse()
        ->toBeBool();

    // Act - Update boolean value
    $classroomTrue->is_moving_class = false;
    $classroomTrue->save();

    // Assert - Value persists after save
    expect($classroomTrue->refresh()->is_moving_class)->toBeFalse();
});

test('timestamps are automatically cast to Carbon instances', function () {
    // Arrange & Act
    $classroom = Classroom::factory()->forSchool()->create();

    // Assert
    expect($classroom)
        ->created_at->toBeInstanceOf(Carbon::class)
        ->updated_at->toBeInstanceOf(Carbon::class);
});

test('belongs to school relationship', function () {
    // Arrange
    $school = School::factory()->create(['name' => 'Specific School']);

    // Act
    $classroom = Classroom::factory()->for($school)->create();

    // Assert
    expect($classroom->school)
        ->toBeInstanceOf(School::class)
        ->id->toBe($school->id)
        ->name->toBe('Specific School');
});

test('school_id is required in database', function () {
    // Arrange
    $classroom = new Classroom([
        'name' => 'Test Classroom',
        'school_id' => null,
    ]);

    // Act & Assert
    expect(fn () => $classroom->save())
        ->toThrow(QueryException::class);
});

test('can eager load school', function () {
    // Arrange
    $school = School::factory()->create();
    $classroom = Classroom::factory()->for($school)->create();

    // Act
    $classroomWithSchool = Classroom::with('school')->find($classroom->id);

    // Assert
    expect($classroomWithSchool->relationLoaded('school'))->toBeTrue()
        ->and($classroomWithSchool->school->id)->toBe($school->id);
});

test('multiple classrooms can belong to same school', function () {
    // Arrange
    $school = School::factory()->create();

    // Act
    $classroom1 = Classroom::factory()->for($school)->create();
    $classroom2 = Classroom::factory()->for($school)->create();

    // Assert
    expect($classroom1->school_id)->toBe($school->id)
        ->and($classroom2->school_id)->toBe($school->id)
        ->and($school->classrooms)->toHaveCount(2);
});
