<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductItem;
use App\Models\ProductStock;
use App\Models\ProductStockMovement;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;

test('it prevents mass assignment to guarded id', function () {
    // Arrange
    $customId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
    $product = Product::factory()->create();

    // Act
    $item = ProductItem::create([
        'id' => $customId,
        'product_id' => $product->getKey(),
        'sku' => 'TEST-SKU',
        'purchase_price' => 50000,
        'sale_price' => 75000,
        'is_active' => true,
    ]);

    // Assert
    expect($item->getKey())
        ->toBeString()
        ->not->toBe($customId);
});

test('it casts the columns')
    ->expect(fn () => ProductItem::factory()->create([
        'purchase_price' => 50000,
        'sale_price' => 75000,
        'sort_order' => '7',
        'is_active' => true,
    ]))
    ->purchase_price->toBeString()
    ->sale_price->toBeString()
    ->sort_order->toBeInt()
    ->is_active->toBeBool();

test('product relationship returns related product', function () {
    // Arrange
    $product = Product::factory()->create();
    $item = ProductItem::factory()->create(['product_id' => $product->getKey()]);

    // Act & Assert
    expect($item->product)
        ->toBeInstanceOf(Product::class)
        ->getKey()->toBe($product->getKey());
});

test('variationOptions relationship returns related options through pivot', function () {
    // Arrange
    $category = ProductCategory::factory()->create();
    $variation = ProductVariation::factory()->create(['product_category_id' => $category->getKey()]);
    $options = ProductVariationOption::factory(2)->create(['product_variation_id' => $variation->getKey()]);

    $product = Product::factory()->create(['product_category_id' => $category->getKey()]);
    $item = ProductItem::factory()->create(['product_id' => $product->getKey()]);
    $item->variationOptions()->attach($options->pluck('id'));

    // Act & Assert
    expect($item->variationOptions)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(ProductVariationOption::class);
});

test('stocks relationship returns related stocks', function () {
    // Arrange
    $item = ProductItem::factory()->create();
    ProductStock::factory()->create(['product_item_id' => $item->getKey()]);

    // Act & Assert
    expect($item->stocks)
        ->toHaveCount(1)
        ->first()->toBeInstanceOf(ProductStock::class);
});

test('movements relationship returns related movements', function () {
    // Arrange
    $item = ProductItem::factory()->create();
    ProductStockMovement::factory()->create(['product_item_id' => $item->getKey()]);

    // Act & Assert
    expect($item->movements)
        ->toHaveCount(1)
        ->first()->toBeInstanceOf(ProductStockMovement::class);
});

test('active scope filters only active items', function () {
    // Arrange
    ProductItem::factory()->create(['is_active' => true]);
    ProductItem::factory()->create(['is_active' => false]);

    // Act & Assert
    expect(ProductItem::active()->count())->toBe(1);
});

test('inactive scope filters only inactive items', function () {
    // Arrange
    ProductItem::factory()->create(['is_active' => true]);
    ProductItem::factory()->create(['is_active' => false]);

    // Act & Assert
    expect(ProductItem::inactive()->count())->toBe(1);
});

test('isActive and isInactive reflect current state', function () {
    // Arrange
    $activeItem = ProductItem::factory()->create(['is_active' => true]);
    $inactiveItem = ProductItem::factory()->create(['is_active' => false]);

    // Act & Assert
    expect($activeItem->isActive())
        ->toBeTrue()
        ->and($activeItem->isInactive())->toBeFalse()
        ->and($inactiveItem->isActive())->toBeFalse()
        ->and($inactiveItem->isInactive())->toBeTrue();
});

test('deactivateOthers turns active product items inactive', function () {
    // Arrange
    [$first, $second] = ProductItem::factory(2)->create(['is_active' => true]);

    // Act
    ProductItem::deactivateOthers();

    // Assert
    expect($first->refresh()->is_active)
        ->toBeFalse()
        ->and($second->refresh()->is_active)->toBeFalse()
        ->and(ProductItem::query()->active()->count())->toBe(0);
});

test('activateExclusively activates current item and deactivates others', function () {
    // Arrange
    $first = ProductItem::factory()->create(['is_active' => true]);
    $second = ProductItem::factory()->create(['is_active' => false]);

    // Act
    $second->activateExclusively();

    // Assert
    expect($first->refresh()->is_active)
        ->toBeFalse()
        ->and($second->refresh()->is_active)->toBeTrue()
        ->and(ProductItem::query()->active()->count())->toBe(1);
});
