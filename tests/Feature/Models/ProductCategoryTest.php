<?php

declare(strict_types=1);

use App\Models\ProductCategory;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;

test('it prevents mass assignment to guarded id', function () {
    // Arrange
    $customId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

    // Act
    $category = ProductCategory::create([
        'id' => $customId,
        'name' => 'Seragam',
        'code' => 'SRG',
    ]);

    // Assert
    expect($category->getKey())
        ->toBeString()
        ->not->toBe($customId);
});

test('variations relationship returns related variations', function () {
    // Arrange
    $category = ProductCategory::factory()->create();
    ProductVariation::factory(3)->create(['product_category_id' => $category->getKey()]);

    // Act
    $variations = $category->variations;

    // Assert
    expect($variations)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(ProductVariation::class);
});

test('it isolates variations by category', function () {
    // Arrange
    $category = ProductCategory::factory()->create();
    $otherCategory = ProductCategory::factory()->create();

    $variation = ProductVariation::factory()->create(['product_category_id' => $category->getKey()]);
    ProductVariation::factory()->create(['product_category_id' => $otherCategory->getKey()]);

    // Act
    $variations = $category->variations;

    // Assert
    expect($variations)
        ->toHaveCount(1)
        ->first()->getKey()->toBe($variation->getKey());
});

test('variationOptions relationship returns options through variations', function () {
    // Arrange
    $category = ProductCategory::factory()->create();
    $variation = ProductVariation::factory()->create(['product_category_id' => $category->getKey()]);
    ProductVariationOption::factory(3)->create(['product_variation_id' => $variation->getKey()]);

    // Act
    $options = $category->variationOptions;

    // Assert
    expect($options)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(ProductVariationOption::class);
});

test('it isolates variationOptions by category', function () {
    // Arrange
    $category = ProductCategory::factory()->create();
    $otherCategory = ProductCategory::factory()->create();

    $variation = ProductVariation::factory()->create(['product_category_id' => $category->getKey()]);
    $otherVariation = ProductVariation::factory()->create(['product_category_id' => $otherCategory->getKey()]);

    $option = ProductVariationOption::factory()->create(['product_variation_id' => $variation->getKey()]);
    ProductVariationOption::factory()->create(['product_variation_id' => $otherVariation->getKey()]);

    // Act
    $options = $category->variationOptions;

    // Assert
    expect($options)
        ->toHaveCount(1)
        ->first()->getKey()->toBe($option->getKey());
});
