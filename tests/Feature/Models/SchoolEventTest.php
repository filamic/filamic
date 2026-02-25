<?php

declare(strict_types=1);

use App\Models\School;
use App\Models\SchoolEvent;
use Illuminate\Support\Carbon;

it('returns upcoming events starting after today', function () {
    // Arrange
    $school = School::factory()->create();
    Carbon::setTestNow('2026-03-15');

    $upcoming = SchoolEvent::factory()->for($school)->create([
        'start_date' => '2026-03-20',
        'end_date' => '2026-03-22',
    ]);
    SchoolEvent::factory()->for($school)->create([
        'start_date' => '2026-03-10',
        'end_date' => '2026-03-12',
    ]);

    // Act
    $result = SchoolEvent::upcoming()->get();

    // Assert
    expect($result)
        ->toHaveCount(1)
        ->first()->id->toBe($upcoming->id);
});

it('orders upcoming events by start_date ascending', function () {
    // Arrange
    $school = School::factory()->create();
    Carbon::setTestNow('2026-03-15');

    $later = SchoolEvent::factory()->for($school)->create([
        'start_date' => '2026-03-25',
        'end_date' => '2026-03-27',
    ]);
    $sooner = SchoolEvent::factory()->for($school)->create([
        'start_date' => '2026-03-18',
        'end_date' => '2026-03-19',
    ]);

    // Act
    $result = SchoolEvent::upcoming()->get();

    // Assert
    expect($result->first()->id)->toBe($sooner->id)
        ->and($result->last()->id)->toBe($later->id);
});

it('returns ongoing events spanning today', function () {
    // Arrange
    $school = School::factory()->create();
    Carbon::setTestNow('2026-03-15');

    $ongoing = SchoolEvent::factory()->for($school)->create([
        'start_date' => '2026-03-14',
        'end_date' => '2026-03-16',
    ]);
    SchoolEvent::factory()->for($school)->create([
        'start_date' => '2026-03-20',
        'end_date' => '2026-03-22',
    ]);

    // Act
    $result = SchoolEvent::ongoing()->get();

    // Assert
    expect($result)
        ->toHaveCount(1)
        ->first()->id->toBe($ongoing->id);
});

it('includes event when today is exactly start_date', function () {
    // Arrange
    $school = School::factory()->create();
    Carbon::setTestNow('2026-03-15');

    $event = SchoolEvent::factory()->for($school)->create([
        'start_date' => '2026-03-15',
        'end_date' => '2026-03-17',
    ]);

    // Act
    $result = SchoolEvent::ongoing()->get();

    // Assert
    expect($result)->toHaveCount(1)
        ->and($result->first()->id)->toBe($event->id);
});

it('includes event when today is exactly end_date', function () {
    // Arrange
    $school = School::factory()->create();
    Carbon::setTestNow('2026-03-15');

    $event = SchoolEvent::factory()->for($school)->create([
        'start_date' => '2026-03-13',
        'end_date' => '2026-03-15',
    ]);

    // Act
    $result = SchoolEvent::ongoing()->get();

    // Assert
    expect($result)->toHaveCount(1)
        ->and($result->first()->id)->toBe($event->id);
});

it('returns past events ending before today', function () {
    // Arrange
    $school = School::factory()->create();
    Carbon::setTestNow('2026-03-15');

    $past = SchoolEvent::factory()->for($school)->create([
        'start_date' => '2026-03-05',
        'end_date' => '2026-03-08',
    ]);
    SchoolEvent::factory()->for($school)->create([
        'start_date' => '2026-03-20',
        'end_date' => '2026-03-22',
    ]);

    // Act
    $result = SchoolEvent::past()->get();

    // Assert
    expect($result)
        ->toHaveCount(1)
        ->first()->id->toBe($past->id);
});

it('orders past events by end_date descending', function () {
    // Arrange
    $school = School::factory()->create();
    Carbon::setTestNow('2026-03-15');

    $older = SchoolEvent::factory()->for($school)->create([
        'start_date' => '2026-03-01',
        'end_date' => '2026-03-03',
    ]);
    $newer = SchoolEvent::factory()->for($school)->create([
        'start_date' => '2026-03-10',
        'end_date' => '2026-03-12',
    ]);

    // Act
    $result = SchoolEvent::past()->get();

    // Assert
    expect($result->first()->id)->toBe($newer->id)
        ->and($result->last()->id)->toBe($older->id);
});

it('casts start_date and end_date to Carbon', function () {
    // Arrange
    $event = SchoolEvent::factory()->create();

    // Act & Assert
    expect($event->start_date)->toBeInstanceOf(Carbon::class)
        ->and($event->end_date)->toBeInstanceOf(Carbon::class);
});
