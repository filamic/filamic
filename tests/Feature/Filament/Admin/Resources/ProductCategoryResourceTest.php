<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\ProductCategories\Pages\ManageProductCategories;
use App\Filament\Admin\Resources\ProductCategories\ProductCategoryResource;
use App\Models\ProductCategory;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(fn () => $this->loginAdmin());

test('manage page is accessible', function () {
    // Act & Assert
    $this->get(ProductCategoryResource::getUrl())->assertOk();
});

test('manage page renders columns', function (string $column) {
    // Arrange
    ProductCategory::factory()->create();

    // Act & Assert
    Livewire::test(ManageProductCategories::class)
        ->assertCanRenderTableColumn($column);
})->with([
    'name',
    'code',
    'variations.name',
    'variationOptions.name',
]);

test('manage page shows rows', function () {
    // Arrange
    $records = ProductCategory::factory(3)->create();

    // Act & Assert
    Livewire::test(ManageProductCategories::class)
        ->assertCanSeeTableRecords($records);
});

test('can search for records on manage page', function (string $attribute) {
    // Arrange
    $record = ProductCategory::factory()->create();

    // Act & Assert
    Livewire::test(ManageProductCategories::class)
        ->searchTable(data_get($record, $attribute))
        ->assertCanSeeTableRecords([$record]);
})->with([
    'name',
    'code',
]);

test('cannot create a record without required fields', function () {
    // Act & Assert
    Livewire::test(ManageProductCategories::class)
        ->mountAction('create')
        ->fillForm([
            'name' => null,
            'code' => null,
        ])
        ->callMountedAction()
        ->assertHasFormErrors([
            'name' => 'required',
            'code' => 'required',
        ]);
});

test('cannot create a record with duplicate code', function () {
    // Arrange
    ProductCategory::factory()->create(['code' => 'SRG']);

    // Act & Assert
    Livewire::test(ManageProductCategories::class)
        ->mountAction('create')
        ->fillForm([
            'name' => 'Seragam Baru',
            'code' => 'SRG',
            'variations' => [],
        ])
        ->callMountedAction()
        ->assertHasFormErrors(['code' => 'unique']);
});

test('can create a record', function () {
    // Act
    Livewire::test(ManageProductCategories::class)
        ->mountAction('create')
        ->fillForm([
            'name' => 'Seragam',
            'code' => 'SRG',
            'description' => 'Kategori seragam sekolah',
            'variations' => [],
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    // Assert
    expect(ProductCategory::first())
        ->name->toBe('Seragam')
        ->code->toBe('SRG')
        ->description->toBe('Kategori seragam sekolah');
});

test('can create a record with duplicate name', function () {
    // Arrange
    ProductCategory::factory()->create(['name' => 'Seragam']);

    // Act
    Livewire::test(ManageProductCategories::class)
        ->mountAction('create')
        ->fillForm([
            'name' => 'Seragam',
            'code' => 'SRG2',
            'variations' => [],
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    // Assert
    expect(ProductCategory::where('name', 'Seragam')->count())->toBe(2);
});

test('manage page rows have edit action', function () {
    // Arrange
    $record = ProductCategory::factory()->create();

    // Act & Assert
    Livewire::test(ManageProductCategories::class)
        ->assertActionVisible(TestAction::make('edit')->table($record));
});

test('cannot edit a record with empty required fields', function () {
    // Arrange
    $record = ProductCategory::factory()->create();

    // Act & Assert
    Livewire::test(ManageProductCategories::class)
        ->mountAction(TestAction::make('edit')->table($record))
        ->fillForm([
            'name' => null,
            'code' => null,
        ])
        ->callMountedAction()
        ->assertHasFormErrors([
            'name' => 'required',
            'code' => 'required',
        ]);
});

test('cannot edit a record with duplicate code', function () {
    // Arrange
    ProductCategory::factory()->create(['code' => 'SRG']);
    $record = ProductCategory::factory()->create(['code' => 'BK']);

    // Act & Assert
    Livewire::test(ManageProductCategories::class)
        ->mountAction(TestAction::make('edit')->table($record))
        ->fillForm([
            'code' => 'SRG',
        ])
        ->callMountedAction()
        ->assertHasFormErrors(['code' => 'unique']);
});

test('can edit a record', function () {
    // Arrange
    $record = ProductCategory::factory()->create();

    // Act
    Livewire::test(ManageProductCategories::class)
        ->mountAction(TestAction::make('edit')->table($record))
        ->fillForm([
            'name' => 'Updated Name',
            'code' => 'UPD',
            'description' => 'Updated description',
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    // Assert
    expect($record->refresh())
        ->name->toBe('Updated Name')
        ->code->toBe('UPD')
        ->description->toBe('Updated description');
});

test('can edit a record without changes', function () {
    // Arrange
    $record = ProductCategory::factory()->create();

    // Act & Assert
    Livewire::test(ManageProductCategories::class)
        ->mountAction(TestAction::make('edit')->table($record))
        ->callMountedAction()
        ->assertHasNoFormErrors();
});
