<?php

declare(strict_types=1);

use App\Enums\StockMovementTypeEnum;
use App\Models\Branch;
use App\Models\ProductItem;
use App\Models\ProductStockMovement;
use App\Models\Student;
use App\Models\User;

test('it prevents mass assignment to guarded id', function () {
    // Arrange
    $customId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

    // Act
    $movement = ProductStockMovement::factory()->create(['id' => $customId]);

    // Note: Factory uses forceCreate which bypasses guarded.
    // Testing via direct create:
    $item = ProductItem::factory()->create();
    $branch = Branch::factory()->create();
    $user = User::factory()->create();

    $movement = ProductStockMovement::create([
        'id' => $customId,
        'user_id' => $user->getKey(),
        'branch_id' => $branch->getKey(),
        'product_item_id' => $item->getKey(),
        'type' => StockMovementTypeEnum::STOCK_IN->value,
        'quantity' => 10,
        'purchase_price' => 50000,
        'sale_price' => 75000,
    ]);

    // Assert
    expect($movement->getKey())
        ->toBeString()
        ->not->toBe($customId);
});

test('type casts to StockMovementTypeEnum', function () {
    // Arrange & Act
    $movement = ProductStockMovement::factory()->create([
        'type' => StockMovementTypeEnum::STOCK_IN,
    ]);

    // Assert
    expect($movement->type)->toBe(StockMovementTypeEnum::STOCK_IN);
});

test('item relationship returns related product item', function () {
    // Arrange
    $item = ProductItem::factory()->create();
    $movement = ProductStockMovement::factory()->create(['product_item_id' => $item->getKey()]);

    // Act & Assert
    expect($movement->item)
        ->toBeInstanceOf(ProductItem::class)
        ->getKey()->toBe($item->getKey());
});

test('branch relationship returns related branch', function () {
    // Arrange
    $branch = Branch::factory()->create();
    $movement = ProductStockMovement::factory()->create(['branch_id' => $branch->getKey()]);

    // Act & Assert
    expect($movement->branch)
        ->toBeInstanceOf(Branch::class)
        ->getKey()->toBe($branch->getKey());
});

test('user relationship returns related user', function () {
    // Arrange
    $user = User::factory()->create();
    $movement = ProductStockMovement::factory()->create(['user_id' => $user->getKey()]);

    // Act & Assert
    expect($movement->user)
        ->toBeInstanceOf(User::class)
        ->getKey()->toBe($user->getKey());
});

test('student relationship returns related student', function () {
    // Arrange
    $student = Student::factory()->create();
    $movement = ProductStockMovement::factory()->create(['student_id' => $student->getKey()]);

    // Act & Assert
    expect($movement->student)
        ->toBeInstanceOf(Student::class)
        ->getKey()->toBe($student->getKey());
});

test('relatedMovement relationship returns related movement', function () {
    // Arrange
    $sourceMovement = ProductStockMovement::factory()->create();
    $pairedMovement = ProductStockMovement::factory()->create([
        'related_movement_id' => $sourceMovement->getKey(),
    ]);

    // Act & Assert
    expect($pairedMovement->relatedMovement)
        ->toBeInstanceOf(ProductStockMovement::class)
        ->getKey()->toBe($sourceMovement->getKey());
});
