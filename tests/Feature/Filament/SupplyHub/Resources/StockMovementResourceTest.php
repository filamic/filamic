<?php

declare(strict_types=1);

use App\Enums\StockMovementTypeEnum;
use App\Filament\SupplyHub\Resources\StockMovements\Pages\CreateStockMovement;
use App\Filament\SupplyHub\Resources\StockMovements\Pages\ListStockMovements;
use App\Filament\SupplyHub\Resources\StockMovements\StockMovementResource;
use App\Models\Branch;
use App\Models\ProductItem;
use App\Models\ProductStock;
use App\Models\ProductStockMovement;
use Livewire\Livewire;

beforeEach(fn () => $this->loginSupplyHub());

test('list page is accessible', function () {
    $this->get(StockMovementResource::getUrl())->assertOk();
});

test('list page renders columns', function (string $column) {
    // Arrange
    ProductStockMovement::factory()->create([
        'branch_id' => filament()->getTenant()->getKey(),
    ]);

    // Act & Assert
    Livewire::test(ListStockMovements::class)
        ->assertCanRenderTableColumn($column);
})->with([
    'item.product.name',
    'transaction_date',
    'item.variationOptions.formatted_name',
    'quantity',
    'destination_branch',
    'user.name',
]);

test('list page shows rows for current branch', function () {
    // Arrange
    $branch = filament()->getTenant();
    $records = ProductStockMovement::factory(3)->create([
        'branch_id' => $branch->getKey(),
    ]);

    // Act & Assert
    Livewire::test(ListStockMovements::class)
        ->assertCanSeeTableRecords($records);
});

test('list page does not show movements from other branches', function () {
    // Arrange
    $currentBranch = filament()->getTenant();
    $otherBranch = Branch::factory()->create();

    $ownMovement = ProductStockMovement::factory()->create([
        'branch_id' => $currentBranch->getKey(),
    ]);

    // Create item outside withoutEvents so Product fingerprint event fires
    $otherItem = ProductItem::factory()->create();

    // Bypass Filament's creating observer that overrides branch_id
    $otherMovement = ProductStockMovement::withoutEvents(fn () => ProductStockMovement::factory()->create([
        'product_item_id' => $otherItem->getKey(),
        'branch_id' => $otherBranch->getKey(),
    ]));

    // Act
    $visibleRecords = StockMovementResource::getEloquentQuery()->pluck('id');

    // Assert
    expect($visibleRecords)
        ->toContain($ownMovement->getKey())
        ->not->toContain($otherMovement->getKey());
});

test('create page is accessible', function () {
    $this->get(StockMovementResource::getUrl('create'))->assertOk();
});

test('cannot create a movement without required fields', function () {
    Livewire::test(CreateStockMovement::class)
        ->call('create')
        ->assertHasFormErrors([
            'product_item_id' => 'required',
            'type' => 'required',
            'quantity' => 'required',
            // 'purchase_price' => 'required',
            // 'sale_price' => 'required',
        ]);
});

test('can create a STOCK_IN movement', function () {
    // Arrange
    $item = ProductItem::factory()->create();
    $transactionDate = now()->subDay()->toDateString();

    // Act
    Livewire::test(CreateStockMovement::class)
        ->fillForm([
            'product_item_id' => $item->getKey(),
            'type' => StockMovementTypeEnum::STOCK_IN->value,
            'transaction_date' => $transactionDate,
            'quantity' => 50,
            // 'purchase_price' => 25000,
            // 'sale_price' => 40000,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    // Assert
    expect(ProductStockMovement::count())->toBe(1);

    $movement = ProductStockMovement::first();
    expect($movement)
        ->type->toBe(StockMovementTypeEnum::STOCK_IN)
        ->quantity->toBe(50)
        ->and($movement->transaction_date?->toDateString())->toBe($transactionDate);

    $stock = ProductStock::where('product_item_id', $item->getKey())->first();
    expect($stock)->not->toBeNull()
        ->quantity->toBe(50);
});

test('shows stock validation on quantity field when distribution exceeds available stock', function () {
    // Arrange
    $item = ProductItem::factory()->create();

    // Act & Assert
    Livewire::test(CreateStockMovement::class)
        ->fillForm([
            'product_item_id' => $item->getKey(),
            'type' => StockMovementTypeEnum::DISTRIBUTION->value,
            'quantity' => 1,
            // 'purchase_price' => 25000,
            // 'sale_price' => 40000,
        ])
        ->call('create')
        ->assertHasFormErrors(['quantity']);

    expect(ProductStockMovement::count())->toBe(0);
});
