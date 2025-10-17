<?php

declare(strict_types=1);

use App\Models\School;
use App\Models\Subject;
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
    // Arrange & Act
    $subjectCategory = SubjectCategory::create([
        'id' => 999,
        'name' => 'Test Category',
        'school_id' => School::factory()->create()->id,
    ]);

    // Assert
    expect($subjectCategory->id)->not->toBe(999);
});

test('sort_order is cast to integer', function () {
    // Arrange & Act
    $subjectCategory = SubjectCategory::factory()->create(['sort_order' => '5']);

    // Assert
    expect($subjectCategory->sort_order)->toBe(5)->toBeInt();
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

    // Act & Assert
    expect($subjectCategory->school)
        ->toBeInstanceOf(School::class)
        ->id->toBe($school->id);
});

test('school relationship returns correct school', function () {
    // Arrange
    [$school1, $school2] = School::factory(2)->create();
    $subjectCategory = SubjectCategory::factory()->for($school1)->create();

    // Act & Assert
    expect($subjectCategory->school->id)
        ->toBe($school1->id)
        ->not->toBe($school2->id);
});

test('can eager load school', function () {
    // Arrange
    $subjectCategory = SubjectCategory::factory()->for(School::factory())->create();

    // Act & Assert
    $subjectCategoryWithSchool = SubjectCategory::with('school')->find($subjectCategory->id);
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

test('has many subjects relationship', function () {
    // Arrange
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $subjects = Subject::factory(3)->create(['subject_category_id' => $category->id]);

    // Act & Assert
    expect($category->subjects)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Subject::class)
        ->and($category->subjects->pluck('id')->sort()->values()->toArray())
        ->toBe($subjects->pluck('id')->sort()->values()->toArray());
});

test('subjects relationship returns empty collection when no subjects exist', function () {
    // Arrange
    $category = SubjectCategory::factory()->for(School::factory())->create();

    // Act & Assert
    expect($category->subjects)
        ->toBeEmpty()
        ->toBeInstanceOf(Illuminate\Database\Eloquent\Collection::class);
});

test('can eager load subjects', function () {
    // Arrange
    $category = SubjectCategory::factory()->for(School::factory())->create();
    Subject::factory(2)->create(['subject_category_id' => $category->id]);

    // Act & Assert
    $categoryWithSubjects = SubjectCategory::with('subjects')->find($category->id);
    expect($categoryWithSubjects->relationLoaded('subjects'))->toBeTrue()
        ->and($categoryWithSubjects->subjects)->toHaveCount(2);
});

test('subjects from different categories are not mixed', function () {
    // Arrange
    [$category1, $category2] = SubjectCategory::factory(2)->for(School::factory())->create();
    $subject1 = Subject::factory()->create(['subject_category_id' => $category1->id]);
    $subject2 = Subject::factory()->create(['subject_category_id' => $category2->id]);

    // Act & Assert
    expect($category1->subjects)
        ->toHaveCount(1)
        ->first()->id->toBe($subject1->id)
        ->and($category2->subjects)
        ->toHaveCount(1)
        ->first()->id->toBe($subject2->id);
});
