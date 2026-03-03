<?php

declare(strict_types=1);

use App\Models\ProductCategory;
use App\Models\ProductItem;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;

test('it prevents mass assignment to guarded id', function () {
    // Arrange
    $customId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
    $variation = ProductVariation::factory()->create();

    // Act
    $option = ProductVariationOption::create([
        'id' => $customId,
        'product_variation_id' => $variation->getKey(),
        'name' => 'Biru',
    ]);

    // Assert
    expect($option->getKey())
        ->toBeString()
        ->not->toBe($customId);
});

test('formatted_name accessor combines variation and option name', function () {
    // Arrange
    $category = ProductCategory::factory()->create();
    $variation = ProductVariation::factory()->create([
        'product_category_id' => $category->getKey(),
        'name' => 'Ukuran',
    ]);

    $option = ProductVariationOption::factory()->create([
        'product_variation_id' => $variation->getKey(),
        'name' => 'XL',
    ]);

    // Act
    $formattedName = $option->formatted_name;

    // Assert
    expect($formattedName)->toBe('Ukuran: XL');
});

test('variation relationship returns related variation', function () {
    // Arrange
    $variation = ProductVariation::factory()->create();
    $option = ProductVariationOption::factory()->create(['product_variation_id' => $variation->getKey()]);

    // Act
    $relatedVariation = $option->variation;

    // Assert
    expect($relatedVariation)
        ->toBeInstanceOf(ProductVariation::class)
        ->getKey()->toBe($variation->getKey());
});

test('productItems relationship returns related product items through pivot', function () {
    // Arrange
    $option = ProductVariationOption::factory()->create();
    $itemOne = ProductItem::factory()->create();
    $itemTwo = ProductItem::factory()->create();

    $option->productItems()->attach([$itemOne->getKey(), $itemTwo->getKey()]);

    // Act
    $productItems = $option->productItems;

    // Assert
    expect($productItems)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(ProductItem::class);
});

test('it isolates productItems by variation option', function () {
    // Arrange
    $option = ProductVariationOption::factory()->create();
    $otherOption = ProductVariationOption::factory()->create();

    $item = ProductItem::factory()->create();
    $otherItem = ProductItem::factory()->create();

    $option->productItems()->attach($item->getKey());
    $otherOption->productItems()->attach($otherItem->getKey());

    // Act
    $productItems = $option->productItems;

    // Assert
    expect($productItems)
        ->toHaveCount(1)
        ->first()->getKey()->toBe($item->getKey());
});
