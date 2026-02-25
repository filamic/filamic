<?php

declare(strict_types=1);

use App\Models\Curriculum;
use App\Models\School;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

// --- Mass Assignment ---

test('id is guarded from mass assignment', function () {
    // Arrange
    $customId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

    // Act
    $curriculum = Curriculum::create([
        'id' => $customId,
        'name' => 'Kurikulum Merdeka',
        'school_id' => School::factory()->create()->id,
    ]);

    // Assert
    expect($curriculum->getKey())
        ->toBeString()
        ->not->toBe($customId);
});

// --- Casts ---

test('is_active is cast to boolean', function () {
    // Arrange & Act
    $curriculum = Curriculum::factory()->active()->create();

    // Assert
    expect($curriculum->is_active)->toBeBool()->toBeTrue();
});

test('timestamps are automatically cast to Carbon instances', function () {
    // Arrange & Act
    $curriculum = Curriculum::factory()->create();

    // Assert
    expect($curriculum)
        ->created_at->toBeInstanceOf(Carbon::class)
        ->updated_at->toBeInstanceOf(Carbon::class);
});

// --- Relationships ---

test('school relation')
    ->expect(fn () => Curriculum::factory()->create())
    ->school()->toBeInstanceOf(BelongsTo::class)
    ->school->toBeInstanceOf(School::class);

test('school relationship returns correct school', function () {
    // Arrange
    [$school1, $school2] = School::factory(2)->create();
    $curriculum = Curriculum::factory()->for($school1)->create();

    // Act & Assert
    expect($curriculum->school->id)
        ->toBe($school1->id)
        ->not->toBe($school2->id);
});

test('can eager load school', function () {
    // Arrange
    $curriculum = Curriculum::factory()->create();

    // Act
    $loaded = Curriculum::with('school')->find($curriculum->id);

    // Assert
    expect($loaded->relationLoaded('school'))->toBeTrue()
        ->and($loaded->school)->toBeInstanceOf(School::class);
});

// --- Scopes (HasActiveState) ---

test('active scope only returns active records', function () {
    // Arrange
    $active = Curriculum::factory()->active()->create();
    Curriculum::factory()->inactive()->create();

    // Act
    $result = Curriculum::active()->get();

    // Assert
    expect($result)
        ->toHaveCount(1)
        ->first()->id->toBe($active->getKey());
});

test('inactive scope only returns inactive records', function () {
    // Arrange
    Curriculum::factory()->active()->create();
    $inactive = Curriculum::factory()->inactive()->create();

    // Act
    $result = Curriculum::inactive()->get();

    // Assert
    expect($result)
        ->toHaveCount(1)
        ->first()->id->toBe($inactive->getKey());
});

test('isActive and isInactive reflect current state', function () {
    // Arrange
    $active = Curriculum::factory()->active()->create();
    $inactive = Curriculum::factory()->inactive()->create();

    // Act & Assert
    expect($active->isActive())
        ->toBeTrue()
        ->and($active->isInactive())->toBeFalse()
        ->and($inactive->isActive())->toBeFalse()
        ->and($inactive->isInactive())->toBeTrue();
});

test('deactivateOthers turns active records inactive', function () {
    // Arrange
    [$first, $second] = Curriculum::factory(2)->active()->create();

    // Act
    Curriculum::deactivateOthers();

    // Assert
    expect($first->refresh()->is_active)
        ->toBeFalse()
        ->and($second->refresh()->is_active)->toBeFalse()
        ->and(Curriculum::query()->active()->count())->toBe(0);
});

test('activateExclusively activates current record and deactivates others', function () {
    // Arrange
    $first = Curriculum::factory()->active()->create();
    $second = Curriculum::factory()->inactive()->create();

    // Act
    $second->activateExclusively();

    // Assert
    expect($first->refresh()->is_active)
        ->toBeFalse()
        ->and($second->refresh()->is_active)->toBeTrue();
});
