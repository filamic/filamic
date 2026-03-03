<?php

declare(strict_types=1);

use App\Enums\LevelEnum;
use App\Filament\SupplyHub\Resources\Products\Pages\CreateProduct;
use App\Filament\SupplyHub\Resources\Products\Pages\EditProduct;
use App\Filament\SupplyHub\Resources\Products\Pages\ListProducts;
use App\Filament\SupplyHub\Resources\Products\ProductResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductItem;
use App\Models\Supplier;
use Livewire\Livewire;

beforeEach(fn () => $this->loginSupplyHub());

test('list page is accessible', function () {
    $this->get(ProductResource::getUrl())->assertOk();
});

test('list page renders columns', function (string $column) {
    // Arrange
    $product = Product::factory()->create();
    ProductItem::factory()->create(['product_id' => $product->getKey()]);

    // Act & Assert
    Livewire::test(ListProducts::class)
        ->assertCanRenderTableColumn($column);
})->with([
    'name',
    'items.variationOptions.name',
]);

test('list page shows rows', function () {
    // Arrange — use KINDERGARTEN level to match the default active tab
    $records = Product::factory(3)->create(['level' => LevelEnum::KINDERGARTEN]);

    // Act & Assert
    Livewire::test(ListProducts::class)
        ->assertCanSeeTableRecords($records);
});

test('can search for records on list page', function () {
    // Arrange — use KINDERGARTEN level to match the default active tab
    $record = Product::factory()->create(['name' => 'Produk Unik XYZ', 'level' => LevelEnum::KINDERGARTEN]);
    Product::factory()->create(['name' => 'Produk Lain', 'level' => LevelEnum::KINDERGARTEN]);

    // Act & Assert
    Livewire::test(ListProducts::class)
        ->searchTable('Produk Unik XYZ')
        ->assertCanSeeTableRecords([$record]);
});

test('create page is accessible', function () {
    $this->get(ProductResource::getUrl('create'))->assertOk();
});

// test('cannot create a record without required fields', function () {
//     Livewire::test(CreateProduct::class)
//         ->call('create')
//         ->assertHasFormErrors([
//             'supplier_id' => 'required',
//             'product_category_id' => 'required',
//             'name' => 'required',
//         ]);
// });

// TODO: please implmenet this in SupplierResourceTest or ProductItemsRelationManager since we move the form to Action Modal inside EditSupplier
// test('can create a product with single item', function () {
//     // Arrange
//     $supplier = Supplier::factory()->create();
//     $category = ProductCategory::factory()->create(['code' => 'BK']);

//     // Act
//     Livewire::test(CreateProduct::class)
//         ->fillForm([
//             'supplier_id' => $supplier->getKey(),
//             'product_category_id' => $category->getKey(),
//             'level' => LevelEnum::ELEMENTARY->value,
//             'name' => 'Matematika Kelas 1',
//             'purchase_price' => '25000',
//             'sale_price' => '40000',
//         ])
//         ->call('create')
//         ->assertHasNoFormErrors();

//     // Assert
//     expect(Product::count())->toBe(1);
//     expect(ProductItem::count())->toBe(1);

//     $product = Product::first();
//     expect($product)
//         ->name->toBe('Matematika Kelas 1')
//         ->level->toBe(LevelEnum::ELEMENTARY);

//     $item = ProductItem::first();
//     expect($item)
//         ->sku->toBe(Product::generateSku('BK', 'Matematika Kelas 1'))
//         ->purchase_price->toBe('25000.00')
//         ->sale_price->toBe('40000.00');
// });

test('edit page is accessible', function () {
    // Arrange
    $category = ProductCategory::factory()->create(['code' => 'BK']);
    $product = Product::factory()->create(['product_category_id' => $category->getKey()]);
    ProductItem::factory()->create(['product_id' => $product->getKey()]);

    // Act & Assert
    $this->get(ProductResource::getUrl('edit', ['record' => $product]))->assertOk();
});

// test('cannot save a record without required fields', function () {
//     // Arrange
//     $category = ProductCategory::factory()->create(['code' => 'BK']);
//     $product = Product::factory()->create(['product_category_id' => $category->getKey()]);
//     ProductItem::factory()->create(['product_id' => $product->getKey()]);

//     // Act & Assert
//     Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
//         ->fillForm(['name' => null])
//         ->call('save')
//         ->assertHasFormErrors(['name' => 'required']);
// });

// test('can save a record', function () {
//     // Arrange
//     $category = ProductCategory::factory()->create(['code' => 'BK']);
//     $product = Product::factory()->create([
//         'product_category_id' => $category->getKey(),
//         'name' => 'Original Name',
//     ]);
//     ProductItem::factory()->create([
//         'product_id' => $product->getKey(),
//         'sku' => 'BK-ORIGINAL',
//     ]);
//     $newSupplier = Supplier::factory()->create();

//     // Act
//     Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
//         ->fillForm([
//             'supplier_id' => $newSupplier->getKey(),
//             'name' => 'Updated Name',
//         ])
//         ->call('save')
//         ->assertHasNoFormErrors();

//     // Assert
//     expect($product->refresh())
//         ->name->toBe('Updated Name')
//         ->supplier_id->toBe($newSupplier->getKey());
// });

// test('can save a record without changes', function () {
//     // Arrange
//     $category = ProductCategory::factory()->create(['code' => 'BK']);
//     $product = Product::factory()->create([
//         'product_category_id' => $category->getKey(),
//     ]);
//     ProductItem::factory()->create([
//         'product_id' => $product->getKey(),
//         'sku' => 'BK-TEST',
//     ]);

//     // Act & Assert
//     Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
//         ->call('save')
//         ->assertHasNoFormErrors();
// });
