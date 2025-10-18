<?php

declare(strict_types=1);

use App\Models\School;
use App\Models\Subject;
use App\Models\SubjectCategory;
use Illuminate\Support\Carbon;

test('can mass assign fillable attributes', function () {
    // Arrange
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $attributes = [
        'subject_category_id' => $category->id,
        'name' => 'Physics',
        'sort_order' => 1,
    ];

    // Act
    $subject = Subject::create($attributes);

    // Assert
    expect($subject)
        ->subject_category_id->toBe($category->id)
        ->name->toBe('Physics')
        ->sort_order->toBe(1);
});

test('id is guarded from mass assignment', function () {
    // Arrange & Act
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $subject = Subject::create([
        'id' => 999,
        'name' => 'Test Subject',
        'subject_category_id' => $category->id,
    ]);

    // Assert
    expect($subject->id)->not->toBe(999);
});

test('sort_order is cast to integer', function () {
    // Arrange & Act
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $subject = Subject::factory()->create([
        'subject_category_id' => $category->id,
        'sort_order' => '5',
    ]);

    // Assert
    expect($subject->sort_order)->toBe(5)->toBeInt();
});

test('timestamps are automatically cast to Carbon instances', function () {
    // Arrange & Act
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $subject = Subject::factory()->create(['subject_category_id' => $category->id]);

    // Assert
    expect($subject)
        ->created_at->toBeInstanceOf(Carbon::class)
        ->updated_at->toBeInstanceOf(Carbon::class);
});

test('belongs to category relationship', function () {
    // Arrange
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $subject = Subject::factory()->create(['subject_category_id' => $category->id]);

    // Act & Assert
    expect($subject->category)
        ->toBeInstanceOf(SubjectCategory::class)
        ->id->toBe($category->id);
});

test('category relationship returns correct category', function () {
    // Arrange
    $school = School::factory()->create();
    [$category1, $category2] = SubjectCategory::factory(2)->for($school)->create();
    $subject = Subject::factory()->create(['subject_category_id' => $category1->id]);

    // Act & Assert
    expect($subject->category->id)
        ->toBe($category1->id)
        ->not->toBe($category2->id);
});

test('can eager load category', function () {
    // Arrange
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $subject = Subject::factory()->create(['subject_category_id' => $category->id]);

    // Act & Assert
    $subjectWithCategory = Subject::with('category')->find($subject->id);
    expect($subjectWithCategory->relationLoaded('category'))->toBeTrue()
        ->and($subjectWithCategory->category)->toBeInstanceOf(SubjectCategory::class);
});

test('has one through school relationship', function () {
    // Arrange
    $school = School::factory()->create();
    $category = SubjectCategory::factory()->for($school)->create();
    $subject = Subject::factory()->create(['subject_category_id' => $category->id]);

    // Act & Assert
    expect($subject->school)
        ->toBeInstanceOf(School::class)
        ->id->toBe($school->id);
});

test('school relationship returns correct school', function () {
    // Arrange
    [$school1, $school2] = School::factory(2)->create();
    $category1 = SubjectCategory::factory()->for($school1)->create();
    $category2 = SubjectCategory::factory()->for($school2)->create();
    $subject1 = Subject::factory()->create(['subject_category_id' => $category1->id]);
    $subject2 = Subject::factory()->create(['subject_category_id' => $category2->id]);

    // Act & Assert
    expect($subject1->school->id)
        ->toBe($school1->id)
        ->not->toBe($school2->id)
        ->and($subject2->school->id)
        ->toBe($school2->id)
        ->not->toBe($school1->id);
});

test('can eager load school through category', function () {
    // Arrange
    $school = School::factory()->create();
    $category = SubjectCategory::factory()->for($school)->create();
    $subject = Subject::factory()->create(['subject_category_id' => $category->id]);

    // Act & Assert
    $subjectWithSchool = Subject::with('school')->find($subject->id);
    expect($subjectWithSchool->relationLoaded('school'))->toBeTrue()
        ->and($subjectWithSchool->school)->toBeInstanceOf(School::class)
        ->and($subjectWithSchool->school->id)->toBe($school->id);
});

test('can access school through category relationship', function () {
    // Arrange
    $school = School::factory()->create();
    $category = SubjectCategory::factory()->for($school)->create();
    $subject = Subject::factory()->create(['subject_category_id' => $category->id]);

    // Act & Assert
    expect($subject->category->school)
        ->toBeInstanceOf(School::class)
        ->id->toBe($school->id);
});

test('factory creates subject with sort_order', function () {
    // Arrange & Act
    $category = SubjectCategory::factory()->for(School::factory())->create();
    $subject = Subject::factory()->create(['subject_category_id' => $category->id]);

    // Assert
    expect($subject->sort_order)
        ->toBeInt()
        ->toBeGreaterThanOrEqual(1)
        ->toBeLessThanOrEqual(10);
});
