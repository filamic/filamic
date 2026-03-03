<?php

declare(strict_types=1);

use App\Models\Branch;
use App\Models\ProductItem;
use App\Models\ProductStock;

test('it prevents mass assignment to guarded id', function () {
    // Arrange
    $customId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
    $item = ProductItem::factory()->create();
    $branch = Branch::factory()->create();

    // Act
    $stock = ProductStock::create([
        'id' => $customId,
        'product_item_id' => $item->getKey(),
        'branch_id' => $branch->getKey(),
        'quantity' => 10,
    ]);

    // Assert
    expect($stock->getKey())
        ->toBeString()
        ->not->toBe($customId);
});

test('item relationship returns related product item', function () {
    // Arrange
    $item = ProductItem::factory()->create();
    $stock = ProductStock::factory()->create(['product_item_id' => $item->getKey()]);

    // Act & Assert
    expect($stock->item)
        ->toBeInstanceOf(ProductItem::class)
        ->getKey()->toBe($item->getKey());
});

test('branch relationship returns related branch', function () {
    // Arrange
    $branch = Branch::factory()->create();
    $stock = ProductStock::factory()->create(['branch_id' => $branch->getKey()]);

    // Act & Assert
    expect($stock->branch)
        ->toBeInstanceOf(Branch::class)
        ->getKey()->toBe($branch->getKey());
});
