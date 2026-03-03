<?php

declare(strict_types=1);

use App\Enums\GradeEnum;
use App\Enums\LevelEnum;
use App\Filament\SupplyHub\Resources\ProductItems\RelationManagers\ProductItemsRelationManager;
use App\Filament\SupplyHub\Resources\Suppliers\Pages\CreateSupplier;
use App\Filament\SupplyHub\Resources\Suppliers\Pages\EditSupplier;
use App\Filament\SupplyHub\Resources\Suppliers\Pages\ListSuppliers;
use App\Filament\SupplyHub\Resources\Suppliers\SupplierResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductItem;
use App\Models\Supplier;
use Livewire\Livewire;

beforeEach(fn () => $this->loginSupplyHub());

test('list page is accessible', function () {
    $this->get(SupplierResource::getUrl())->assertOk();
});

test('list page renders columns', function (string $column) {
    // Arrange
    Supplier::factory()->create();

    // Act & Assert
    Livewire::test(ListSuppliers::class)
        ->assertCanRenderTableColumn($column);
})->with([
    'name',
    'contact_person',
    'phone',
]);

test('list page shows rows', function () {
    // Arrange
    $records = Supplier::factory(3)->create();

    // Act & Assert
    Livewire::test(ListSuppliers::class)
        ->assertCanSeeTableRecords($records);
});

test('can search for records on list page', function (string $attribute) {
    // Arrange
    $record = Supplier::factory()->create([
        'name' => 'Supplier Unik ABC',
        'contact_person' => 'Budi Santoso Unik',
        'phone' => '089999888777',
    ]);

    Supplier::factory()->create([
        'name' => 'Lainnya',
        'contact_person' => 'Orang Lain',
        'phone' => '081111222333',
    ]);

    // Act & Assert
    Livewire::test(ListSuppliers::class)
        ->searchTable(data_get($record, $attribute))
        ->assertCanSeeTableRecords([$record]);
})->with([
    'name',
    'contact_person',
    'phone',
]);

test('create page is accessible', function () {
    $this->get(SupplierResource::getUrl('create'))->assertOk();
});

test('cannot create a record without required fields', function () {
    Livewire::test(CreateSupplier::class)
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

test('can create a record', function () {
    // Act
    Livewire::test(CreateSupplier::class)
        ->fillForm([
            'name' => 'PT Supplier Baru',
            'contact_person' => 'John Doe',
            'phone' => '081234567890',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    // Assert
    expect(Supplier::count())->toBe(1);

    $supplier = Supplier::first();
    expect($supplier)
        ->name->toBe('PT Supplier Baru')
        ->contact_person->toBe('John Doe')
        ->phone->toBe('081234567890');
});

test('cannot create a supplier with duplicate name', function () {
    // Arrange
    Supplier::factory()->create(['name' => 'PT Existing']);

    // Act & Assert
    Livewire::test(CreateSupplier::class)
        ->fillForm(['name' => 'PT Existing'])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});

test('edit page is accessible', function () {
    // Arrange
    $supplier = Supplier::factory()->create();

    // Act & Assert
    $this->get(SupplierResource::getUrl('edit', ['record' => $supplier]))->assertOk();
});

test('cannot save a record without required fields', function () {
    // Arrange
    $supplier = Supplier::factory()->create([
        'phone' => null,
    ]);

    // Act & Assert
    Livewire::test(EditSupplier::class, ['record' => $supplier->getRouteKey()])
        ->fillForm(['name' => null])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required']);
});

test('can save a record', function () {
    // Arrange
    $supplier = Supplier::factory()->create([
        'name' => 'Original',
        'phone' => null,
    ]);

    // Act
    Livewire::test(EditSupplier::class, ['record' => $supplier->getRouteKey()])
        ->fillForm(['name' => 'Updated Name'])
        ->call('save')
        ->assertHasNoFormErrors();

    // Assert
    expect($supplier->refresh()->name)->toBe('Updated Name');
});

test('product items relation manager only shows items for the selected supplier', function () {
    // Arrange
    $supplier = Supplier::factory()->create();
    $otherSupplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create();

    $product = Product::factory()->create([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
    ]);

    $otherProduct = Product::factory()->create([
        'supplier_id' => $otherSupplier->getKey(),
        'product_category_id' => $category->getKey(),
    ]);

    $item = ProductItem::factory()->create(['product_id' => $product->getKey()]);
    $otherItem = ProductItem::factory()->create(['product_id' => $otherProduct->getKey()]);

    // Act
    $component = Livewire::test(ProductItemsRelationManager::class, [
        'ownerRecord' => $supplier,
        'pageClass' => EditSupplier::class,
    ]);

    // Assert
    $component
        ->assertCanSeeTableRecords([$item])
        ->assertCanNotSeeTableRecords([$otherItem]);
});

test('product items relation manager refresh event applies level grade and category filters', function () {
    // Arrange
    $supplier = Supplier::factory()->create();
    $category = ProductCategory::factory()->create();
    $otherCategory = ProductCategory::factory()->create();

    $matchingProduct = Product::factory()->create([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
        'level' => LevelEnum::ELEMENTARY,
        'grade' => GradeEnum::GRADE_1,
    ]);

    $differentGradeProduct = Product::factory()->create([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $category->getKey(),
        'level' => LevelEnum::ELEMENTARY,
        'grade' => GradeEnum::GRADE_2,
    ]);

    $differentCategoryProduct = Product::factory()->create([
        'supplier_id' => $supplier->getKey(),
        'product_category_id' => $otherCategory->getKey(),
        'level' => LevelEnum::ELEMENTARY,
        'grade' => GradeEnum::GRADE_1,
    ]);

    $matchingItem = ProductItem::factory()->create(['product_id' => $matchingProduct->getKey()]);
    $differentGradeItem = ProductItem::factory()->create(['product_id' => $differentGradeProduct->getKey()]);
    $differentCategoryItem = ProductItem::factory()->create(['product_id' => $differentCategoryProduct->getKey()]);

    // Act
    $component = Livewire::test(ProductItemsRelationManager::class, [
        'ownerRecord' => $supplier,
        'pageClass' => EditSupplier::class,
    ])
        ->call('refreshProductItemRelationManagerTable', [
            'level' => LevelEnum::ELEMENTARY->value,
            'grade' => GradeEnum::GRADE_1->value,
            'product_category_id' => $category->getKey(),
        ]);

    // Assert
    $component
        ->assertSet('tableDeferredFilters.level.value', LevelEnum::ELEMENTARY->value)
        ->assertSet('tableDeferredFilters.grade.value', GradeEnum::GRADE_1->value)
        ->assertSet('tableDeferredFilters.category.value', $category->getKey())
        ->assertCanSeeTableRecords([$matchingItem])
        ->assertCanNotSeeTableRecords([$differentGradeItem, $differentCategoryItem]);
});
