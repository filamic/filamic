<?php

declare(strict_types=1);

use App\Models\ProductCategory;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;

test('it prevents mass assignment to guarded id', function () {
    // Arrange
    $customId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
    $category = ProductCategory::factory()->create();

    // Act
    $variation = ProductVariation::create([
        'id' => $customId,
        'product_category_id' => $category->getKey(),
        'name' => 'Warna',
    ]);

    // Assert
    expect($variation->getKey())
        ->toBeString()
        ->not->toBe($customId);
});

test('category relationship returns related category', function () {
    // Arrange
    $category = ProductCategory::factory()->create();
    $variation = ProductVariation::factory()->create(['product_category_id' => $category->getKey()]);

    // Act
    $relatedCategory = $variation->category;

    // Assert
    expect($relatedCategory)
        ->toBeInstanceOf(ProductCategory::class)
        ->getKey()->toBe($category->getKey());
});

test('options relationship returns related options', function () {
    // Arrange
    $variation = ProductVariation::factory()->create();
    ProductVariationOption::factory(3)->create(['product_variation_id' => $variation->getKey()]);

    // Act
    $options = $variation->options;

    // Assert
    expect($options)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(ProductVariationOption::class);
});

test('it isolates options by variation', function () {
    // Arrange
    $variation = ProductVariation::factory()->create();
    $otherVariation = ProductVariation::factory()->create();

    $option = ProductVariationOption::factory()->create(['product_variation_id' => $variation->getKey()]);
    ProductVariationOption::factory()->create(['product_variation_id' => $otherVariation->getKey()]);

    // Act
    $options = $variation->options;

    // Assert
    expect($options)
        ->toHaveCount(1)
        ->first()->getKey()->toBe($option->getKey());
});
