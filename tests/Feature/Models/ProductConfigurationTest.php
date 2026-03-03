<?php

declare(strict_types=1);

use App\Models\ProductCategory;
use App\Models\ProductConfiguration;
use App\Models\ProductItem;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;

test('item relationship returns related product item', function () {
    // Arrange
    $item = ProductItem::factory()->create();
    $category = ProductCategory::factory()->create();
    $variation = ProductVariation::factory()->create(['product_category_id' => $category->getKey()]);
    $option = ProductVariationOption::factory()->create(['product_variation_id' => $variation->getKey()]);

    $item->variationOptions()->attach($option->getKey());

    $configuration = ProductConfiguration::query()->firstOrFail();

    // Act
    $relatedItem = $configuration->item;

    // Assert
    expect($relatedItem)
        ->toBeInstanceOf(ProductItem::class)
        ->getKey()->toBe($item->getKey());
});

test('variationOption relationship returns related variation option', function () {
    // Arrange
    $item = ProductItem::factory()->create();
    $category = ProductCategory::factory()->create();
    $variation = ProductVariation::factory()->create(['product_category_id' => $category->getKey()]);
    $option = ProductVariationOption::factory()->create(['product_variation_id' => $variation->getKey()]);

    $item->variationOptions()->attach($option->getKey());

    $configuration = ProductConfiguration::query()->firstOrFail();

    // Act
    $relatedOption = $configuration->variationOption;

    // Assert
    expect($relatedOption)
        ->toBeInstanceOf(ProductVariationOption::class)
        ->getKey()->toBe($option->getKey());
});

test('it isolates pivot rows by product item', function () {
    // Arrange
    $category = ProductCategory::factory()->create();
    $variation = ProductVariation::factory()->create(['product_category_id' => $category->getKey()]);

    $optionOne = ProductVariationOption::factory()->create(['product_variation_id' => $variation->getKey()]);
    $optionTwo = ProductVariationOption::factory()->create(['product_variation_id' => $variation->getKey()]);

    $itemOne = ProductItem::factory()->create();
    $itemTwo = ProductItem::factory()->create();

    $itemOne->variationOptions()->attach($optionOne->getKey());
    $itemTwo->variationOptions()->attach($optionTwo->getKey());

    // Act
    $configurations = ProductConfiguration::query()
        ->where('product_item_id', $itemOne->getKey())
        ->get();

    // Assert
    expect($configurations)
        ->toHaveCount(1)
        ->first()->product_variation_option_id->toBe($optionOne->getKey());
});
