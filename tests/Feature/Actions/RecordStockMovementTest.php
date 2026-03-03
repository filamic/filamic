<?php

declare(strict_types=1);

use App\Actions\RecordStockMovement;
use App\Enums\StockMovementTypeEnum;
use App\Models\Branch;
use App\Models\ProductItem;
use App\Models\ProductStock;
use App\Models\ProductStockMovement;
use App\Models\Student;
use App\Models\User;
use Illuminate\Validation\ValidationException;

test('cannot create movement without required fields', function () {
    expect(fn () => RecordStockMovement::run([]))
        ->toThrow(ValidationException::class);
});

test('STOCK_IN creates movement and increases stock', function () {
    // Arrange
    $user = User::factory()->create();
    $branch = Branch::factory()->create();
    $item = ProductItem::factory()->create();

    // Act
    $movement = RecordStockMovement::run([
        'user_id' => $user->getKey(),
        'branch_id' => $branch->getKey(),
        'product_item_id' => $item->getKey(),
        'type' => StockMovementTypeEnum::STOCK_IN->value,
        'quantity' => 50,
        'purchase_price' => 25000,
        'sale_price' => 40000,
    ]);

    // Assert
    expect($movement)
        ->toBeInstanceOf(ProductStockMovement::class)
        ->type->toBe(StockMovementTypeEnum::STOCK_IN)
        ->quantity->toBe(50);

    $stock = ProductStock::where('product_item_id', $item->getKey())
        ->where('branch_id', $branch->getKey())
        ->first();

    expect($stock)->not->toBeNull()
        ->quantity->toBe(50);
});

test('STOCK_IN creates stock record if none exists', function () {
    // Arrange
    $user = User::factory()->create();
    $branch = Branch::factory()->create();
    $item = ProductItem::factory()->create();

    expect(ProductStock::count())->toBe(0);

    // Act
    RecordStockMovement::run([
        'user_id' => $user->getKey(),
        'branch_id' => $branch->getKey(),
        'product_item_id' => $item->getKey(),
        'type' => StockMovementTypeEnum::STOCK_IN->value,
        'quantity' => 10,
        'purchase_price' => 25000,
        'sale_price' => 40000,
    ]);

    // Assert
    expect(ProductStock::count())->toBe(1);
});

test('DISTRIBUTION decreases stock', function () {
    // Arrange
    $user = User::factory()->create();
    $branch = Branch::factory()->create();
    $item = ProductItem::factory()->create();
    ProductStock::create([
        'product_item_id' => $item->getKey(),
        'branch_id' => $branch->getKey(),
        'quantity' => 100,
    ]);

    // Act
    $movement = RecordStockMovement::run([
        'user_id' => $user->getKey(),
        'branch_id' => $branch->getKey(),
        'product_item_id' => $item->getKey(),
        'type' => StockMovementTypeEnum::DISTRIBUTION->value,
        'quantity' => 30,
        'purchase_price' => 25000,
        'sale_price' => 40000,
    ]);

    // Assert
    expect($movement->quantity)->toBe(-30);

    $stock = ProductStock::where('product_item_id', $item->getKey())
        ->where('branch_id', $branch->getKey())
        ->first();

    expect($stock->quantity)->toBe(70);
});

test('DIRECT_SALE decreases stock', function () {
    // Arrange
    $user = User::factory()->create();
    $branch = Branch::factory()->create();
    $item = ProductItem::factory()->create();
    ProductStock::create([
        'product_item_id' => $item->getKey(),
        'branch_id' => $branch->getKey(),
        'quantity' => 50,
    ]);

    // Act
    RecordStockMovement::run([
        'user_id' => $user->getKey(),
        'branch_id' => $branch->getKey(),
        'product_item_id' => $item->getKey(),
        'type' => StockMovementTypeEnum::DIRECT_SALE->value,
        'quantity' => 20,
        'purchase_price' => 25000,
        'sale_price' => 40000,
    ]);

    // Assert
    $stock = ProductStock::where('product_item_id', $item->getKey())
        ->where('branch_id', $branch->getKey())
        ->first();

    expect($stock->quantity)->toBe(30);
});

test('cannot create movement that would cause negative stock', function () {
    // Arrange
    $user = User::factory()->create();
    $branch = Branch::factory()->create();
    $item = ProductItem::factory()->create();
    ProductStock::create([
        'product_item_id' => $item->getKey(),
        'branch_id' => $branch->getKey(),
        'quantity' => 5,
    ]);

    // Act & Assert
    expect(fn () => RecordStockMovement::run([
        'user_id' => $user->getKey(),
        'branch_id' => $branch->getKey(),
        'product_item_id' => $item->getKey(),
        'type' => StockMovementTypeEnum::DISTRIBUTION->value,
        'quantity' => 10,
        'purchase_price' => 25000,
        'sale_price' => 40000,
    ]))->toThrow(ValidationException::class);

    // Stock unchanged
    expect(ProductStock::where('product_item_id', $item->getKey())
        ->where('branch_id', $branch->getKey())
        ->first()->quantity)->toBe(5);
});

test('TRANSFER_OUT auto-creates paired TRANSFER_IN', function () {
    // Arrange
    $user = User::factory()->create();
    $sourceBranch = Branch::factory()->create();
    $destBranch = Branch::factory()->create();
    $item = ProductItem::factory()->create();
    ProductStock::create([
        'product_item_id' => $item->getKey(),
        'branch_id' => $sourceBranch->getKey(),
        'quantity' => 100,
    ]);

    // Act
    $movement = RecordStockMovement::run([
        'user_id' => $user->getKey(),
        'branch_id' => $sourceBranch->getKey(),
        'product_item_id' => $item->getKey(),
        'type' => StockMovementTypeEnum::TRANSFER_OUT->value,
        'quantity' => 25,
        'purchase_price' => 25000,
        'sale_price' => 40000,
        'destination_branch_id' => $destBranch->getKey(),
    ]);

    // Assert — two movements created
    expect(ProductStockMovement::count())->toBe(2);

    // Source movement
    expect($movement)
        ->type->toBe(StockMovementTypeEnum::TRANSFER_OUT)
        ->quantity->toBe(-25)
        ->branch_id->toBe($sourceBranch->getKey());

    // Paired TRANSFER_IN
    $transferIn = ProductStockMovement::where('type', StockMovementTypeEnum::TRANSFER_IN)->first();
    expect($transferIn)
        ->not->toBeNull()
        ->quantity->toBe(25)
        ->branch_id->toBe($destBranch->getKey())
        ->related_movement_id->toBe($movement->getKey());

    // Source movement points back to TRANSFER_IN
    expect($movement->refresh()->related_movement_id)->toBe($transferIn->getKey());

    // Source branch stock decreased
    $sourceStock = ProductStock::where('product_item_id', $item->getKey())
        ->where('branch_id', $sourceBranch->getKey())
        ->first();
    expect($sourceStock->quantity)->toBe(75);

    // Destination branch stock increased
    $destStock = ProductStock::where('product_item_id', $item->getKey())
        ->where('branch_id', $destBranch->getKey())
        ->first();
    expect($destStock->quantity)->toBe(25);
});

test('TRANSFER_OUT requires destination_branch_id', function () {
    // Arrange
    $user = User::factory()->create();
    $branch = Branch::factory()->create();
    $item = ProductItem::factory()->create();
    ProductStock::create([
        'product_item_id' => $item->getKey(),
        'branch_id' => $branch->getKey(),
        'quantity' => 100,
    ]);

    // Act & Assert
    expect(fn () => RecordStockMovement::run([
        'user_id' => $user->getKey(),
        'branch_id' => $branch->getKey(),
        'product_item_id' => $item->getKey(),
        'type' => StockMovementTypeEnum::TRANSFER_OUT->value,
        'quantity' => 10,
        'purchase_price' => 25000,
        'sale_price' => 40000,
    ]))->toThrow(ValidationException::class);
});

test('ADJUSTMENT can increase stock with positive quantity', function () {
    // Arrange
    $user = User::factory()->create();
    $branch = Branch::factory()->create();
    $item = ProductItem::factory()->create();
    ProductStock::create([
        'product_item_id' => $item->getKey(),
        'branch_id' => $branch->getKey(),
        'quantity' => 10,
    ]);

    // Act
    $movement = RecordStockMovement::run([
        'user_id' => $user->getKey(),
        'branch_id' => $branch->getKey(),
        'product_item_id' => $item->getKey(),
        'type' => StockMovementTypeEnum::ADJUSTMENT->value,
        'quantity' => 5,
        'purchase_price' => 25000,
        'sale_price' => 40000,
        'notes' => 'Found extra stock during audit',
    ]);

    // Assert
    expect($movement->quantity)->toBe(5);

    $stock = ProductStock::where('product_item_id', $item->getKey())
        ->where('branch_id', $branch->getKey())
        ->first();
    expect($stock->quantity)->toBe(15);
});

test('ADJUSTMENT can decrease stock with negative quantity', function () {
    // Arrange
    $user = User::factory()->create();
    $branch = Branch::factory()->create();
    $item = ProductItem::factory()->create();
    ProductStock::create([
        'product_item_id' => $item->getKey(),
        'branch_id' => $branch->getKey(),
        'quantity' => 20,
    ]);

    // Act
    $movement = RecordStockMovement::run([
        'user_id' => $user->getKey(),
        'branch_id' => $branch->getKey(),
        'product_item_id' => $item->getKey(),
        'type' => StockMovementTypeEnum::ADJUSTMENT->value,
        'quantity' => -5,
        'purchase_price' => 25000,
        'sale_price' => 40000,
        'notes' => 'Damaged stock removed',
    ]);

    // Assert
    expect($movement->quantity)->toBe(-5);

    $stock = ProductStock::where('product_item_id', $item->getKey())
        ->where('branch_id', $branch->getKey())
        ->first();
    expect($stock->quantity)->toBe(15);
});

test('records student_id when provided', function () {
    // Arrange
    $user = User::factory()->create();
    $branch = Branch::factory()->create();
    $item = ProductItem::factory()->create();
    $student = Student::factory()->create();
    ProductStock::create([
        'product_item_id' => $item->getKey(),
        'branch_id' => $branch->getKey(),
        'quantity' => 100,
    ]);

    // Act
    $movement = RecordStockMovement::run([
        'user_id' => $user->getKey(),
        'branch_id' => $branch->getKey(),
        'product_item_id' => $item->getKey(),
        'type' => StockMovementTypeEnum::DISTRIBUTION->value,
        'quantity' => 1,
        'purchase_price' => 25000,
        'sale_price' => 40000,
        'student_id' => $student->getKey(),
    ]);

    // Assert
    expect($movement->student_id)->toBe($student->getKey());
});

test('wraps creation in a database transaction', function () {
    // Arrange
    $user = User::factory()->create();
    $branch = Branch::factory()->create();
    $item = ProductItem::factory()->create();
    ProductStock::create([
        'product_item_id' => $item->getKey(),
        'branch_id' => $branch->getKey(),
        'quantity' => 5,
    ]);

    // Act — try to distribute more than available
    try {
        RecordStockMovement::run([
            'user_id' => $user->getKey(),
            'branch_id' => $branch->getKey(),
            'product_item_id' => $item->getKey(),
            'type' => StockMovementTypeEnum::DISTRIBUTION->value,
            'quantity' => 999,
            'purchase_price' => 25000,
            'sale_price' => 40000,
        ]);
    } catch (Throwable) {
        // Expected
    }

    // Assert — no movement created, stock unchanged
    expect(ProductStockMovement::count())->toBe(0);
    expect(ProductStock::where('product_item_id', $item->getKey())
        ->where('branch_id', $branch->getKey())
        ->first()->quantity)->toBe(5);
});
