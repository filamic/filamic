<?php

declare(strict_types=1);

use App\Models\School;
use App\Models\SubjectCategory;
use Illuminate\Support\Carbon;

test('can mass assign fillable attributes', function () {
    // Arrange
    $school = School::factory()->create();
    $attributes = [
        'school_id' => $school->id,
        'name' => 'Mathematics',
        'sort_order' => 1,
    ];

    // Act
    $subjectCategory = SubjectCategory::create($attributes);

    // Assert
    expect($subjectCategory)
        ->school_id->toBe($school->id)
        ->name->toBe('Mathematics')
        ->sort_order->toBe(1);
});

test('id is guarded from mass assignment', function () {
    // Arrange & Act - Try to mass assign id using fill()
    $subjectCategory = new SubjectCategory;
    $subjectCategory->fill([
        'id' => 999,
        'name' => 'Test Category',
    ]);
    $subjectCategory->school_id = School::factory()->create()->id;
    $subjectCategory->save();

    // Assert - id should be auto-generated, not 999 (guarded works)
    expect($subjectCategory->id)->not->toBe(999);
});

test('sort_order is cast to integer', function () {
    // Arrange & Act
    $subjectCategory = SubjectCategory::factory()->create([
        'sort_order' => '5',
    ]);

    // Assert
    expect($subjectCategory->sort_order)
        ->toBe(5)
        ->toBeInt();
});

test('timestamps are automatically cast to Carbon instances', function () {
    // Arrange & Act
    $subjectCategory = SubjectCategory::factory()->create();

    // Assert
    expect($subjectCategory)
        ->created_at->toBeInstanceOf(Carbon::class)
        ->updated_at->toBeInstanceOf(Carbon::class);
});

test('belongs to school relationship', function () {
    // Arrange
    $school = School::factory()->create();
    $subjectCategory = SubjectCategory::factory()->for($school)->create();

    // Act
    $relatedSchool = $subjectCategory->school;

    // Assert
    expect($relatedSchool)
        ->toBeInstanceOf(School::class)
        ->id->toBe($school->id);
});

test('school relationship returns correct school', function () {
    // Arrange
    $school1 = School::factory()->create();
    $school2 = School::factory()->create();
    $subjectCategory = SubjectCategory::factory()->for($school1)->create();

    // Act
    $relatedSchool = $subjectCategory->school;

    // Assert
    expect($relatedSchool->id)
        ->toBe($school1->id)
        ->not->toBe($school2->id);
});

test('can eager load school', function () {
    // Arrange
    $subjectCategory = SubjectCategory::factory()
        ->for(School::factory())
        ->create();

    // Act
    $subjectCategoryWithSchool = SubjectCategory::with('school')->find($subjectCategory->id);

    // Assert
    expect($subjectCategoryWithSchool->relationLoaded('school'))->toBeTrue()
        ->and($subjectCategoryWithSchool->school)->toBeInstanceOf(School::class);
});

test('factory creates subject category with sort_order', function () {
    // Arrange & Act
    $subjectCategory = SubjectCategory::factory()->create();

    // Assert
    expect($subjectCategory->sort_order)
        ->toBeInt()
        ->toBeGreaterThanOrEqual(1)
        ->toBeLessThanOrEqual(10);
});
