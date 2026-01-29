<?php

declare(strict_types=1);

use App\Enums\SchoolTermEnum;
use App\Models\SchoolYear;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;

test('can mass assign fillable attributes', function () {
    // Arrange
    $attributes = [
        'name' => '2024/2025',
        'start_date' => '2024-09-01',
        'end_date' => '2025-06-30',
        'is_active' => true,
    ];

    // Act
    $schoolYear = SchoolYear::create($attributes);

    // Assert
    expect($schoolYear)
        ->name->toBe('2024/2025')
        ->is_active->toBe(true);
});

test('id is guarded from mass assignment 2', function () {
    // Arrange & Act
    $schoolYear = SchoolYear::create([
        'id' => 999,
        'name' => '2024/2025',
    ]);

    // Assert - id should be auto-generated, not 999 (guarded works)
    expect($schoolYear->id)->not->toBe(999);
});

// test('semester is cast to SemesterEnum', function () {
//     // Arrange & Act - Create with integer value
//     $schoolYear = SchoolYear::factory()->create([
//         'semester' => 1,
//     ]);

//     // Assert - Should be cast to enum
//     expect($schoolYear->semester)
//         ->toBeInstanceOf(SchoolTermEnum::class)
//         ->toBe(SchoolTermEnum::ODD);

//     // Arrange & Act - Create with enum value
//     $schoolYear2 = SchoolYear::factory()->create([
//         'semester' => SchoolTermEnum::EVEN,
//     ]);

//     // Assert
//     expect($schoolYear2->semester)
//         ->toBeInstanceOf(SchoolTermEnum::class)
//         ->toBe(SchoolTermEnum::EVEN);
// });

test('start_date is cast to Carbon instance', function () {
    // Arrange & Act
    $schoolYear = SchoolYear::factory()->create([
        'start_date' => '2024-09-01',
    ]);

    // Assert
    expect($schoolYear->start_date)->toBeInstanceOf(Carbon::class);
});

test('end_date is cast to Carbon instance', function () {
    // Arrange & Act
    $schoolYear = SchoolYear::factory()->create([
        'end_date' => '2025-06-30',
    ]);

    // Assert
    expect($schoolYear->end_date)->toBeInstanceOf(Carbon::class);
});

test('is_active is cast to boolean', function () {
    // Arrange & Act - Create with truthy value
    $activeSchoolYear = SchoolYear::factory()->create([
        'is_active' => 1,
    ]);

    // Assert
    expect($activeSchoolYear->is_active)
        ->toBeTrue()
        ->toBeBool();

    // Arrange & Act - Create with falsy value
    $inactiveSchoolYear = SchoolYear::factory()->create([
        'is_active' => 0,
    ]);

    // Assert
    expect($inactiveSchoolYear->is_active)
        ->toBeFalse()
        ->toBeBool();

    // Act - Update boolean value
    $activeSchoolYear->is_active = false;
    $activeSchoolYear->save();

    // Assert - Value persists after save
    expect($activeSchoolYear->refresh()->is_active)->toBeFalse();
});

test('timestamps are automatically cast to Carbon instances', function () {
    // Arrange & Act
    $schoolYear = SchoolYear::factory()->create();

    // Assert
    expect($schoolYear)
        ->created_at->toBeInstanceOf(Carbon::class)
        ->updated_at->toBeInstanceOf(Carbon::class);
});

test('name is required in database', function () {
    // Arrange
    $schoolYear = new SchoolYear([
        'is_active' => true,
    ]);

    // Act & Assert
    expect(fn () => $schoolYear->save())
        ->toThrow(QueryException::class);
});

// test('semester is required in database', function () {
//     // Arrange
//     $schoolYear = new SchoolYear([
//         'name' => '2024/2025',
//         'is_active' => true,
//     ]);

//     // Act & Assert
//     expect(fn () => $schoolYear->save())
//         ->toThrow(QueryException::class);
// });

test('can scope active school years', function () {
    // Arrange
    SchoolYear::factory()->count(3)->inactive()->create();
    SchoolYear::factory()->active()->create();

    // Act
    $activeSchoolYears = SchoolYear::active()->get();

    // Assert
    expect($activeSchoolYears)->toHaveCount(1);

    $activeSchoolYears->each(function ($schoolYear) {
        expect($schoolYear->is_active)->toBeTrue();
    });
});

test('can scope inactive school years', function () {
    // Arrange - Create inactive and one active
    SchoolYear::factory()->count(3)->inactive()->create();
    SchoolYear::factory()->active()->create();

    // Act
    $inactiveSchoolYears = SchoolYear::inactive()->get();

    // Assert
    expect($inactiveSchoolYears)->toHaveCount(3);

    $inactiveSchoolYears->each(function ($schoolYear) {
        expect($schoolYear->is_active)->toBeFalse();
    });
});

test('can check if active using isActive method', function () {
    // Arrange & Act
    $activeSchoolYear = SchoolYear::factory()->active()->create();
    $inactiveSchoolYear = SchoolYear::factory()->inactive()->create();

    // Assert
    expect($activeSchoolYear->isActive())->toBeTrue()
        ->and($inactiveSchoolYear->isActive())->toBeFalse();
});

test('can check if inactive using isInactive method', function () {
    // Arrange & Act
    $activeSchoolYear = SchoolYear::factory()->active()->create();
    $inactiveSchoolYear = SchoolYear::factory()->inactive()->create();

    // Assert
    expect($activeSchoolYear->isInactive())->toBeFalse()
        ->and($inactiveSchoolYear->isInactive())->toBeTrue();
});

test('deactivateOthers deactivates all active school years', function () {
    // Arrange - Create multiple active years
    SchoolYear::factory()->count(3)->active()->create();

    // Act
    SchoolYear::deactivateOthers();

    // Assert - All should be inactive
    expect(SchoolYear::active()->count())->toBe(0)
        ->and(SchoolYear::inactive()->count())->toBe(3);
});

test('deactivateOthers does nothing when no active years exist', function () {
    // Arrange
    SchoolYear::factory()->count(3)->inactive()->create();

    // Act
    SchoolYear::deactivateOthers();

    // Assert - All still inactive
    expect(SchoolYear::inactive()->count())->toBe(3);
});

test('activateExclusively deactivates all others and activates itself', function () {
    // Arrange - Create multiple active years
    $first = SchoolYear::factory()->active()->create(['name' => '2022/2023']);
    $second = SchoolYear::factory()->active()->create(['name' => '2023/2024']);
    $third = SchoolYear::factory()->inactive()->create(['name' => '2024/2025']);

    // Act - Activate the third one exclusively
    $third->activateExclusively();

    // Assert - Only third one is active
    expect($first->refresh()->is_active)->toBeFalse()
        ->and($second->refresh()->is_active)->toBeFalse()
        ->and($third->refresh()->is_active)->toBeTrue()
        ->and(SchoolYear::active()->count())->toBe(1);
});

test('activateExclusively works when no others are active', function () {
    // Arrange
    $schoolYear = SchoolYear::factory()->inactive()->create();

    // Act
    $schoolYear->activateExclusively();

    // Assert
    expect($schoolYear->refresh()->is_active)->toBeTrue()
        ->and(SchoolYear::active()->count())->toBe(1);
});
