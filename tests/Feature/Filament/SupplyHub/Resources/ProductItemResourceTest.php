<?php

declare(strict_types=1);

use App\Filament\SupplyHub\Resources\ProductItems\Pages\ListProductItems;
use App\Filament\SupplyHub\Resources\ProductItems\ProductItemResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductItem;
use App\Models\Supplier;
use Livewire\Livewire;

beforeEach(fn () => $this->loginSupplyHub());

test('list page is accessible', function () {
    // Act
    $response = $this->get(ProductItemResource::getUrl());

    // Assert
    $response->assertOk();
});

test('list page renders columns', function (string $column) {
    // Arrange
    $supplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create();
    $product = Product::factory()->create([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
    ]);

    ProductItem::factory()->create(['product_id' => $product->getKey()]);

    // Act
    $component = Livewire::test(ListProductItems::class);

    // Assert
    $component->assertCanRenderTableColumn($column);
})->with([
    'product.name',
    'variationOptions.formatted_name',
    'purchase_price',
    'sale_price',
    'is_active',
]);

test('list page shows rows', function () {
    // Arrange
    $supplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create();
    $product = Product::factory()->create([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
    ]);

    $records = ProductItem::factory(3)->create(['product_id' => $product->getKey()]);

    // Act
    $component = Livewire::test(ListProductItems::class);

    // Assert
    $component->assertCanSeeTableRecords($records);
});
