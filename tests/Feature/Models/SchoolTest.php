<?php

declare(strict_types=1);

use App\Enums\LevelEnum;
use App\Models\Branch;
use App\Models\Classroom;
use App\Models\School;
use App\Models\SchoolEvent;
use App\Models\Subject;
use App\Models\SubjectCategory;

it('casts level to LevelEnum', function () {
    // Arrange
    $school = School::factory()->create();

    // Act & Assert
    expect($school->level)->toBeInstanceOf(LevelEnum::class);
});

it('formats npsn with prefix when filled', function () {
    // Arrange
    $school = School::factory()->make([
        'npsn' => '12345678',
    ]);

    // Act & Assert
    expect($school->formatted_npsn)->toBe('NPSN: 12345678');
});

it('returns dash when npsn is null', function () {
    // Arrange
    $school = School::factory()->make([
        'npsn' => null,
    ]);

    // Act & Assert
    expect($school->formatted_npsn)->toBe('-');
});

it('returns dash when npsn is empty string', function () {
    // Arrange
    $school = School::factory()->make([
        'npsn' => '',
    ]);

    // Act & Assert
    expect($school->formatted_npsn)->toBe('-');
});

it('has many classrooms', function () {
    // Arrange
    $school = School::factory()->create();
    Classroom::factory(3)->for($school)->create();

    // Act
    $classrooms = $school->classrooms;

    // Assert
    expect($classrooms)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Classroom::class);
});

it('has many subject categories', function () {
    // Arrange
    $school = School::factory()->create();
    SubjectCategory::factory(2)->for($school)->create();

    // Act
    $categories = $school->subjectCategories;

    // Assert
    expect($categories)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(SubjectCategory::class);
});

it('has many events', function () {
    // Arrange
    $school = School::factory()->create();
    SchoolEvent::factory(2)->for($school)->create();

    // Act
    $events = $school->events;

    // Assert
    expect($events)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(SchoolEvent::class);
});

it('has many subjects through subject categories', function () {
    // Arrange
    $school = School::factory()->create();
    $category1 = SubjectCategory::factory()->for($school)->create();
    $category2 = SubjectCategory::factory()->for($school)->create();
    Subject::factory(2)->for($category1, 'category')->create();
    Subject::factory()->for($category2, 'category')->create();

    // Act
    $subjects = $school->subjects;

    // Assert
    expect($subjects)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Subject::class);
});

it('isolates subjects between schools', function () {
    // Arrange
    [$school1, $school2] = School::factory(2)->create();
    $cat1 = SubjectCategory::factory()->for($school1)->create();
    $cat2 = SubjectCategory::factory()->for($school2)->create();
    $subject1 = Subject::factory()->for($cat1, 'category')->create();
    $subject2 = Subject::factory()->for($cat2, 'category')->create();

    // Act & Assert
    expect($school1->subjects)
        ->toHaveCount(1)
        ->first()->id->toBe($subject1->id)
        ->and($school2->subjects)
        ->toHaveCount(1)
        ->first()->id->toBe($subject2->id);
});

it('belongs to branch', function () {
    // Arrange
    $school = School::factory()->create();

    // Act & Assert
    expect($school->branch)->toBeInstanceOf(Branch::class);
});
