<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\ProductItem;
use App\Models\Supplier;

test('it prevents mass assignment to guarded id', function () {
    // Arrange
    $customId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

    // Act
    $supplier = Supplier::create([
        'id' => $customId,
        'name' => 'Test Supplier',
    ]);

    // Assert
    expect($supplier->getKey())
        ->toBeString()
        ->not->toBe($customId);
});

test('products relationship returns related products', function () {
    // Arrange
    $supplier = Supplier::factory()->create();
    Product::factory(3)->create(['supplier_id' => $supplier->getKey()]);

    // Act & Assert
    expect($supplier->products)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Product::class);
});

test('productItems relationship returns product items through products', function () {
    // Arrange
    $supplier = Supplier::factory()->create();
    $product = Product::factory()->create(['supplier_id' => $supplier->getKey()]);
    ProductItem::factory(2)->create(['product_id' => $product->getKey()]);

    // Act
    $items = $supplier->productItems;

    // Assert
    expect($items)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(ProductItem::class);
});

test('it isolates productItems by supplier', function () {
    // Arrange
    $supplier = Supplier::factory()->create();
    $otherSupplier = Supplier::factory()->create();

    $product = Product::factory()->create(['supplier_id' => $supplier->getKey()]);
    $otherProduct = Product::factory()->create(['supplier_id' => $otherSupplier->getKey()]);

    $item = ProductItem::factory()->create(['product_id' => $product->getKey()]);
    ProductItem::factory()->create(['product_id' => $otherProduct->getKey()]);

    // Act
    $items = $supplier->productItems;

    // Assert
    expect($items)
        ->toHaveCount(1)
        ->first()->getKey()->toBe($item->getKey());
});
