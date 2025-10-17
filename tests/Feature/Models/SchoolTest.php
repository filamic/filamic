<?php

declare(strict_types=1);

use App\Models\Classroom;
use App\Models\School;
use Illuminate\Support\Carbon;

test('can mass assign fillable attributes', function () {
    // Arrange
    $attributes = [
        'name' => 'Test School',
        'address' => '123 Test St',
        'npsn' => '12345678',
        'nis_nss_nds' => '87654321',
        'telp' => '08123456789',
        'postal_code' => '12345',
        'village' => 'Test Village',
        'subdistrict' => 'Test Subdistrict',
        'city' => 'Test City',
        'province' => 'Test Province',
        'website' => 'https://test.com',
        'email' => 'test@school.com',
    ];

    // Act
    $school = School::create($attributes);

    // Assert
    expect($school)
        ->name->toBe('Test School')
        ->address->toBe('123 Test St')
        ->npsn->toBe('12345678')
        ->nis_nss_nds->toBe('87654321')
        ->telp->toBe('08123456789')
        ->postal_code->toBe('12345')
        ->village->toBe('Test Village')
        ->subdistrict->toBe('Test Subdistrict')
        ->city->toBe('Test City')
        ->province->toBe('Test Province')
        ->website->toBe('https://test.com')
        ->email->toBe('test@school.com');
});

test('id is guarded from mass assignment', function () {
    // Arrange & Act - Try to mass assign id using fill()
    $school = new School;
    $school->fill([
        'id' => 999,
        'name' => 'Test School',
    ]);
    $school->save();

    // Assert - id should be auto-generated, not 999 (guarded works)
    expect($school->id)->not->toBe(999);
});

test('timestamps are automatically cast to Carbon instances', function () {
    // Arrange & Act
    $school = School::factory()->create();

    // Assert
    expect($school)
        ->created_at->toBeInstanceOf(Carbon::class)
        ->updated_at->toBeInstanceOf(Carbon::class);
});

test('has many classrooms relationship', function () {
    // Arrange
    $school = School::factory()->create();
    $classrooms = Classroom::factory()
        ->count(3)
        ->for($school)
        ->create();

    // Act
    $schoolClassrooms = $school->classrooms;

    // Assert
    expect($schoolClassrooms)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Classroom::class);

    expect($schoolClassrooms->pluck('id')->sort()->values()->toArray())
        ->toBe($classrooms->pluck('id')->sort()->values()->toArray());
});

test('classrooms relationship returns empty collection when school has no classrooms', function () {
    // Arrange
    $school = School::factory()->create();

    // Act
    $classrooms = $school->classrooms;

    // Assert
    expect($classrooms)
        ->toBeEmpty()
        ->toBeInstanceOf(Illuminate\Database\Eloquent\Collection::class);
});

test('can eager load classrooms', function () {
    // Arrange
    $school = School::factory()
        ->has(Classroom::factory()->count(2))
        ->create();

    // Act
    $schoolWithClassrooms = School::with('classrooms')->find($school->id);

    // Assert
    expect($schoolWithClassrooms->relationLoaded('classrooms'))->toBeTrue()
        ->and($schoolWithClassrooms->classrooms)->toHaveCount(2);
});
