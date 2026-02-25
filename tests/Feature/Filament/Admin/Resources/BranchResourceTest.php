<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\Branches\BranchResource;
use App\Filament\Admin\Resources\Branches\Pages\ManageBranches;
use App\Models\Branch;
use Livewire\Livewire;

beforeEach(fn () => $this->loginAdmin());

test('list page is accessible', function () {
    // Act & Assert
    $this->get(BranchResource::getUrl())->assertOk();
});

test('list page renders columns', function (string $column) {
    // Arrange
    Branch::factory()->create();

    // Act & Assert
    Livewire::test(ManageBranches::class)
        ->assertCanRenderTableColumn($column);
})->with([
    'name',
    'whatsapp',
    'phone',
]);

test('list page renders description for name column', function () {
    // Arrange
    $branch = Branch::factory()->create();

    // Act & Assert
    Livewire::test(ManageBranches::class)
        ->assertTableColumnHasDescription('name', $branch->address, $branch);
});

test('list page shows rows', function () {
    // Arrange
    $records = Branch::factory(3)->create();

    // Act & Assert
    Livewire::test(ManageBranches::class)
        ->assertCanSeeTableRecords($records);
});
