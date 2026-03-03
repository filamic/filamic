<?php

declare(strict_types=1);

use App\Enums\GradeEnum;
use App\Enums\LevelEnum;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductItem;
use App\Models\ProductStock;
use App\Models\ProductStockMovement;
use App\Models\Supplier;

test('it prevents mass assignment to guarded id', function () {
    // Arrange
    $customId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
    $supplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create();

    // Act
    $product = Product::create([
        'id' => $customId,
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
        'level' => LevelEnum::ELEMENTARY->value,
        'name' => 'Test Product',
    ]);

    // Assert
    expect($product->getKey())
        ->toBeString()
        ->not->toBe($customId);
});

test('it casts the columns')
    ->expect(fn () => Product::factory()->create([
        'level' => LevelEnum::ELEMENTARY,
        'grade' => GradeEnum::GRADE_1,
    ]))
    ->level->toBeInstanceOf(LevelEnum::class)
    ->grade->toBeInstanceOf(GradeEnum::class);

test('fingerprint is generated with all components', function () {
    // Arrange
    $supplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create();

    // Act
    $fingerprint = Product::generateFingerprint([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
        'name' => 'Matematika',
        'level' => LevelEnum::ELEMENTARY,
        'grade' => GradeEnum::GRADE_1,
    ]);

    // Assert
    expect($fingerprint)->toBe($supplier->getKey() . ':' . $category->getKey() . ':matematika:2:5');
});

test('fingerprint excludes optional components when blank', function () {
    // Arrange
    $supplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create();

    // Act
    $fingerprint = Product::generateFingerprint([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
        'name' => 'Pensil 2B',
    ]);

    // Assert
    expect($fingerprint)->toBe($supplier->getKey() . ':' . $category->getKey() . ':pensil 2b');
});

test('fingerprint throws on missing required component', function () {
    expect(fn () => Product::generateFingerprint([
        'supplier_id' => 'abc',
        'name' => 'Test',
    ]))->toThrow(InvalidArgumentException::class, 'Component [product_category_id] is required');
});

test('fingerprint is auto-generated on create', function () {
    // Arrange & Act
    $product = Product::factory()->create([
        'level' => LevelEnum::ELEMENTARY,
        'grade' => GradeEnum::GRADE_1,
        'name' => 'Matematika',
    ]);

    // Assert
    expect($product->fingerprint)
        ->toBeString()
        ->toContain($product->supplier_id)
        ->toContain($product->product_category_id)
        ->toContain('matematika')
        ->toContain(':2:5');
});

test('fingerprint is regenerated on update', function () {
    // Arrange
    $product = Product::factory()->create(['name' => 'Original']);
    $originalFingerprint = $product->fingerprint;

    // Act
    $product->update(['name' => 'Updated']);

    // Assert
    expect($product->fingerprint)
        ->not->toBe($originalFingerprint)
        ->toContain('updated');
});

test('duplicate fingerprint is rejected', function () {
    // Arrange
    $supplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create();

    Product::factory()->create([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
        'level' => LevelEnum::ELEMENTARY,
        'grade' => GradeEnum::GRADE_1,
        'name' => 'Same Product',
    ]);

    // Act & Assert
    expect(fn () => Product::factory()->create([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
        'level' => LevelEnum::ELEMENTARY,
        'grade' => GradeEnum::GRADE_1,
        'name' => 'Same Product',
    ]))->toThrow(Illuminate\Database\UniqueConstraintViolationException::class);
});

test('supplier relationship returns related supplier', function () {
    // Arrange
    $supplier = Supplier::factory()->create();
    $product = Product::factory()->create(['supplier_id' => $supplier->getKey()]);

    // Act & Assert
    expect($product->supplier)
        ->toBeInstanceOf(Supplier::class)
        ->getKey()->toBe($supplier->getKey());
});

test('category relationship returns related category', function () {
    // Arrange
    $category = ProductCategory::factory()->create();
    $product = Product::factory()->create(['product_category_id' => $category->getKey()]);

    // Act & Assert
    expect($product->category)
        ->toBeInstanceOf(ProductCategory::class)
        ->getKey()->toBe($category->getKey());
});

test('items relationship returns related items', function () {
    // Arrange
    $product = Product::factory()->create();
    ProductItem::factory(3)->create(['product_id' => $product->getKey()]);

    // Act & Assert
    expect($product->items)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(ProductItem::class);
});

test('stocks relationship returns stocks through items', function () {
    // Arrange
    $product = Product::factory()->create();
    $item = ProductItem::factory()->create(['product_id' => $product->getKey()]);
    ProductStock::factory()->create(['product_item_id' => $item->getKey()]);

    // Act & Assert
    expect($product->stocks)
        ->toHaveCount(1)
        ->first()->toBeInstanceOf(ProductStock::class);
});

test('stockMovements relationship returns movements through items', function () {
    // Arrange
    $product = Product::factory()->create();
    $item = ProductItem::factory()->create(['product_id' => $product->getKey()]);
    ProductStockMovement::factory()->create(['product_item_id' => $item->getKey()]);

    // Act & Assert
    expect($product->stockMovements)
        ->toHaveCount(1)
        ->first()->toBeInstanceOf(ProductStockMovement::class);
});

test('generateSku generates correct SKU from category code and product name', function () {
    // Act
    $sku = Product::generateSku('SRG', 'Kemeja Putih');

    // Assert
    expect($sku)->toBe('SRG-KEMEJA-PUTIH');
});

test('generateSku generates correct SKU with variation option names', function () {
    // Act
    $sku = Product::generateSku('SRG', 'Kemeja Putih', ['S', 'Merah']);

    // Assert
    expect($sku)->toBe('SRG-KEMEJA-PUTIH-S-MERAH');
});

test('generateSku truncates long product names', function () {
    // Act
    $sku = Product::generateSku('SRG', 'Kemeja Putih Lengan Panjang Formal');

    // Assert
    $parts = explode('-', $sku);
    $namePartLength = mb_strlen(implode('-', array_slice($parts, 1)));
    expect($namePartLength)->toBeLessThanOrEqual(20);
});

test('generateSku handles empty option names array', function () {
    // Act
    $sku = Product::generateSku('BK', 'Matematika', []);

    // Assert
    expect($sku)->toBe('BK-MATEMATIKA');
});
