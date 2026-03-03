<?php

declare(strict_types=1);

use App\Actions\CreateProductWithItems;
use App\Enums\LevelEnum;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductItem;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;
use App\Models\Supplier;
use Illuminate\Validation\ValidationException;

test('cannot create product without required fields', function () {
    expect(fn () => CreateProductWithItems::run([]))
        ->toThrow(ValidationException::class);
});

test('can create product with single item when category has no variations', function () {
    $supplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create(['code' => 'BK']);

    $product = CreateProductWithItems::run([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
        'level' => LevelEnum::ELEMENTARY->value,
        'name' => 'Matematika Kelas 1',
        'description' => null,
        'purchase_price' => 25000,
        'sale_price' => 40000,
    ]);

    expect($product)
        ->toBeInstanceOf(Product::class)
        ->name->toBe('Matematika Kelas 1');

    expect($product->items)->toHaveCount(1);

    $item = $product->items->first();
    expect($item)
        ->sku->toBe(Product::generateSku('BK', 'Matematika Kelas 1'))
        ->purchase_price->toBe('25000.00')
        ->sale_price->toBe('40000.00')
        ->is_active->toBeTrue();
});

test('auto-generates SKU for single item using category code and product name', function () {
    $supplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create(['code' => 'ATK']);

    $product = CreateProductWithItems::run([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
        'level' => LevelEnum::ELEMENTARY->value,
        'name' => 'Pensil 2B',
        'description' => null,
        'purchase_price' => 3000,
        'sale_price' => 5000,
    ]);

    $expectedSku = Product::generateSku('ATK', 'Pensil 2B');

    expect($product->items->first()->sku)->toBe($expectedSku);
});

test('can create product with variant items from cartesian product', function () {
    $supplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create(['code' => 'SRG']);
    $sizeVariation = ProductVariation::factory()->create(['product_category_id' => $category->getKey(), 'name' => 'Ukuran']);
    $sizeS = ProductVariationOption::factory()->create(['product_variation_id' => $sizeVariation->getKey(), 'name' => 'S']);
    $sizeM = ProductVariationOption::factory()->create(['product_variation_id' => $sizeVariation->getKey(), 'name' => 'M']);

    $product = CreateProductWithItems::run([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
        'level' => LevelEnum::ELEMENTARY->value,
        'name' => 'Kemeja Putih',
        'description' => null,
        'purchase_price' => 50000,
        'sale_price' => 75000,
        'variations' => [
            str()->uuid()->toString() => [
                'variation_id' => $sizeVariation->getKey(),
                'variation_name' => 'Ukuran',
                'selected_options' => [$sizeS->getKey(), $sizeM->getKey()],
            ],
        ],
    ]);

    expect($product->items)->toHaveCount(2);

    $skus = $product->items->pluck('sku')->sort()->values()->all();

    expect($skus)->toBe([
        Product::generateSku('SRG', 'Kemeja Putih', ['M']),
        Product::generateSku('SRG', 'Kemeja Putih', ['S']),
    ]);
});

test('generates correct cartesian product for multiple variations', function () {
    $supplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create(['code' => 'SRG']);

    $sizeVariation = ProductVariation::factory()->create(['product_category_id' => $category->getKey(), 'name' => 'Ukuran']);
    $sizeS = ProductVariationOption::factory()->create(['product_variation_id' => $sizeVariation->getKey(), 'name' => 'S']);
    $sizeM = ProductVariationOption::factory()->create(['product_variation_id' => $sizeVariation->getKey(), 'name' => 'M']);
    $sizeL = ProductVariationOption::factory()->create(['product_variation_id' => $sizeVariation->getKey(), 'name' => 'L']);

    $colorVariation = ProductVariation::factory()->create(['product_category_id' => $category->getKey(), 'name' => 'Warna']);
    $colorRed = ProductVariationOption::factory()->create(['product_variation_id' => $colorVariation->getKey(), 'name' => 'Merah']);
    $colorBlue = ProductVariationOption::factory()->create(['product_variation_id' => $colorVariation->getKey(), 'name' => 'Biru']);

    $product = CreateProductWithItems::run([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
        'level' => LevelEnum::ELEMENTARY->value,
        'name' => 'Kemeja',
        'description' => null,
        'purchase_price' => 50000,
        'sale_price' => 75000,
        'variations' => [
            str()->uuid()->toString() => [
                'variation_id' => $sizeVariation->getKey(),
                'variation_name' => 'Ukuran',
                'selected_options' => [$sizeS->getKey(), $sizeM->getKey(), $sizeL->getKey()],
            ],
            str()->uuid()->toString() => [
                'variation_id' => $colorVariation->getKey(),
                'variation_name' => 'Warna',
                'selected_options' => [$colorRed->getKey(), $colorBlue->getKey()],
            ],
        ],
    ]);

    // 3 sizes × 2 colors = 6 items
    expect($product->items)->toHaveCount(6);

    // All items share the same price
    $product->items->each(function (ProductItem $item) {
        expect($item)
            ->purchase_price->toBe('50000.00')
            ->sale_price->toBe('75000.00')
            ->is_active->toBeTrue();
    });
});

test('creates product configurations pivot for variant items', function () {
    $supplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create(['code' => 'SRG']);

    $sizeVariation = ProductVariation::factory()->create(['product_category_id' => $category->getKey(), 'name' => 'Ukuran']);
    $colorVariation = ProductVariation::factory()->create(['product_category_id' => $category->getKey(), 'name' => 'Warna']);

    $sizeS = ProductVariationOption::factory()->create(['product_variation_id' => $sizeVariation->getKey(), 'name' => 'S']);
    $colorRed = ProductVariationOption::factory()->create(['product_variation_id' => $colorVariation->getKey(), 'name' => 'Merah']);

    $product = CreateProductWithItems::run([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
        'level' => LevelEnum::ELEMENTARY->value,
        'name' => 'Kemeja',
        'description' => null,
        'purchase_price' => 50000,
        'sale_price' => 75000,
        'variations' => [
            str()->uuid()->toString() => [
                'variation_id' => $sizeVariation->getKey(),
                'variation_name' => 'Ukuran',
                'selected_options' => [$sizeS->getKey()],
            ],
            str()->uuid()->toString() => [
                'variation_id' => $colorVariation->getKey(),
                'variation_name' => 'Warna',
                'selected_options' => [$colorRed->getKey()],
            ],
        ],
    ]);

    // 1 size × 1 color = 1 item
    expect($product->items)->toHaveCount(1);

    $item = $product->items->first();
    $item->load('variationOptions');

    expect($item->variationOptions)->toHaveCount(2);

    $optionIds = $item->variationOptions->pluck('id')->toArray();
    expect($optionIds)
        ->toContain($sizeS->getKey())
        ->toContain($colorRed->getKey());
});

test('auto-generates SKU with option names for variant items', function () {
    $supplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create(['code' => 'SRG']);

    $sizeVariation = ProductVariation::factory()->create(['product_category_id' => $category->getKey(), 'name' => 'Ukuran']);
    $colorVariation = ProductVariation::factory()->create(['product_category_id' => $category->getKey(), 'name' => 'Warna']);

    $sizeM = ProductVariationOption::factory()->create(['product_variation_id' => $sizeVariation->getKey(), 'name' => 'M']);
    $colorRed = ProductVariationOption::factory()->create(['product_variation_id' => $colorVariation->getKey(), 'name' => 'Merah']);

    $product = CreateProductWithItems::run([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
        'level' => LevelEnum::ELEMENTARY->value,
        'name' => 'Kemeja',
        'description' => null,
        'purchase_price' => 50000,
        'sale_price' => 75000,
        'variations' => [
            str()->uuid()->toString() => [
                'variation_id' => $sizeVariation->getKey(),
                'variation_name' => 'Ukuran',
                'selected_options' => [$sizeM->getKey()],
            ],
            str()->uuid()->toString() => [
                'variation_id' => $colorVariation->getKey(),
                'variation_name' => 'Warna',
                'selected_options' => [$colorRed->getKey()],
            ],
        ],
    ]);

    $item = $product->items->first();
    $expectedSku = Product::generateSku('SRG', 'Kemeja', ['M', 'Merah']);

    expect($item->sku)->toBe($expectedSku);
});

test('wraps creation in a database transaction', function () {
    $supplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create(['code' => 'BK']);

    // Create a product first to cause a SKU collision
    $existingProduct = Product::factory()->create([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
    ]);
    $existingProduct->items()->create([
        'sku' => Product::generateSku('BK', 'Duplicate Product'),
        'purchase_price' => 10000,
        'sale_price' => 20000,
        'is_active' => true,
    ]);

    try {
        CreateProductWithItems::run([
            'supplier_id' => $supplier->getKey(),
            'product_category_id' => $category->getKey(),
            'level' => LevelEnum::ELEMENTARY->value,
            'name' => 'Duplicate Product',
            'description' => null,
            'purchase_price' => 50000,
            'sale_price' => 75000,
        ]);
    } catch (Throwable) {
        // Expected — duplicate SKU triggers DB error
    }

    // Transaction rolled back — only the pre-existing product remains
    expect(Product::count())->toBe(1);
    expect(ProductItem::count())->toBe(1);
});
