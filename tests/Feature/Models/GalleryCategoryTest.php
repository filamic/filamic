<?php

declare(strict_types=1);

use App\Models\GalleryCategory;
use Illuminate\Database\QueryException;

test('it requires name in database', function () {
    // Arrange
    $category = new GalleryCategory([
        'slug' => 'gallery-category-1000',
    ]);

    // Act & Assert
    expect(fn () => $category->save())->toThrow(QueryException::class);
});

test('it requires slug in database', function () {
    // Arrange
    $category = new GalleryCategory([
        'name' => 'General Gallery',
    ]);

    // Act & Assert
    expect(fn () => $category->save())->toThrow(QueryException::class);
});

test('it enforces unique slug constraint', function () {
    // Arrange
    GalleryCategory::factory()->create([
        'name' => 'School Event',
        'slug' => 'school-event',
    ]);

    // Act & Assert
    expect(fn () => GalleryCategory::factory()->create([
        'name' => 'School Event Duplicate',
        'slug' => 'school-event',
    ]))->toThrow(QueryException::class);
});

test('it can be mass assigned with valid attributes', function () {
    // Arrange
    $payload = [
        'name' => 'Class Activities',
        'slug' => 'class-activities',
    ];

    // Act
    $category = GalleryCategory::create($payload);

    // Assert
    expect($category->refresh())
        ->name->toBe('Class Activities')
        ->slug->toBe('class-activities');
});

test('factory slug stays based on name while remaining unique', function () {
    // Arrange
    $first = GalleryCategory::factory()->create(['name' => 'Morning Assembly']);
    $second = GalleryCategory::factory()->create(['name' => 'Morning Assembly']);

    // Act
    $firstSlugPrefix = str($first->slug)->beforeLast('-')->value();
    $secondSlugPrefix = str($second->slug)->beforeLast('-')->value();

    // Assert
    expect($firstSlugPrefix)
        ->toBe('morning-assembly')
        ->and($secondSlugPrefix)->toBe('morning-assembly')
        ->and($first->slug)->not->toBe($second->slug);
});
