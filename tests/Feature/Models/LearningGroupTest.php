<?php

declare(strict_types=1);

use App\Models\LearningGroup;
use App\Models\School;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

// --- Mass Assignment ---

test('id is guarded from mass assignment', function () {
    // Arrange
    $customId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

    // Act
    $learningGroup = LearningGroup::create([
        'id' => $customId,
        'name' => 'Group A',
        'school_id' => School::factory()->create()->id,
    ]);

    // Assert
    expect($learningGroup->getKey())
        ->toBeString()
        ->not->toBe($customId);
});

// --- Casts ---

test('timestamps are automatically cast to Carbon instances', function () {
    // Arrange & Act
    $learningGroup = LearningGroup::factory()->create();

    // Assert
    expect($learningGroup)
        ->created_at->toBeInstanceOf(Carbon::class)
        ->updated_at->toBeInstanceOf(Carbon::class);
});

// --- Relationships ---

test('school relation')
    ->expect(fn () => LearningGroup::factory()->create())
    ->school()->toBeInstanceOf(BelongsTo::class)
    ->school->toBeInstanceOf(School::class);

test('school relationship returns correct school', function () {
    // Arrange
    [$school1, $school2] = School::factory(2)->create();
    $learningGroup = LearningGroup::factory()->for($school1)->create();

    // Act & Assert
    expect($learningGroup->school->id)
        ->toBe($school1->id)
        ->not->toBe($school2->id);
});

test('can eager load school', function () {
    // Arrange
    $learningGroup = LearningGroup::factory()->create();

    // Act
    $loaded = LearningGroup::with('school')->find($learningGroup->id);

    // Assert
    expect($loaded->relationLoaded('school'))->toBeTrue()
        ->and($loaded->school)->toBeInstanceOf(School::class);
});
